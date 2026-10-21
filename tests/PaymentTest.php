<?php

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client as MockClient;
use Omnipay\Ameria\Gateway;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Http\Client;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    private $http;
    private $gateway;

    protected function setUp(): void
    {
        $this->http = new MockClient();
        $this->gateway = new Gateway(new Client($this->http));
        $this->gateway->initialize([
            'client_id' => 'merchant', 'username' => 'regular', 'password' => 'secret',
            'bindingUsername' => 'binding', 'testMode' => true,
        ]);
    }

    private function options(): array
    {
        return ['transactionId' => '123', 'amount' => '10.25', 'currency' => 'AMD',
            'description' => 'Order 123', 'returnUrl' => 'https://example.com/back',
            'card_holder_id' => 'holder'];
    }

    private function respond(array $data): void
    {
        $this->http->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode($data)));
    }

    public function testPurchasePreservesDecimalAmountAndTestMode(): void
    {
        $this->respond(['PaymentID' => 'bank-id', 'ResponseCode' => 1]);
        $response = $this->gateway->purchase($this->options())->send();
        $request = $this->http->getLastRequest();
        $body = json_decode((string) $request->getBody(), true);
        $this->assertEquals(10.25, $body['Amount']);
        $this->assertSame('051', $body['Currency']);
        $this->assertSame('123', (string) $body['OrderID']);
        $this->assertStringContainsString('servicestest.ameriabank.am', (string) $request->getUri());
        $this->assertSame('bank-id', $response->getTransactionReference());
        $this->assertTrue($response->isRedirect());
        $this->assertFalse($response->isSuccessful());
        $this->assertStringContainsString('servicestest.ameriabank.am', $response->getRedirectUrl());
    }

    public function testLiveRedirectUsesLiveHost(): void
    {
        $this->respond(['PaymentID' => 'bank-id', 'ResponseCode' => 1]);
        $response = $this->gateway->purchase(array_merge($this->options(), ['testMode' => false]))->send();
        $this->assertStringContainsString('https://services.ameriabank.am/', $response->getRedirectUrl());
    }

    /** @dataProvider failedInitializations */
    public function testFailedInitializationCannotRedirect(array $data): void
    {
        $this->respond($data);
        $response = $this->gateway->purchase($this->options())->send();
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getRedirectUrl());
    }

    public static function failedInitializations(): array
    {
        return [[['ResponseCode' => -1]], [['ResponseCode' => 1]],
            [['ResponseCode' => 1, 'PaymentID' => '']], [['ResponseCode' => -1, 'PaymentID' => 'bank-id']]];
    }

    /** @dataProvider followUpOperations */
    public function testFollowUpUsesBankReferenceAndCredentials(string $method, string $endpoint): void
    {
        $this->respond(['ResponseCode' => '00', 'OrderStatus' => '2', 'OrderID' => '123']);
        $response = $this->gateway->$method(['transactionReference' => 'bank-id', 'amount' => '10.25', 'currency' => 'AMD'])->send();
        $request = $this->http->getLastRequest();
        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('bank-id', $body['PaymentID']);
        $this->assertSame('regular', $body['Username']);
        $this->assertSame('secret', $body['Password']);
        $this->assertStringEndsWith('/'.$endpoint, (string) $request->getUri());
        $this->assertTrue($response->isSuccessful());
        if ($method === 'completePurchase') {
            $this->assertSame('bank-id', $response->getTransactionReference());
            $this->assertSame('123', $response->getTransactionId());
        }
    }

    public static function followUpOperations(): array
    {
        return [['capture', 'ConfirmPayment'], ['refund', 'RefundPayment'],
            ['void', 'CancelPayment'], ['completePurchase', 'GetPaymentDetails']];
    }

    /** @dataProvider followUpOperations */
    public function testDeclinedFollowUpIsNotSuccessful(string $method, string $endpoint): void
    {
        $this->respond(['ResponseCode' => '05', 'ResponseMessage' => 'Declined', 'OrderStatus' => '2']);
        $response = $this->gateway->$method(['transactionReference' => 'bank-id', 'amount' => '5.00'])->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertSame('05', $response->getCode());
        $this->assertSame('Declined', $response->getMessage());
        $this->assertStringEndsWith('/'.$endpoint, (string) $this->http->getLastRequest()->getUri());
    }

    public function testLegacyPaymentIdAndReferencePrecedence(): void
    {
        $this->assertSame('legacy', $this->gateway->refund(['transactionId' => 'legacy', 'amount' => '1'])->getData()['PaymentID']);
        $this->assertSame('bank-id', $this->gateway->refund(['transactionId' => '123', 'transactionReference' => 'bank-id', 'amount' => '1'])->getData()['PaymentID']);
    }

    public function testMissingBankReferenceIsRejected(): void
    {
        $this->expectException(InvalidRequestException::class);
        $this->gateway->void()->getData();
    }

    /** @dataProvider paymentStates */
    public function testPaymentStateMustAgreeWithResponseCode(string $code, string $status, bool $paid, bool $authorized): void
    {
        $this->respond(['ResponseCode' => $code, 'OrderStatus' => $status]);
        $response = $this->gateway->completePurchase(['transactionReference' => 'bank-id'])->send();
        $this->assertSame($paid, $response->isSuccessful());
        $this->assertSame($authorized, $response->isAuthorized());
    }

    public static function paymentStates(): array
    {
        return [['00', '2', true, false], ['00', '1', false, true], ['00', '0', false, false],
            ['00', '3', false, false], ['00', '4', false, false], ['05', '2', false, false], ['05', '1', false, false]];
    }

    public function testBindingPaymentSupportsThreeDSecurePost(): void
    {
        $this->respond(['PaymentID' => 'bank-id', 'OrderID' => '123', 'ResponseCode' => '00', 'OrderStatus' => '0',
            'AcsUrl' => 'https://issuer.example/acs', 'PaReq' => 'challenge', 'TermUrl' => 'https://bank.example/term']);
        $response = $this->gateway->createCard($this->options())->send();
        $body = json_decode((string) $this->http->getLastRequest()->getBody(), true);
        $this->assertEquals(10.25, $body['Amount']);
        $this->assertSame('binding', $body['Username']);
        $this->assertSame('bank-id', $response->getTransactionReference());
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('POST', $response->getRedirectMethod());
        $this->assertSame('https://issuer.example/acs', $response->getRedirectUrl());
        $this->assertSame(['PaReq' => 'challenge', 'TermUrl' => 'https://bank.example/term'], $response->getRedirectData());
    }

    public function testBindingOperationsUseBindingUsername(): void
    {
        foreach (['getBindings', 'enableCard', 'disableCard'] as $method) {
            $data = $this->gateway->$method(['card_holder_id' => 'holder'])->getData();
            $this->assertSame('binding', $data['Username']);
            $this->assertSame(6, $data['PaymentType']);
        }
        $this->respond(['ResponseCode' => '00', 'CardBindingFileds' => [['CardHolderID' => 'holder']]]);
        $response = $this->gateway->getBindings()->send();
        $this->assertSame([['CardHolderID' => 'holder']], $response->getCardBindings());
    }

    /** @dataProvider currencies */
    public function testAmountIsConsistentAcrossMoneyOperations(string $currency, string $amount): void
    {
        foreach (['purchase', 'authorize', 'createCard', 'capture', 'refund'] as $method) {
            $data = $this->gateway->$method(array_merge($this->options(), ['currency' => $currency, 'amount' => $amount]))->getData();
            $this->assertSame($amount, $data['Amount']);
        }
    }

    public static function currencies(): array
    {
        return [['AMD', '10.25'], ['USD', '0.01'], ['EUR', '999.99']];
    }

    /** @dataProvider invalidAmounts */
    public function testInvalidAmountCannotReachTheBank(string $amount): void
    {
        $this->expectException(InvalidRequestException::class);
        try {
            $this->gateway->purchase(array_merge($this->options(), ['amount' => $amount]))->send();
        } finally {
            $this->assertCount(0, $this->http->getRequests());
        }
    }

    public static function invalidAmounts(): array
    {
        return [['-1'], ['1.001']];
    }

    public function testCaptureRequiresCredentialsBeforeSending(): void
    {
        $this->gateway->setPassword(null);
        $this->expectException(InvalidRequestException::class);
        try {
            $this->gateway->capture(['transactionReference' => 'bank-id', 'amount' => '1'])->send();
        } finally {
            $this->assertCount(0, $this->http->getRequests());
        }
    }

    public function testMissingCardHolderIsAnInvalidRequest(): void
    {
        $this->expectException(InvalidRequestException::class);
        $this->gateway->enableCard()->getData();
    }

    public function testInitCanRegisterCardHolderAndPassExplicitServiceType(): void
    {
        $data = $this->gateway->purchase(array_merge($this->options(), ['paymentServiceType' => 0]))->getData();
        $this->assertSame('holder', $data['CardHolderID']);
        $this->assertSame(0, $data['PaymentServiceType']);
    }

    public function testAuthorizedPaymentCanBeCheckedBeforeCapture(): void
    {
        $this->respond(['ResponseCode' => '00', 'OrderStatus' => '1']);
        $response = $this->gateway->completeAuthorize(['transactionReference' => 'bank-id'])->send();
        $this->assertTrue($response->isAuthorized());
        $this->assertFalse($response->isSuccessful());
    }

    /** @dataProvider bindingResponses */
    public function testBindingPaymentCompletionAndIncompleteChallenges(array $data, bool $successful): void
    {
        $this->respond($data);
        $response = $this->gateway->createCard($this->options())->send();
        $this->assertSame($successful, $response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getRedirectUrl());
        $this->assertSame([], $response->getRedirectData());
    }

    public static function bindingResponses(): array
    {
        return [[['PaymentID' => 'bank-id', 'ResponseCode' => '00', 'OrderStatus' => '2'], true],
            [['ResponseCode' => '05', 'OrderStatus' => '0'], false],
            [['ResponseCode' => '00', 'OrderStatus' => '0', 'AcsUrl' => 'https://issuer.example/acs'], false]];
    }

    /** @dataProvider invalidResponses */
    public function testMalformedResponseIsRejected(string $body, string $exception): void
    {
        $this->http->addResponse(new Response(200, [], $body));
        $this->expectException($exception);
        $this->gateway->purchase($this->options())->send();
    }

    public static function invalidResponses(): array
    {
        return [['<html>upstream error</html>', JsonException::class], ['null', UnexpectedValueException::class]];
    }

    public function testInvalidUtf8CannotSendAnEmptyPayment(): void
    {
        $this->expectException(JsonException::class);
        try {
            $this->gateway->purchase(array_merge($this->options(), ['description' => "\xB1\x31"]))->send();
        } finally {
            $this->assertCount(0, $this->http->getRequests());
        }
    }

    /** @dataProvider followUpOperations */
    public function testFollowUpDoesNotRequireClientId(string $method, string $endpoint): void
    {
        $this->gateway->setClientId(null);
        $this->respond(['ResponseCode' => '00', 'OrderStatus' => '2']);
        $response = $this->gateway->$method(['transactionReference' => 'bank-id', 'amount' => '1'])->send();
        $data = json_decode((string) $this->http->getLastRequest()->getBody(), true);
        $this->assertArrayNotHasKey('ClientID', $data);
        $this->assertTrue($response->isSuccessful());
        $this->assertStringEndsWith('/'.$endpoint, (string) $this->http->getLastRequest()->getUri());
    }

    public function testExistingBindingCanChargeWithoutReturnUrl(): void
    {
        $options = $this->options();
        unset($options['returnUrl']);
        $this->respond(['PaymentID' => 'bank-id', 'ResponseCode' => '00', 'OrderStatus' => '2']);
        $this->assertTrue($this->gateway->createCard($options)->send()->isSuccessful());
    }

    public function testCommonResponsesCanSafelyReportCompletion(): void
    {
        $this->respond(['ResponseCode' => '00']);
        $response = $this->gateway->refund(['transactionReference' => 'bank-id', 'amount' => '1'])->send();
        $this->assertFalse($response->isCompleted());
    }
}

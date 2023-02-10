<?php

namespace Omnipay\Ameria\Message\Request;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Ameria\Message\Response\InitPaymentResponse;

/**
 * Class RegisterRequest
 *
 * @package Omnipay\Ameria\Message
 */
class InitPaymentRequest extends AbstractRequest
{
    /**
     * Get the request sessionTimeoutSecs.
     */
    public function getTimeout(): int
    {
        return $this->getParameter('sessionTimeoutSecs');
    }

    /**
     * Set the request sessionTimeoutSecs.
     *
     * @param  int  $value  default is 1200 Second (20minute)
     *
     * @return $this
     */
    public function setTimeout(int $value): AbstractRequest
    {
        return $this->setParameter('sessionTimeoutSecs', $value);
    }

    /**
     * Prepare data to send
     *
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('transactionId', 'amount', 'returnUrl');

        $data = parent::getData();
        if ($this->getCurrency()) {
            $data['Currency'] = str_pad($this->getCurrencyNumeric(), 3, 0, STR_PAD_LEFT);
        }
        $data['Description'] = $this->getDescription();
        $data['OrderId']     = $this->getTransactionId();
        $data['Amount']      = $this->getAmountInteger();
        if ($this->getReturnUrl()) {
            $data['BackURL'] = $this->getReturnUrl();
        }
        if ($this->getJsonParams()) {
            $data['Opaque'] = $this->getJsonParams();
//        $data['Opaque'] = json_encode(["FORCE_3DS2" => true]);
        }
        if ($this->getCardHolderId()) {
            $data['CardHolderID'] = $this->getCardHolderId();
        }
        if ($this->getTimeout()) {
            $data['Timeout'] = $this->getTimeout();
        }
        if ($this->getLanguage()) {
            $data['language'] = $this->getLanguage();
        }

        return $data;
    }

    public function getEndpoint(): string
    {
        return $this->getUrl().'/InitPayment';
    }

    protected function createResponse(string $data, array $headers = []): ResponseInterface
    {
        return $this->response = new InitPaymentResponse($this, $data, $headers);
    }
}

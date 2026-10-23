# Omnipay: Ameriabank

[![Tests](https://github.com/arm092/omnipay-ameriabank/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/arm092/omnipay-ameriabank/actions/workflows/tests.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/arm092/omnipay-ameriabank)](https://packagist.org/packages/arm092/omnipay-ameriabank)
[![Total Downloads](https://img.shields.io/packagist/dt/arm092/omnipay-ameriabank)](https://packagist.org/packages/arm092/omnipay-ameriabank/stats)
[![PHP Version](https://img.shields.io/packagist/dependency-v/arm092/omnipay-ameriabank/php)](https://packagist.org/packages/arm092/omnipay-ameriabank)
[![License](https://img.shields.io/packagist/l/arm092/omnipay-ameriabank)](https://packagist.org/packages/arm092/omnipay-ameriabank)

Ameriabank VPOS gateway for Omnipay, compatible with PHP 7.4 and PHP 8.

## Installation

Install the gateway; the Guzzle HTTP adapter is included as a dependency:

```sh
composer require arm092/omnipay-ameriabank:^2.0
```

## Register a payment and redirect

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Ameria');
$gateway->setClientId(env('AMERIA_CLIENT_ID'));
$gateway->setUsername(env('AMERIA_USERNAME'));
$gateway->setPassword(env('AMERIA_PASSWORD'));
$gateway->setTestMode(true); // false for production; use the same mode on completion

$response = $gateway->purchase([
    'transactionId' => '123', // Your unique numeric order ID
    'amount' => '10.25',     // Major currency units, never multiply by 100
    'currency' => 'AMD',
    'description' => 'Order 123',
    'returnUrl' => env('AMERIA_RETURN_URL'),
    'opaque' => json_encode(['order_id' => 123]),
])->send();

if ($response->isRedirect()) {
    // Persist this bank ID against your local order before redirecting.
    $paymentId = $response->getTransactionReference();
    $response->setLanguage('en'); // am, ru, en
    $response->redirect();
} else {
    // Registration failed. Inspect getCode() and getMessage().
}
```

A successful `InitPayment` only registers an order. Its `isSuccessful()` is false;
use `isRedirect()` to proceed. The redirect automatically inherits the request's
test mode. `setTestMode()` on the response remains available for existing callers.

## Verify payment after the browser returns

`BackURL` is a browser return URL, not a trusted server webhook. Do not mark an
order paid based on the callback's `resposneCode`, `orderID`, or `opaque` values.
Find the local order using its stored bank PaymentID and query the bank:

```php
// $gateway must have the same credentials and test mode as registration.
// $storedPaymentId is the PaymentID saved for the local order.
$response = $gateway->completePurchase([
    'transactionReference' => $storedPaymentId,
])->send();

if ($response->isSuccessful()) {
    // OrderStatus=2 and ResponseCode=00.
    // Match OrderID, Amount and Currency from getData() to the stored order.
    // Mark the order paid idempotently in your application.
}
```

`getTransactionReference()` returns the bank PaymentID. `getTransactionId()` on
a details response returns the merchant OrderID. GetPaymentDetails does not return
PaymentID, so the response retains the ID from its request.

## Capture, cancel and refund

```php
$capture = $gateway->capture([
    'transactionReference' => $storedPaymentId,
    'amount' => '10.25', 'currency' => 'AMD',
])->send();

$refund = $gateway->refund([
    'transactionReference' => $storedPaymentId,
    'amount' => '5.00', 'currency' => 'AMD', // Partial refund in major units
])->send();

$cancel = $gateway->void(['transactionReference' => $storedPaymentId])->send();
```

Check each response's `isSuccessful()`, `getCode()` and `getMessage()`. For existing
integrations, these follow-up requests still accept PaymentID through
`transactionId`; `transactionReference` takes precedence when both are supplied.

`authorize()` registers through InitPayment just like `purchase()`. Preauthorization
must be enabled/configured for the terminal by Ameriabank; this method alone does
not switch a single-stage terminal into two-stage mode. After return, use
`completeAuthorize()` and check `isAuthorized()` (ResponseCode=00, OrderStatus=1),
then `capture()` to deposit the amount. Authorization is not completed payment,
so `isSuccessful()` remains false until OrderStatus=2. The API exposes an optional
integer `paymentServiceType` on InitPayment, but its values are not explained on
the Help page. Pass it only according to your bank-issued integration settings;
the gateway does not guess a value for preauthorization.

## Card bindings

To register a card binding through the hosted payment form, add a stable
`card_holder_id` to `purchase()` (InitPayment). Keep the same ID for subsequent
binding operations.

The historical `createCard()` method calls MakeBindingPayment: it **charges an
existing binding**, rather than simply creating a card. Supply `card_holder_id`,
`transactionId`, `amount`, `currency` and `description` as above. Supply `returnUrl`
when authentication can require returning through the browser; it remains optional
for existing bindings that charge without an interactive redirect.
If the response `isRedirect()`, perform its redirect to complete 3-D Secure;
the gateway supplies a POST with PaReq and TermUrl from the bank. If supplied,
MD is forwarded unchanged. Otherwise check `isSuccessful()`. Always query
GetPaymentDetails after authentication before marking the local order paid.

`setBindingUsername()` configures the username for `createCard()`, `getBindings()`,
`enableCard()` and `disableCard()`; if omitted, the normal username is used.
Enable/disable requests require `card_holder_id`. `getBindings()->send()` exposes
`getCardBindings()`. The spelling `CardBindingFileds` is intentional in the bank API.

## Compatibility changes

- Payment and binding amounts now use major currency units consistently with
  capture/refund. Remove any application workaround for the previous 100x scaling.
- InitPayment is a redirect response, not proof of payment. Replace old checks
  of its `isSuccessful()` (or the README typo `isSuccessfull()`) with `isRedirect()`.
- Bank references and merchant order IDs are separate. Update consumers that
  previously read merchant OrderID through `getTransactionReference()`.
- Malformed JSON responses and unencodable request data throw exceptions rather
  than silently yielding an empty request or an unusable response.

## Tests

```sh
composer install
composer test
composer validate --strict
composer audit --locked
```

Tests use an HTTP mock and never contact payment endpoints. The lock file resolves
dependencies for PHP 7.4.33, the lowest supported PHP line; the same suite can run
on PHP 8. Production credentials, bank-enabled terminal settings and real 3-D
Secure flows still need validation in the bank sandbox before release.

[Ameriabank API documentation](https://servicestest.ameriabank.am/VPOS/help)

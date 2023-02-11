# Omnipay: AmeriaBank

**AmeriaBank driver for the Omnipay Laravel payment processing library**

[![Latest Stable Version](https://poser.pugx.org/arm092/omnipay-ameriabank/version.png)](https://packagist.org/packages/arm092/omnipay-ameriabank)
[![Total Downloads](https://poser.pugx.org/arm092/omnipay-ameriabank/d/total.png)](https://packagist.org/packages/arm092/omnipay-ameriabank)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework-agnostic, multi-gateway payment
processing library for PHP 5.5+. This package implements Arca support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "arm092/omnipay-ameriabank": "~1.0"
    }
}
```

And run composer to update your dependencies:

    composer update

Or you can simply run

    composer require arm092/omnipay-ameriabank

## Basic Usage

1. Use Omnipay gateway class:

```php
    use Omnipay\Omnipay;
```

2. Initialize Ameria gateway:

```php

    $gateway = Omnipay::create('Ameria');
    $gateway->setClientId(env('AMERIA_CLIENT_ID')); // Merchant ID - Provided by bank
    $gateway->setUsername(env('AMERIA_USERNAME')); // Username - Provided by bank
    $gateway->setPassword(env('AMERIA_PASSWORD')); // Password - Provided by bank
    $purchase = $gateway->purchase(); // Creating purchase request
    $purchase->setReturnUrl(env('AMERIA_RETURN_URL')); // Return url, that should be point to your arca webhook route
    $purchase->setAmount(10); // Amount to charge - should be decimal
    $purchase->setTransactionId(XXXX); // Transaction ID from your system
    $purchase->setTestMode(true); // For enabling test mode
    $purchase->setOpaque(json_encode(['email' => 'user@example.com'])); // Is not mandatory field and used as additional information during information exchange 

```

3. Call purchase, it will automatically redirect to AmeriaBank's hosted page

```php

    $purchaseResponse = $purchase->send();
    if ($purchaseResponse->isSuccessfull()) {
        $purchaseResponse->setLanguage(\App::getLocale()); // Interface language ('am', 'ru', 'en')
        $purchaseResponse->setTestMode(true); // For enabling test mode
        $purchaseResponse->redirect();
    }

```

4. Create a webhook controller to handle the back URL request at your `AMERIA_RETURN_URL` and catch the webhook as follows

```php

    $gateway = Omnipay::create('Ameria');
    $gateway->setClientId(env('AMERIA_CLIENT_ID'));
    $gateway->setUsername(env('AMERIA_USERNAME'));
    $gateway->setPassword(env('AMERIA_PASSWORD'));
    
    $purchaseCompleteRequest = $gateway->completePurchase();
    $purchaseCompleteRequest->setTransactionId(request()->get('paymentID'));
    $purchaseCompleteResponse = $purchaseCompleteRequest->send();
    
    // Do the rest with $purchase and response with 'OK'
    if ($purchaseCompleteResponse->isSuccessful()) {
        
        // Your logic
        
    }
    
    return new Response('OK');

```

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/arm092/omnipay-ameria/issues),
or better yet, fork the library and submit a pull request.

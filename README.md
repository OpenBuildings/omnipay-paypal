Omnipay: Paypal
============

**Paypal REST driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/clippings/omnipay-paypal.png?branch=master)](https://travis-ci.org/clippings/omnipay-paypal)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/clippings/omnipay-paypal/badges/quality-score.png)](https://scrutinizer-ci.com/g/clippings/omnipay-paypal/)
[![Code Coverage](https://scrutinizer-ci.com/g/clippings/omnipay-paypal/badges/coverage.png)](https://scrutinizer-ci.com/g/clippings/omnipay-paypal/)
[![Latest Stable Version](https://poser.pugx.org/clippings/omnipay-paypal/v/stable.png)](https://packagist.org/packages/clippings/omnipay-paypal)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements eMerchantPay support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "clippings/omnipay-paypal": "~0.1"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* PaypalRest

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

In order to use this gateway, you need to provide apiKey and clientId.

```php
$gateway = Omnipay::create('PaypalRest');
$gateway->setClientId('abc123');
$gateway->setSecret('abc123');
```

For a successful purchase you need to provide  ``amount``,``currency``:

```php
$purchase = $gateway->purchase(array(
    'currency' => 'GBP',
    'amount' => '15.00',
    'description' => 'This is a purchase',
));
```

Funding methods
---------------

You can use 3 different methods for paying for purchases

__Paypal Redirect__

If you don ot provide any payment methods, purchase() method will generate a redirect response.

```php
$purchase = $gateway->purchase(array(
    'currency' => 'GBP',
    'amount' => '15.00',
    'description' => 'This is a purchase',
    'redirectUrl' => 'http://example.com/completed',
    'cancelUrl' => 'http://example.com/cancel',
));

$response = $purchase->send();

// redirect to $response->getRedirectUrl()
$response->redirect();
$key = $response->getTransactionReference();

// You'll need to pass $key as well as "payerid" query parameter that you'll get from paypal redirecting back to your site

$completePurchase = $gateway->completePurchase(array(
    'purchaseId' => $key,
    'payerId' => $_GET['PAYERID'],
));

$response2 = $completePurchase->send();
```

__Credit card reference__

You can store user's credit cards within paypal's vault, and use a reference to that for purchases.

```php
$createCard = $gateway->createCard(array(
    'card' => ...,
    'payerId' => 'payer id',
));

$response = $createCard->send();
$cardId = $response->getTransactionReference();

$purchase = $gateway->purchase(array(
    'currency' => 'GBP',
    'amount' => '15.00',
    'cardReference' => $cardId,
));

$response = $purchase->send();
```

__Directly with a credit card__

```php
$purchase = $gateway->purchase(array(
    'currency' => 'GBP',
    'amount' => '15.00',
    'card' => ...,
));

$response = $purchase->send();
```

Authorization
-------------

Authorisation, capture and void are supported too. It works the same way as purchase, but you'll need to "capture" or "void" afterwords

```php
$authorise = $gateway->authorise(array(
    'currency' => 'GBP',
    'amount' => '15.00',
    'card' => ...,
));

$response = $authorise->send();
$id = $response->getTransactionReference();

$capture = $gateway->capture(array(
    'purchaseId' => $id,
    'amount' => '15.00',
));

$capture->send();

// Or

$capture = $gateway->void(array(
    'purchaseId' => $id,
));

$capture->send();

```

Refund
------

```
$refund = $gateway->refund(array(
    'purchaseId' => $id,
));

$refund->send();

// If you are refunding a capture
$refund = $gateway->refund(array(
    'purchaseId' => $id,
    'type' => 'capture'
));

$refund->send();

// Partial refund
$refund = $gateway->refund(array(
    'purchaseId' => $id,
    'amount' => '10.00'
    'currency' => 'GBP'
));

$refund->send();
```

Credit Card Vault
-----------------

createCard, updateCard and deleteCard are supported too.

```php
$createCard = $gateway->createCard(array(
    'card' => ...,
    'payerId' => '123123' // Optional, if set, will be required when referencing for a purchase later
));

$response = $createCard->send();
$cardReference = $response->getTransactionReference();

$updateCard = $gateway->updateCard(array(
    'card' => ...,
));

$deleteCard = $gateway->deleteCard(array(
    'card' => ...,
));
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/clippings/omnipay-emp/issues),
or better yet, fork the library and submit a pull request.

License
-------

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin

Under BSD-3-Clause license, read LICENSE file.

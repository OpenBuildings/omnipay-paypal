<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Omnipay;
use Omnipay\PaypalRest\Message\PaymentRequest;
use Omnipay\PaypalRest\Message\RefundRequest;
use Omnipay\PaypalRest\Message\PaymentCompleteRequest;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\PurchaseRequest
 */
class PurchaseMockTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testPaypalRequest()
    {
        $this->setMockHttpResponse('PurchasePaypalSuccess.http');

        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'intent' => 'sale',
            'amount' => '15.00',
            'currency' => 'GBP',
            'returnUrl' => 'http://clippings.com/completed',
            'cancelUrl' => 'http://clippings.com/cancel',
            'transactionReference' => 'referenceID1',
            'items' => array(
                array(
                    'name' => 10,
                    'price' => '5.00',
                    'description' => 'Product 1 Desc',
                    'quantity' => 2
                ),
                array(
                    'name' => 12,
                    'price' => '5.00',
                    'description' => 'Shipping for Product 1',
                    'quantity' => 1
                ),
            ),
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentApproveResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('PAY-7N5239784Y302191WKQ72KJI', $response->getTransactionReference());
        $this->assertEquals('https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-2PU56195M4518381J', $response->getRedirectUrl());
        $this->assertEquals('GET', $response->getRedirectMethod());
    }

    /**
     * @coversNothing
     */
    public function testPaypalRequestComplete()
    {
        $this->setMockHttpResponse('PurchasePaypalCompleteSuccess.http');

        $request = new PaymentCompleteRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'purchaseId' => 'PAY-7N5239784Y302191WKQ72KJI',
            'payerId' => 'HVMBSS6TABKJN',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('96G836504V833741T', $response->getTransactionReference());
    }

    /**
     * @coversNothing
     */
    public function testCardRequest()
    {
        $this->setMockHttpResponse('PurchaseCreditCardSuccess.http');

        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'intent' => 'sale',
            'amount' => '3.00',
            'currency' => 'USD',
            'card' => $this->getValidCard(),
            'transactionReference' => 'referenceID1',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('92M94738P51857122', $response->getTransactionReference());
    }

    /**
     * @coversNothing
     */
    public function testCardReferenceRequest()
    {
        $this->setMockHttpResponse('PurchaseCardRefereneSuccess.http');

        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'intent' => 'sale',
            'amount' => '3.00',
            'currency' => 'USD',
            'cardReference' => 'CARD-36F38182Y8875171YKRACCUQ',
            'transactionReference' => 'referenceID1',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('71302803XW437763C', $response->getTransactionReference());
    }

    /**
     * @coversNothing
     */
    public function testRefund()
    {
        $this->setMockHttpResponse('PurchaseRefundSuccess.http');

        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'type' => 'sale',
            'purchaseId' => '92M94738P51857122',
            'currency' => 'USD',
            'amount' => '1.50',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\RefundResponse', $response);
        $this->assertTrue($response->isSuccessful());
    }
}

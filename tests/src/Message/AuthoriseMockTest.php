<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Omnipay;
use Omnipay\PaypalRest\Message\PaymentRequest;
use Omnipay\PaypalRest\Message\CaptureRequest;
use Omnipay\PaypalRest\Message\RefundRequest;
use Omnipay\PaypalRest\Message\VoidRequest;
use Omnipay\PaypalRest\Message\PaymentCompleteRequest;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\PurchaseRequest
 */
class AuthoriseMockTest extends TestCase
{
    /**
     * @covers ::getEndpoint
     */
    public function testPaypalRequest()
    {
        $this->setMockHttpResponse('AuthorisePaypalSuccess.http');

        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'intent' => 'authorize',
            'amount' => '15.00',
            'currency' => 'GBP',
            'returnUrl' => 'http://clippings.com/completed',
            'cancelUrl' => 'http://clippings.com/cancel',
            'transactionReference' => 'referenceID1',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentApproveResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('PAY-2LY017912Y929154HKQ74XPY', $response->getTransactionReference());
        $this->assertEquals('https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-5SD087658M435925N', $response->getRedirectUrl());
        $this->assertEquals('GET', $response->getRedirectMethod());
    }

    /**
     * @covers ::getEndpoint
     */
    public function testPaypalRequestComplete()
    {
        $this->setMockHttpResponse('AuthorisePaypalCompleteSuccess.http');

        $request = new PaymentCompleteRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'purchaseId' => 'PAY-2LY017912Y929154HKQ74XPY',
            'payerId' => 'HVMBSS6TABKJN',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('1SN458127W2399139', $response->getTransactionReference());
    }

    public function testCardRequest()
    {
        $this->setMockHttpResponse('AuthoriseCreditCardSuccess.http');

        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'intent' => 'authorize',
            'amount' => '3.00',
            'currency' => 'USD',
            'card' => $this->getValidCard(),
            'transactionReference' => 'referenceID1',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('9HJ30098MP9464308', $response->getTransactionReference());
    }

    public function testCardReferenceRequest()
    {
        $this->setMockHttpResponse('AuthoriseCardReferenceSuccess.http');

        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'intent' => 'authorize',
            'amount' => '3.00',
            'currency' => 'USD',
            'cardReference' => 'CARD-36F38182Y8875171YKRACCUQ',
            'transactionReference' => 'referenceID1',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('1CF71998H9869342R', $response->getTransactionReference());
    }

    public function testCapture()
    {
        $this->setMockHttpResponse('AuthoriseCaptureSuccess.http');

        $request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'purchaseId' => '9HJ30098MP9464308',
            'amount' => '3.00',
            'currency' => 'USD',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\CaptureResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('1D2800957K044141A', $response->getTransactionReference());
    }

    public function testRefund()
    {
        $this->setMockHttpResponse('AuthoriseRefundSuccess.http');

        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'purchaseId' => '1D2800957K044141A',
            'amount' => '3.00',
            'currency' => 'USD',
            'type' => 'capture',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\RefundResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('3KC74088CH688810Y', $response->getTransactionReference());
    }

    public function testVoid()
    {
        $this->setMockHttpResponse('AuthoriseVoidSuccess.http');

        $request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'purchaseId' => '1SN458127W2399139',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\VoidResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('1SN458127W2399139', $response->getTransactionReference());
    }
}

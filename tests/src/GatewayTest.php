<?php

namespace Omnipay\PaypalRest\Test;

use Omnipay\Omnipay;
use Omnipay\PaypalRest\Gateway;
use Omnipay\PaypalRest\Message\TokenResponse;
use Omnipay\PaypalRest\Message\TokenRequest;
use Omnipay\Tests\TestCase;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Gateway
 */
class GatewayTest extends TestCase
{
    private $gateway;
    private $purchaseOptions;
    private $refundOptions;

    /**
     * @covers ::getClientId
     * @covers ::setClientId
     */
    public function testClientId()
    {
        $gateway = Omnipay::create('PaypalRest');

        $this->assertSame($gateway, $gateway->setClientId('safsdfclientid'));
        $this->assertSame('safsdfclientid', $gateway->getClientId());
    }

    /**
     * @covers ::getSecret
     * @covers ::setSecret
     */
    public function testSecret()
    {
        $gateway = Omnipay::create('PaypalRest');

        $this->assertSame($gateway, $gateway->setSecret('safsdfsecret'));
        $this->assertSame('safsdfsecret', $gateway->getSecret());
    }

    /**
     * @covers ::getPartnerAttributionId
     * @covers ::setPartnerAttributionId
     */
    public function testPartnerAttributionId()
    {
        $gateway = Omnipay::create('PaypalRest');

        $this->assertSame($gateway, $gateway->setPartnerAttributionId('safsdfpartner'));
        $this->assertSame('safsdfpartner', $gateway->getPartnerAttributionId());
    }

    /**
     * @covers ::getTokenExpires
     * @covers ::setTokenExpires
     */
    public function testTokenExpires()
    {
        $gateway = Omnipay::create('PaypalRest');

        $this->assertSame($gateway, $gateway->setTokenExpires('safsdfpartner'));
        $this->assertSame('safsdfpartner', $gateway->getTokenExpires());
    }

    /**
     * @covers ::__construct
    */
    public function testErrorHandling()
    {
        $this->setMockHttpResponse('TokenError.http');

        $gateway = Omnipay::create('PaypalRest', $this->getHttpClient());
        $response = $gateway->token()->send();

        $this->assertFalse($response->isSuccessful());
    }

    public function dataHasToken()
    {
        return array(
            array(null, null, false),
            array('sdfasdfa', null, false),
            array('sdfasdfa', time() + 100000, true),
            array('sdfasdfa', time() - 1000, false),
            array(null, time() + 100000, false),
        );
    }

    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $gateway = new Gateway();

        $this->assertSame('PaypalRest', $gateway->getName());
    }

    /**
     * @covers ::hasToken
     * @dataProvider dataHasToken
     */
    public function testHasToken($token, $tokenExpires, $expected)
    {
        $gateway = Omnipay::create('PaypalRest');

        $gateway->setToken($token);
        $gateway->setTokenExpires($tokenExpires);

        $this->assertSame($expected, $gateway->hasToken());
    }

    public function testGetToken()
    {
        $gateway = $this->getMock('Omnipay\PaypalRest\Gateway', array('getTokenResponse'));

        $tokenResponse = new TokenResponse(
            $gateway->token(),
            array(
                'access_token' => 'testtoken',
                'expires_in' => 100,
            ),
            200
        );

        $gateway
            ->expects($this->once())
            ->method('getTokenResponse')
            ->will($this->returnValue($tokenResponse));

        $token = $gateway->getToken();

        $this->assertSame('testtoken', $token);
        $this->assertGreaterThanOrEqual(time() + 100, $gateway->getTokenExpires());

        $this->assertSame('testtoken', $gateway->getToken());
    }

    public function testGetTokenResponse()
    {
        $gateway = $this->getMock('Omnipay\PaypalRest\Gateway', array('token'));

        $tokenRequest = $this->getMock('Omnipay\PaypalRest\Message\TokenRequest', array('send'), array(), '', false);

        $expected = new TokenResponse($tokenRequest, array(), 200);

        $gateway
            ->expects($this->once())
            ->method('token')
            ->will($this->returnValue($tokenRequest));

        $tokenRequest
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue($expected));

        $response = $gateway->getTokenResponse();

        $this->assertSame($expected, $response);
    }

    /**
     * @covers ::createRequestWithToken
     */
    public function testCreateRequestWithToken()
    {
        $gateway = $this->getMock('Omnipay\PaypalRest\Gateway', array('getToken'));

        $gateway
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue('tokentest'));

        $request = $gateway->createRequestWithToken('Omnipay\PaypalRest\Message\PaymentRequest', array());
        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentRequest', $request);

        $token = $request->getToken();

        $this->assertSame('tokentest', $token);

    }

    public function dataRequest()
    {
        return array(
            array('purchase', 'Omnipay\PaypalRest\Message\PaymentRequest', array('intent' => 'sale')),
            array('completePurchase', 'Omnipay\PaypalRest\Message\PaymentCompleteRequest', array()),
            array('authorise', 'Omnipay\PaypalRest\Message\PaymentRequest', array('intent' => 'authorize')),
            array('completeAuthorise', 'Omnipay\PaypalRest\Message\PaymentCompleteRequest', array()),
            array('capture', 'Omnipay\PaypalRest\Message\CaptureRequest', array()),
            array('void', 'Omnipay\PaypalRest\Message\VoidRequest', array()),
            array('createCard', 'Omnipay\PaypalRest\Message\CreateCardRequest', array()),
            array('updateCard', 'Omnipay\PaypalRest\Message\UpdateCardRequest', array()),
            array('deleteCard', 'Omnipay\PaypalRest\Message\DeleteCardRequest', array()),
            array('refund', 'Omnipay\PaypalRest\Message\RefundRequest', array('type' => 'sale')),
        );
    }

    /**
     * @covers ::purchase
     * @covers ::completePurchase
     * @covers ::authorise
     * @covers ::completeAuthorise
     * @covers ::capture
     * @covers ::void
     * @covers ::createCard
     * @covers ::updateCard
     * @covers ::deleteCard
     * @covers ::refund
     * @dataProvider dataRequest
     */
    public function testRequest($method, $class, $additionalParameters)
    {
        $gateway = $this->getMock('Omnipay\PaypalRest\Gateway', array('createRequestWithToken'));

        $expected = $this->getMock($class, array(), array(), '', false);

        $gateway
            ->expects($this->once())
            ->method('createRequestWithToken')
            ->with(
                $this->equalTo($class),
                $this->equalTo(array('description' => 'testdesc') + $additionalParameters)
            )
            ->will($this->returnValue($expected));

        $request = $gateway->$method(array('description' => 'testdesc'));

        $this->assertSame($expected, $request);
    }
}

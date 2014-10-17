<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\TokenRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\TokenRequest
 */
class TokenRequestTest extends TestCase
{
    /**
     * @covers ::getEndpoint
     */
    public function testGetEndpoint()
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('/oauth2/token', $request->getEndpoint());
    }

    /**
     * @covers ::getClientId
     * @covers ::setClientId
     */
    public function testClientId()
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setClientId('clientId'));
        $this->assertSame('clientId', $request->getClientId());
    }

    /**
     * @covers ::getSecret
     * @covers ::setSecret
     */
    public function testSecret()
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setSecret('secret'));
        $this->assertSame('secret', $request->getSecret());
    }

    /**
     * @covers ::getData
     */
    public function testGetData()
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals(array(), $request->getData());
    }

    /**
     * @covers ::getHttpRequest
     */
    public function testGetHttpRequest()
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'secret' => 'test',
            'clientId' => 'client'
        ));

        $httpRequest = $request->getHttpRequest();

        $expectedHeaders = array(
            'Host' => array('api.paypal.com'),
            'Accept' => array('application/json'),
            'Content-Type' => array('application/x-www-form-urlencoded; charset=utf-8'),
            'User-Agent' => array('Guzzle/3.9.2 curl/7.26.0 PHP/5.4.4-14+deb7u14'),
            'Authorization' => array('Basic Y2xpZW50OnRlc3Q=')
        );

        $this->assertEquals('POST', $httpRequest->getMethod());
        $this->assertEquals($expectedHeaders, $httpRequest->getHeaders()->toArray());
    }

    /**
     * @covers ::sendHttpRequest
     */
    public function testSendHttpRequest()
    {
        $request = $this->getMock(
            'Omnipay\PaypalRest\Message\TokenRequest',
            ['getHttpRequest'],
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $httpRequest = $this->getMock('Guzzle\Http\Message\Request', ['send'], [], '', false);

        $expected = new Response(204, array('Content-Type' => 'application/json'), '{"access_token":"test"}');

        $request
            ->expects($this->once())
            ->method('getHttpRequest')
            ->will($this->returnValue($httpRequest));

        $httpRequest
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue($expected));

        $response = $request->sendHttpRequest(array());

        $this->assertSame($expected, $response);
    }

    /**
     * @covers ::sendData
     */
    public function testSendData()
    {
        $request = $this->getMock(
            'Omnipay\PaypalRest\Message\TokenRequest',
            ['sendHttpRequest'],
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $request
            ->expects($this->once())
            ->method('sendHttpRequest')
            ->will($this->returnValue(
                new Response(204, array('Content-Type' => 'application/json'), '{"access_token":"test"}')
            ));

        $response = $request->sendData(array());

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\TokenResponse', $response);
        $this->assertEquals(array('access_token' => 'test'), $response->getData());
    }
}

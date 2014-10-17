<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\AbstractPaypalRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\AbstractPaypalRequest
 */
class AbstractPaypalRequestTest extends TestCase
{
    /**
     * @covers ::initialize
     * @covers ::generateRequestId
     */
    public function testInitialize()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractPaypalRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $request->initialize();

        $this->assertNotNull($request->getRequestId());

        $request2 = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractPaypalRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $request2->initialize();

        $this->assertNotEquals($request2->getRequestId(), $request->getRequestId());
    }

    /**
     * @return ::getHttpMethod
     */
    public function testGetHttpMethod()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractPaypalRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $this->assertEquals('POST', $request->getHttpMethod());
    }



    /**
     * @covers ::getRequestId
     * @covers ::setRequestId
     */
    public function testRequestId()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractPaypalRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $this->assertSame($request, $request->setRequestId('requestid'));
        $this->assertSame('requestid', $request->getRequestId());
    }

    /**
     * @covers ::getToken
     * @covers ::setToken
     */
    public function testToken()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractPaypalRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $this->assertSame($request, $request->setToken('token'));
        $this->assertSame('token', $request->getToken());
    }

    /**
     * @covers ::getHttpRequest
     */
    public function testGetHttpRequest()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractPaypalRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $request->initialize(array(
            'secret' => 'test',
            'token' => 'testtoken',
            'clientId' => 'client',
        ));

        $httpRequest = $request->getHttpRequest(array('test' => 10));

        $expectedHeaders = array(
            'Host' => array('api.paypal.com'),
            'Accept' => array('application/json'),
            'Content-Type' => array('application/json'),
            'User-Agent' => array($this->getHttpClient()->getDefaultUserAgent()),
            'Authorization' => array('Bearer testtoken'),
            'Content-Length' => array(11),
        );

        $expectedBody = '{"test":10}';

        $this->assertEquals('POST', $httpRequest->getMethod());
        $this->assertEquals($expectedHeaders, $httpRequest->getHeaders()->toArray());
        $this->assertEquals($expectedBody, (string) $httpRequest->getBody());
    }

    /**
     * @covers ::sendHttpRequest
     */
    public function testSendHttpRequest()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractPaypalRequest',
            array($this->getHttpClient(), $this->getHttpRequest()),
            '',
            true,
            true,
            true,
            array('getHttpRequest')
        );


        $httpRequest = $this->getMock('Guzzle\Http\Message\Request', array('send'), array(), '', false);

        $expected = new Response(204, array('Content-Type' => 'application/json'), '{"status":"ok"}');

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
}

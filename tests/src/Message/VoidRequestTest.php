<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\VoidRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\VoidRequest
 */
class VoidRequestTest extends TestCase
{
    /**
     * @covers ::getEndpoint
     */
    public function testGetEndpoint()
    {
        $request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array('transactionReference' => 'id-12'));

        $this->assertEquals('/payments/authorization/id-12/void', $request->getEndpoint());
    }

    /**
     * @covers ::getEndpoint
     */
    public function testInvalidGetEndpoint()
    {
        $request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->setExpectedException(
            'Omnipay\Common\Exception\InvalidRequestException',
            'The transactionReference parameter is required'
        );

        $request->getEndpoint();
    }

    /**
     * @return ::getHttpMethod
     */
    public function testGetHttpMethod()
    {
        $request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('POST', $request->getHttpMethod());
    }

    /**
     * @covers ::getData
     */
    public function testGetData()
    {
        $request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals(array(), $request->getData());
    }

    /**
     * @covers ::sendData
     */
    public function testSendData()
    {
        $request = $this->getMock(
            'Omnipay\PaypalRest\Message\VoidRequest',
            array('sendHttpRequest'),
            array($this->getHttpClient(), $this->getHttpRequest())
        );

        $data = array('data' => 1);

        $request
            ->expects($this->once())
            ->method('sendHttpRequest')
            ->with($this->identicalTo($data))
            ->will($this->returnValue(
                new Response(204, array('Content-Type' => 'application/json'), '{"state":"voided"}')
            ));

        $response = $request->sendData($data);

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\VoidResponse', $response);
        $this->assertEquals(array('state' => 'voided'), $response->getData());
    }
}

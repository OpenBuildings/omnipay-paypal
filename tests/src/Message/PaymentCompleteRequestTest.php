<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\PaymentCompleteRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\PaymentCompleteRequest
 */
class PaymentCompleteRequestTest extends TestCase
{
    public function dataGetEndpoint()
    {
        return array(
            array(
                array('transactionReference' => 'id-12'),
                '/payments/payment/id-12/execute',
                null,
            ),
            array(
                array(),
                null,
                'The transactionReference parameter is required'
            ),
        );
    }

    /**
     * @dataProvider dataGetEndpoint
     * @covers ::getEndpoint
     */
    public function testGetEndpoint($parameters, $expected, $expectedException)
    {
        $request = new PaymentCompleteRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($parameters);

        if ($expectedException) {
            $this->setExpectedException(
                'Omnipay\Common\Exception\InvalidRequestException',
                $expectedException
            );
        }

        $this->assertEquals($expected, $request->getEndpoint());
    }

    /**
     * @return ::getHttpMethod
     */
    public function testGetHttpMethod()
    {
        $request = new PaymentCompleteRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('POST', $request->getHttpMethod());
    }

    /**
     * @covers ::getPayerId
     * @covers ::setPayerId
     */
    public function testPayerId()
    {
        $request = new PaymentCompleteRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setPayerId('payerId'));
        $this->assertSame('payerId', $request->getPayerId());
    }

    public function dataGetData()
    {
        return array(
            array(
                array(),
                null,
                'The payerId parameter is required'
            ),
            array(
                array('payerId' => 'asdweq'),
                array('payer_id' => 'asdweq'),
                null
            ),
        );
    }

    /**
     * @dataProvider dataGetData
     * @covers ::getData
     */
    public function testGetData($parameters, $expected, $expectedException)
    {
        $request = new PaymentCompleteRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($parameters);

        if ($expectedException) {
            $this->setExpectedException(
                'Omnipay\Common\Exception\InvalidRequestException',
                $expectedException
            );
        }

        $this->assertEquals($expected, $request->getData());
    }


    /**
     * @covers ::sendData
     */
    public function testSendData()
    {
        $request = $this->getMock(
            'Omnipay\PaypalRest\Message\PaymentCompleteRequest',
            array('sendHttpRequest'),
            array($this->getHttpClient(), $this->getHttpRequest())
        );

        $data = array('data' => 1);

        $request
            ->expects($this->once())
            ->method('sendHttpRequest')
            ->with($this->identicalTo($data))
            ->will($this->returnValue(
                new Response(204, array('Content-Type' => 'application/json'), '{"state":"approved"}')
            ));

        $response = $request->sendData($data);

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentResponse', $response);
        $this->assertEquals(array('state' => 'approved'), $response->getData());
    }
}

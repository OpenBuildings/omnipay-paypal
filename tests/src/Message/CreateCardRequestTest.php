<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\CreateCardRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\CreateCardRequest
 */
class CreateCardRequestTest extends TestCase
{
    /**
     * @covers ::getEndpoint
     */
    public function testGetEndpoint()
    {
        $request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('/vault/credit-card', $request->getEndpoint());
    }

    /**
     * @return ::getHttpMethod
     */
    public function testGetHttpMethod()
    {
        $request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('POST', $request->getHttpMethod());
    }

    /**
     * @covers ::getPayerId
     * @covers ::setPayerId
     */
    public function testPayerId()
    {
        $request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setPayerId('payerId'));
        $this->assertSame('payerId', $request->getPayerId());
    }


    public function dataGetData()
    {
        return array(
            // Invalid Credit card
            array(
                array(),
                array(),
                'The card parameter is required'
            ),
            // Valid Credit card
            array(
                array(
                    'card' => array(
                        'number' => '4111111111111111',
                        'expiryMonth' => 9,
                        'expiryYear' => 2017,
                        'cvv' => 123,
                    )
                ),
                array(
                    'number' => '4111111111111111',
                    'expire_month' => 9,
                    'expire_year' => 2017,
                    'cvv2' => 123,
                    'type' => 'visa'
                ),
                null,
            ),
            // Valid Credit card with payer id
            array(
                array(
                    'payerId' => 'asdasd',
                    'card' => array(
                        'number' => '4111111111111111',
                        'expiryMonth' => 9,
                        'expiryYear' => 2017,
                        'cvv' => 123,
                    )
                ),
                array(
                    'number' => '4111111111111111',
                    'expire_month' => 9,
                    'expire_year' => 2017,
                    'cvv2' => 123,
                    'payer_id' => 'asdasd',
                    'type' => 'visa',
                ),
                null,
            ),
        );
    }

    /**
     * @dataProvider dataGetData
     * @covers ::getData
     */
    public function testGetData($parameters, $expected, $expectedException)
    {
        $request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
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
            'Omnipay\PaypalRest\Message\CreateCardRequest',
            array('sendHttpRequest'),
            array($this->getHttpClient(), $this->getHttpRequest())
        );

        $data = array('data' => 1);

        $request
            ->expects($this->once())
            ->method('sendHttpRequest')
            ->with($this->identicalTo($data))
            ->will($this->returnValue(
                new Response(201, array('Content-Type' => 'application/json'), '{"state":"created"}')
            ));

        $response = $request->sendData($data);

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\CreateCardResponse', $response);
        $this->assertEquals(array('state' => 'created'), $response->getData());
    }
}

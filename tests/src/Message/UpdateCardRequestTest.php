<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\UpdateCardRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\UpdateCardRequest
 */
class UpdateCardRequestTest extends TestCase
{
    public function dataGetEndpoint()
    {
        return array(
            array(
                array('cardReference' => 'id-12'),
                '/vault/credit-card/id-12',
                null,
            ),
            array(
                array(),
                null,
                'The cardReference parameter is required'
            ),
        );
    }

    /**
     * @dataProvider dataGetEndpoint
     * @covers ::getEndpoint
     */
    public function testGetEndpoint($parameters, $expected, $expectedException)
    {
        $request = new UpdateCardRequest($this->getHttpClient(), $this->getHttpRequest());
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
        $request = new UpdateCardRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('PATCH', $request->getHttpMethod());
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
                    array(
                        'path' => '/expire_month',
                        'op' => 'add',
                        'value' => 9,
                    ),
                    array(
                        'path' => '/expire_year',
                        'op' => 'add',
                        'value' => 2017,
                    ),
                    array(
                        'path' => '/first_name',
                        'op' => 'add',
                        'value' => '',
                    ),
                    array(
                        'path' => '/last_name',
                        'op' => 'add',
                        'value' => '',
                    ),
                    array(
                        'path' => '/billing_address',
                        'op' => 'add',
                        'value' => array(
                            'line1' => '',
                            'line2' => '',
                            'city' => '',
                            'state' => '',
                            'postal_code' => '',
                            'country_code' => '',
                        )
                    ),
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
        $request = new UpdateCardRequest($this->getHttpClient(), $this->getHttpRequest());
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
            'Omnipay\PaypalRest\Message\UpdateCardRequest',
            array('sendHttpRequest'),
            array($this->getHttpClient(), $this->getHttpRequest())
        );

        $data = array('data' => 1);

        $request
            ->expects($this->once())
            ->method('sendHttpRequest')
            ->with($this->identicalTo($data))
            ->will($this->returnValue(
                new Response(201, array('Content-Type' => 'application/json'), '{"state":"ok"}')
            ));

        $response = $request->sendData($data);

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\UpdateCardResponse', $response);
        $this->assertEquals(array('state' => 'ok'), $response->getData());
    }
}

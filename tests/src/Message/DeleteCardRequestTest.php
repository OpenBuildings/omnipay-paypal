<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\DeleteCardRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\DeleteCardRequest
 */
class DeleteCardRequestTest extends TestCase
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
        $request = new DeleteCardRequest($this->getHttpClient(), $this->getHttpRequest());
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
        $request = new DeleteCardRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('DELETE', $request->getHttpMethod());
    }

    /**
     * @covers ::getData
     */
    public function testGetData()
    {
        $request = new DeleteCardRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals(array(), $request->getData());
    }

    /**
     * @covers ::sendData
     */
    public function testSendData()
    {
        $request = $this->getMock(
            'Omnipay\PaypalRest\Message\DeleteCardRequest',
            ['sendHttpRequest'],
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $data = array('data' => 1);

        $request
            ->expects($this->once())
            ->method('sendHttpRequest')
            ->with($this->identicalTo($data))
            ->will($this->returnValue(
                new Response(201, array('Content-Type' => 'application/json'), '')
            ));

        $response = $request->sendData($data);

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\DeleteCardResponse', $response);
        $this->assertEquals(array(), $response->getData());
    }
}

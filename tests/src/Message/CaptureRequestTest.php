<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\CaptureRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\CaptureRequest
 */
class CaptureRequestTest extends TestCase
{
    /**
     * @covers ::getEndpoint
     */
    public function testGetEndpoint()
    {
        $request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array('purchaseId' => 'id-12'));

        $this->assertEquals('/payments/authorization/id-12/capture', $request->getEndpoint());
    }

    /**
     * @covers ::getEndpoint
     */
    public function testInvalidGetEndpoint()
    {
        $request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->setExpectedException(
            'Omnipay\Common\Exception\InvalidRequestException',
            'The purchaseId parameter is required'
        );

        $request->getEndpoint();
    }

    /**
     * @return ::getHttpMethod
     */
    public function testGetHttpMethod()
    {
        $request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('POST', $request->getHttpMethod());
    }

    /**
     * @covers ::getPurchaseId
     * @covers ::setPurchaseId
     */
    public function testPurchaseId()
    {
        $request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setPurchaseId('purchaseid'));
        $this->assertSame('purchaseid', $request->getPurchaseId());
    }

    /**
     * @covers ::getIsFinalCapture
     * @covers ::setIsFinalCapture
     */
    public function testIsFinalCapture()
    {
        $request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setIsFinalCapture('purchaseid'));
        $this->assertSame('purchaseid', $request->getIsFinalCapture());
    }

    public function dataGetData()
    {
        return array(
            array(
                array(),
                array(),
                'The amount parameter is required'
            ),
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP'
                ),
                array(
                    'amount' => array('total' => '10.00', 'currency' => 'GBP')
                ),
                null
            ),
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                    'isFinalCapture' => true
                ),
                array(
                    'amount' => array('total' => '10.00', 'currency' => 'GBP'),
                    'is_final_capture' => true
                ),
                null
            ),
            array(
                array('amount' => '10.00'),
                null,
                'The currency parameter is required'
            ),
        );
    }

    /**
     * @dataProvider dataGetData
     * @covers ::getData
     */
    public function testGetData($parameters, $expected, $expectedException)
    {
        $request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
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
            'Omnipay\PaypalRest\Message\CaptureRequest',
            ['sendHttpRequest'],
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $data = array('data' => 1);

        $request
            ->expects($this->once())
            ->method('sendHttpRequest')
            ->with($this->identicalTo($data))
            ->will($this->returnValue(
                new Response(204, array('Content-Type' => 'application/json'), '{"state":"completed"}')
            ));

        $response = $request->sendData($data);

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\CaptureResponse', $response);
        $this->assertEquals(array('state' => 'completed'), $response->getData());
    }
}

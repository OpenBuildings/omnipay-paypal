<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\RefundRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\RefundRequest
 */
class RefundRequestTest extends TestCase
{
    public function dataGetEndpoint()
    {
        return array(
            array(
                array('purchaseId' => 'id-12', 'type' => 'sale'),
                '/payments/sale/id-12/refund',
                null,
            ),
            array(
                array('purchaseId' => 'id-12', 'type' => 'capture'),
                '/payments/capture/id-12/refund',
                null,
            ),
            array(
                array('purchaseId' => 'id-12', 'type' => 'authorization'),
                '/payments/authorization/id-12/refund',
                null,
            ),
            array(
                array('purchaseId' => 'id-12', 'type' => 'authorization'),
                '/payments/authorization/id-12/refund',
                null,
            ),
            array(
                array('type' => 'sale'),
                null,
                'The purchaseId parameter is required'
            ),
            array(
                array('purchaseId' => 'id-12', 'type' => 'test'),
                null,
                'Type can only be "sale", "authorization" or "capture"'
            ),
        );
    }

    /**
     * @dataProvider dataGetEndpoint
     * @covers ::getEndpoint
     */
    public function testGetEndpoint($parameters, $expected, $expectedException)
    {
        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
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
        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('POST', $request->getHttpMethod());
    }

    /**
     * @covers ::getPurchaseId
     * @covers ::setPurchaseId
     */
    public function testPurchaseId()
    {
        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setPurchaseId('purchaseid'));
        $this->assertSame('purchaseid', $request->getPurchaseId());
    }

    /**
     * @covers ::getType
     * @covers ::setType
     */
    public function testType()
    {
        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setType('type'));
        $this->assertSame('type', $request->getType());
    }

    public function dataGetData()
    {
        return array(
            array(
                array(),
                array(),
                null
            ),
            array(
                array('amount' => '10.00', 'currency' => 'GBP'),
                array('amount' => array('total' => '10.00', 'currency' => 'GBP')),
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
        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
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
            'Omnipay\PaypalRest\Message\RefundRequest',
            array('sendHttpRequest'),
            array($this->getHttpClient(), $this->getHttpRequest())
        );

        $data = array('data' => 1);

        $request
            ->expects($this->once())
            ->method('sendHttpRequest')
            ->with($this->identicalTo($data))
            ->will($this->returnValue(
                new Response(200, array('Content-Type' => 'application/json'), '{"state":"completed"}')
            ));

        $response = $request->sendData($data);

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\RefundResponse', $response);
        $this->assertEquals(array('state' => 'completed'), $response->getData());
    }
}

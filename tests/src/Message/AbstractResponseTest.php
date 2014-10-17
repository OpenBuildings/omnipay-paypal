<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\TokenRequest;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\AbstractResponse
 */
class AnstractResponseTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getCode
     */
    public function testConstruct()
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractResponse',
            array($request, array('data' => 1), 204)
        );

        $this->assertSame(204, $response->getCode());
    }

    /**
     * @covers ::getTransactionReference
     */
    public function testGetTransactionReference()
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractResponse',
            array($request, array('id' => 'asdasd'))
        );

        $this->assertSame('asdasd', $response->getTransactionReference());

        $response = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractResponse',
            array($request, array())
        );

        $this->assertNull($response->getTransactionReference());
    }

    /**
     * @covers ::getStatus
     */
    public function testGetStatus()
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractResponse',
            array($request, array('status' => 'asdasd'))
        );

        $this->assertSame('asdasd', $response->getStatus());

        $response = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractResponse',
            array($request, array())
        );

        $this->assertNull($response->getStatus());
    }

    public function dataIsSuccessful()
    {
        return array(
            array(201, true),
            array(202, true),
            array(203, true),
            array(204, true),
            array(304, true),
            array(400, false),
            array(404, false),
            array(500, false),
        );
    }

    /**
     * @dataProvider dataIsSuccessful
     * @covers ::isSuccessful
     */
    public function testIsSuccessful($status, $expected)
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractResponse',
            array($request, array(), $status)
        );

        $this->assertSame($expected, $response->isSuccessful());
    }

    public function dataGetMessage()
    {
        return array(
            array(
                array(),
                null,
            ),
            array(
                array('name' => 'test'),
                null,
            ),
            array(
                array('name' => 'ERROR', 'message' => 'message text'),
                'ERROR: message text',
            ),
            array(
                array('name' => 'ERROR', 'message' => 'message text', 'details' => array('asd', 'asd')),
                'ERROR: message text [["asd","asd"]]',
            ),
        );
    }

    /**
     * @dataProvider dataGetMessage
     * @covers ::getMessage
     */
    public function testGetMessage($data, $expected)
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractResponse',
            array($request, $data)
        );

        $this->assertSame($expected, $response->getMessage());
    }
}

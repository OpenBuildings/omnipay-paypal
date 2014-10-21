<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\PaymentRequest;
use Omnipay\PaypalRest\Message\PaymentResponse;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\PaymentResponse
 */
class PaymentResponseTest extends TestCase
{
    public function dataIsSuccessful()
    {
        return array(
            array(array(), 200, false),
            array(array('state' => 'voided'), 200, false),
            array(array('state' => 'approved'), 401, false),
            array(array('state' => 'approved'), 200, true),
        );
    }

    /**
     * @dataProvider dataIsSuccessful
     * @covers ::isSuccessful
     */
    public function testIsSuccessful($data, $status, $expected)
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new PaymentResponse($request, $data, $status);

        $this->assertSame($expected, $response->isSuccessful());
    }

    public function dataGetRelatedResourceId()
    {
        return array(
            // Empty
            array(
                array(),
                null,
                null,
                null
            ),
            // Authorization
            array(
                array(
                    'status' => 'approved',
                    'intent' => 'authorize',
                    'transactions' => array(
                        array(
                            'amount' => array(
                                'total' => '15.00',
                                'currency' => 'GBP',
                            ),
                            'related_resources' => array(
                                array(
                                    'authorization' => array(
                                        'id' => '1SN458127W2399139'
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                0,
                'authorization',
                '1SN458127W2399139'
            ),
            // Sale
            array(
                array(
                    'status' => 'approved',
                    'intent' => 'sale',
                    'transactions' => array(
                        array(
                            'amount' => array(
                                'total' => '15.00',
                                'currency' => 'GBP',
                            ),
                            'related_resources' => array(
                                array(
                                    'sale' => array(
                                        'id' => '1SN458127W2399139'
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                0,
                'sale',
                '1SN458127W2399139'
            ),
        );
    }

    /**
     * @dataProvider dataGetRelatedResourceId
     * @covers ::getRelatedResourceId
     */
    public function testGetRelatedResourceId($data, $index, $type, $expected)
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new PaymentResponse($request, $data);

        $this->assertSame($expected, $response->getRelatedResourceId($index, $type));
    }

    public function dataGetIntent()
    {
        return array(
            array(array(), null),
            array(array('intent' => 'sale'), 'sale'),
            array(array('intent' => 'authorization'), 'authorization'),
        );
    }

    public function dataGetTransactionReference()
    {
        return array(
            array(null, null, null, null),
            array('sale', 'sale', 12, 12),
            array('authorize', 'authorization', 10, 10),
            array('authorize', 'authorization', null, null),
        );
    }

    /**
     * @dataProvider dataGetTransactionReference
     * @covers ::getTransactionReference
     */
    public function testGetTransactionReference($intent, $type, $relatedResourceId, $expected)
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = $this->getMock(
            'Omnipay\PaypalRest\Message\PaymentResponse',
            array('getIntent', 'getRelatedResourceId'),
            array($request, array())
        );

        if ($intent) {
            $response
                ->expects($this->atLeastOnce())
                ->method('getIntent')
                ->will($this->returnValue($intent));
        }

        if ($relatedResourceId) {
            $response
                ->expects($this->once())
                ->method('getRelatedResourceId')
                ->with($this->equalTo(0), $this->equalTo($type))
                ->will($this->returnValue($relatedResourceId));
        }

        $this->assertSame($expected, $response->getTransactionReference());
    }
}

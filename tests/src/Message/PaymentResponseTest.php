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

    public function dataGetTransactionReference()
    {
        return array(
            // Empty
            array(
                array(),
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
                '1SN458127W2399139'
            ),
        );
    }
    /**
     * @dataProvider dataGetTransactionReference
     * @covers ::getTransactionReference
     */
    public function testGetTransactionReference($data, $expected)
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new PaymentResponse($request, $data);

        $this->assertSame($expected, $response->getTransactionReference());
    }
}

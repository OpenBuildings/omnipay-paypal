<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\PaymentRequest;
use Guzzle\Http\Message\Response;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\PaymentRequest
 */
class PaymentRequestTest extends TestCase
{
    /**
     * @covers ::getEndpoint
     */
    public function testGetEndpoint()
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertEquals('/payments/payment', $request->getEndpoint());
    }

    public function dataGetTransationData()
    {
        return array(
            // With items
            array(
                array(
                    'amount' => '15.00',
                    'currency' => 'GBP',
                    'items' => array(
                        array(
                            'name' => 'Product 1',
                            'price' => '5.00',
                            'description' => 'Product 1 Desc',
                            'quantity' => 2
                        ),
                        array(
                            'name' => 'Shipping for Product 1',
                            'price' => '5.00',
                            'description' => 'Shipping for Product 1 Desc',
                            'quantity' => 1
                        ),
                    ),
                ),
                array(
                    'transactions' => array(
                        array(
                            'amount' => array(
                                'total' => '15.00',
                                'currency' => 'GBP',
                            ),
                            'item_list' => array(
                                'items' => array(
                                    array(
                                        'name' => 'Product 1',
                                        'quantity' => 2,
                                        'price' => '5.00',
                                        'description' => 'Product 1 Desc',
                                        'currency' => 'GBP',
                                    ),
                                    array(
                                        'name' => 'Shipping for Product 1',
                                        'quantity' => 1,
                                        'price' => '5.00',
                                        'description' => 'Shipping for Product 1 Desc',
                                        'currency' => 'GBP',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            // Without items
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                ),
                array(
                    'transactions' => array(
                        array(
                            'amount' => array(
                                'total' => '10.00',
                                'currency' => 'GBP',
                            )
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * @covers ::getIntent
     * @covers ::setIntent
     */
    public function testIntent()
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setIntent('intent'));
        $this->assertSame('intent', $request->getIntent());
    }

    /**
     * @covers ::getPayerId
     * @covers ::setPayerId
     */
    public function testPayerId()
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertSame($request, $request->setPayerId('payerid'));
        $this->assertSame('payerid', $request->getPayerId());
    }

    /**
     * @covers ::getTransactionData
     * @dataProvider dataGetTransationData
     */
    public function testGetTransactionData($parameters, $expected)
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($parameters);

        $data = $request->getTransactionData();

        $this->assertEquals($expected, $data);
    }

    public function dataGetPayerPaypalData()
    {
        return array(
            // Normal data
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                    'returnUrl' => 'http://example.com/completed',
                    'cancelUrl' => 'http://example.com/canceled',
                ),
                array(
                    'payer' => array(
                        'payment_method' => 'paypal',
                    ),
                    'redirect_urls' => array_filter(array(
                        'return_url' => 'http://example.com/completed',
                        'cancel_url' => 'http://example.com/canceled',
                    )),
                ),
                null,
            ),
            // Without cancel
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                    'returnUrl' => 'http://example.com/completed',
                ),
                array(),
                'The cancelUrl parameter is required',
            ),
            // Wrong data
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                ),
                array(),
                'The returnUrl parameter is required'
            ),
        );
    }

    /**
     * @dataProvider dataGetPayerPaypalData
     */
    public function testGetPayerPaypalData($parameters, $expected, $exception)
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($parameters);

        if ($exception) {
            $this->setExpectedException('Omnipay\Common\Exception\InvalidRequestException', $exception);
        }

        $data = $request->getPayerPaypalData();

        $this->assertEquals($expected, $data);
    }

    public function dataGetRequiredRedirect()
    {
        return array(
            array(
                array(),
                true,
            ),
            array(
                array('card' => $this->getValidCard()),
                false,
            ),
            array(
                array('cardReference' => 'asds'),
                false,
            ),
        );
    }

    /**
     * @dataProvider dataGetRequiredRedirect
     * @covers ::getRequiredRedirect
     */
    public function testGetRequiredRedirect($parameters, $expected)
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($parameters);

        $this->assertEquals($expected, $request->getRequiredRedirect());
    }

    public function dataGetPayerCardReferenceData()
    {
        return array(
            // Normal data
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                    'cardReference' => '123123',
                    'payerId' => 'someId'
                ),
                array(
                    'payer' => array(
                        'payment_method' => 'credit_card',
                        'funding_instruments' => array(
                            array(
                                'credit_card_token' => array(
                                    'credit_card_id' => '123123',
                                    'payer_id' => 'someId',
                                ),
                            ),
                        ),
                    ),
                ),
                null,
            ),
            // Wrong data
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                ),
                array(),
                'The cardReference parameter is required',
            ),
        );
    }

    /**
     * @dataProvider dataGetPayerCardReferenceData
     * @covers ::getPayerCardReferenceData
     */
    public function testGetPayerCardReferenceData($parameters, $expected, $exception)
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($parameters);

        if ($exception) {
            $this->setExpectedException('Omnipay\Common\Exception\InvalidRequestException', $exception);
        }

        $data = $request->getPayerCardReferenceData();

        $this->assertEquals($expected, $data);
    }

    public function dataGetPayerCardData()
    {
        $card = $this->getValidCard();

        return array(
            // Normal data
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                    'card' => $card,
                ),
                array(
                    'payer' => array(
                        'payment_method' => 'credit_card',
                        'funding_instruments' => array(
                            array(
                                'credit_card' => array(
                                    'number' => '4111111111111111',
                                    'type' => 'visa',
                                    'expire_month' => $card['expiryMonth'],
                                    'expire_year' => $card['expiryYear'],
                                    'cvv2' => $card['cvv'],
                                    'first_name' => 'Example',
                                    'last_name' => 'User',
                                    'billing_address' => array(
                                        'line1' => '123 Billing St',
                                        'line2' => 'Billsville',
                                        'city' => 'Billstown',
                                        'state' => 'CA',
                                        'postal_code' => '12345',
                                        'country_code' => 'US',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                null,
            ),
            // Wrong data
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                ),
                array(),
                'The card parameter is required',
            ),
        );
    }

    /**
     * @dataProvider dataGetPayerCardData
     */
    public function testGetPayerCardData($parameters, $expected, $exception)
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($parameters);

        if ($exception) {
            $this->setExpectedException('Omnipay\Common\Exception\InvalidRequestException', $exception);
        }

        $data = $request->getPayerCardData();

        $this->assertEquals($expected, $data);
    }

    public function dataGetPayerData()
    {
        $card = $this->getValidCard();

        return array(
            // Card data
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                    'card' => $this->getValidCard(),
                ),
                'getPayerCardData',
            ),
            // Card reference
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                    'cardReference' => '123123',
                ),
                'getPayerCardReferenceData',
            ),
            // Paypal
            array(
                array(
                    'amount' => '10.00',
                    'currency' => 'GBP',
                ),
                'getPayerPaypalData',
            ),
        );
    }

    /**
     * @dataProvider dataGetPayerData
     *
     * @covers ::getPayerCardData
     * @covers ::getPayerCardReferenceData
     * @covers ::getPayerPaypalData
     * @covers ::getPayerData
     */
    public function testGetPayerData($parameters, $expectedMethod)
    {
        $request = $this->getMock(
            'Omnipay\PaypalRest\Message\PaymentRequest',
            array($expectedMethod),
            array($this->getHttpClient(), $this->getHttpRequest())
        );

        $request->initialize($parameters);

        $request
            ->expects($this->once())
            ->method($expectedMethod)
            ->will($this->returnValue(array('return' => true)));

        $data = $request->getPayerData();

        $this->assertEquals(array('return' => true), $data);
    }

    public function testGetDataInvalid()
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array('intent' => 'test'));

        $this->setExpectedException('Omnipay\Common\Exception\InvalidRequestException', 'Intent can only be "sale" or "authorize"');

        $data = $request->getData();
    }

    /**
     * @covers ::getData
     */
    public function testGetData()
    {
        $request = $this->getMock(
            'Omnipay\PaypalRest\Message\PaymentRequest',
            array('getPayerData', 'getTransactionData'),
            array($this->getHttpClient(), $this->getHttpRequest())
        );

        $request->initialize(array('intent' => 'authorize'));

        $data1 = array('return1' => true, 'nested' => array('test' => false));
        $data2 = array('return2' => false, 'nested' => array('param' => 12));

        $expected = array(
            'intent' => 'authorize',
            'return1' => true,
            'return2' => false,
            'nested' => array(
                'test' => false,
                'param' => 12,
            )
        );

        $request
            ->expects($this->once())
            ->method('getPayerData')
            ->will($this->returnValue($data1));

        $request
            ->expects($this->once())
            ->method('getTransactionData')
            ->will($this->returnValue($data2));

        $data = $request->getData();

        $this->assertEquals($expected, $data);
    }

    /**
     * @covers ::sendData
     */
    public function testSendData()
    {
        $request = $this->getMock(
            'Omnipay\PaypalRest\Message\PaymentRequest',
            array('sendHttpRequest', 'getRequiredRedirect'),
            array($this->getHttpClient(), $this->getHttpRequest())
        );

        $data = array('data' => 1);

        $request
            ->expects($this->exactly(2))
            ->method('sendHttpRequest')
            ->with($this->identicalTo($data))
            ->will($this->returnValue(
                new Response(201, array('Content-Type' => 'application/json'), '')
            ));

        $request
            ->expects($this->exactly(2))
            ->method('getRequiredRedirect')
            ->will($this->onConsecutiveCalls(true, false));

        $response = $request->sendData($data);

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentApproveResponse', $response);
        $this->assertEquals(array(), $response->getData());

        $response = $request->sendData($data);

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\PaymentResponse', $response);
        $this->assertEquals(array(), $response->getData());

    }
}

<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\AbstractRequest;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\AbstractRequest
 */
class AbstractRequestTest extends TestCase
{
    /**
     * @covers ::getServer
     */
    public function testGetServer()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $this->assertSame(AbstractRequest::LIVE, $request->getServer());

        $request->setTestMode(true);

        $this->assertSame(AbstractRequest::SANDBOX, $request->getServer());
    }

    /**
     * @covers ::getPaypalCard
     */
    public function testGetPaypalCard()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $request->setCard(array(
            'firstName' => 'Example',
            'lastName' => 'User',
            'number' => '4111111111111111',
            'expiryMonth' => 4,
            'expiryYear' => 2019,
            'cvv' => 210,
            'billingAddress1' => '123 Billing St',
            'billingAddress2' => 'Billsville',
            'billingCity' => 'Billstown',
            'billingPostcode' => '12345',
            'billingState' => 'CA',
            'billingCountry' => 'US',
            'billingPhone' => '(555) 123-4567',
            'shippingAddress1' => '123 Shipping St',
            'shippingAddress2' => 'Shipsville',
            'shippingCity' => 'Shipstown',
            'shippingPostcode' => '54321',
            'shippingState' => 'NY',
            'shippingCountry' => 'US',
            'shippingPhone' => '(555) 987-6543',
        ));

        $expected = array(
            'number' => '4111111111111111',
            'type' => 'visa',
            'expire_month' => 4,
            'expire_year' => 2019,
            'cvv2' => 210,
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
        );

        $this->assertEquals($expected, $request->getPaypalCard());
    }

    /**
     * @covers ::getPaypalCard
     */
    public function testGetPaypalCardMissing()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $this->setExpectedException('Omnipay\Common\Exception\InvalidRequestException', 'The card parameter is required');

        $request->getPaypalCard();
    }

    /**
     * @covers ::getPaypalCard
     */
    public function testGetPaypalCardInvalid()
    {
        $request = $this->getMockForAbstractClass(
            'Omnipay\PaypalRest\Message\AbstractRequest',
            [$this->getHttpClient(), $this->getHttpRequest()]
        );

        $request->setCard(array(
            'firstName' => 'Example',
            'lastName' => 'User',
            'number' => '123',
        ));

        $this->setExpectedException('Omnipay\Common\Exception\InvalidCreditCardException', 'The expiryMonth parameter is required');

        $request->getPaypalCard();
    }
}

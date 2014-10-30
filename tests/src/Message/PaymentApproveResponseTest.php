<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\PaymentRequest;
use Omnipay\PaypalRest\Message\PaymentApproveResponse;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\PaymentApproveResponse
 */
class PaymentApproveResponseTest extends TestCase
{
    /**
     * @covers ::isSuccessful
     */
    public function testIsSuccessful()
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new PaymentApproveResponse($request, array());

        $this->assertFalse($response->isSuccessful());
    }

    public function dataGetLink()
    {
        return array(
            array(
                'delete',
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/vault/credit-card/CARD-9TX83955Y6631202AKRAPFJI',
                    'rel' => 'delete',
                    'method' => 'DELETE'
                )
            ),
            array(
                'patch',
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/vault/credit-card/CARD-9TX83955Y6631202AKRAPFJI',
                    'rel' => 'patch',
                    'method' => 'PATCH'
                ),
            ),
            array(
                'approval_url',
                null,
            ),
        );
    }
    /**
     * @dataProvider dataGetLink
     * @covers ::getLink
     */
    public function testGetLink($link, $expected)
    {
        $data = array(
            'links' => array(
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/vault/credit-card/CARD-9TX83955Y6631202AKRAPFJI',
                    'rel' => 'self',
                    'method' => 'GET'
                ),
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/vault/credit-card/CARD-9TX83955Y6631202AKRAPFJI',
                    'rel' => 'delete',
                    'method' => 'DELETE'
                ),
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/vault/credit-card/CARD-9TX83955Y6631202AKRAPFJI',
                    'rel' => 'patch',
                    'method' => 'PATCH'
                ),
            )
        );

        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new PaymentApproveResponse($request, $data);

        $this->assertSame($expected, $response->getLink($link));
    }

    /**
     * @covers ::isRedirect
     */
    public function testIsRedirect()
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = $this->getMock(
            'Omnipay\PaypalRest\Message\PaymentApproveResponse',
            array('getLink'),
            array($request, array())
        );

        $response
            ->expects($this->exactly(2))
            ->method('getLink')
            ->with($this->identicalTo('approval_url'))
            ->will($this->onConsecutiveCalls(
                null,
                array(
                    'href' => 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-5SD087658M435925N',
                    'rel' => 'approval_url',
                    'method' => 'REDIRECT'
                )
            ));

        $this->assertFalse($response->isRedirect());

        $this->assertTrue($response->isRedirect());
    }

    /**
     * @covers ::getRedirectUrl
     */
    public function testGetRedirectUrl()
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = $this->getMock(
            'Omnipay\PaypalRest\Message\PaymentApproveResponse',
            array('getLink'),
            array($request, array())
        );

        $response
            ->expects($this->once())
            ->method('getLink')
            ->with($this->identicalTo('approval_url'))
            ->will($this->returnValue(
                array(
                    'href' => 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-5SD087658M435925N',
                    'rel' => 'approval_url',
                    'method' => 'REDIRECT'
                )
            ));

        $this->assertSame(
            'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-5SD087658M435925N',
            $response->getRedirectUrl()
        );
    }

    /**
     * @return ::getRedirectMethod
     */
    public function testGetRedirectMethod()
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new PaymentApproveResponse($request, array());


        $this->assertEquals('GET', $response->getRedirectMethod());
    }
    /**
     * @return ::getRedirectData
     */
    public function testGetRedirectData()
    {
        $request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new PaymentApproveResponse($request, array());


        $this->assertEquals(array(), $response->getRedirectData());
    }

}

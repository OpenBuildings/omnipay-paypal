<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\VoidRequest;
use Omnipay\PaypalRest\Message\VoidResponse;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\VoidResponse
 */
class VoidResponseTest extends TestCase
{
    public function dataIsSuccessful()
    {
        return array(
            array(array(), 200, false),
            array(array('state' => 'created'), 200, false),
            array(array('state' => 'voided'), 401, false),
            array(array('state' => 'voided'), 200, true),
        );
    }
    /**
     * @dataProvider dataIsSuccessful
     * @covers ::isSuccessful
     */
    public function testIsSuccessful($data, $status, $expected)
    {
        $request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new VoidResponse($request, $data, $status);

        $this->assertSame($expected, $response->isSuccessful());
    }
}

<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\CaptureRequest;
use Omnipay\PaypalRest\Message\CaptureResponse;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\CaptureResponse
 */
class CaptureResponseTest extends TestCase
{
    public function dataIsSuccessful()
    {
        return array(
            array(array(), 200, false),
            array(array('state' => 'created'), 200, false),
            array(array('state' => 'completed'), 401, false),
            array(array('state' => 'completed'), 200, true),
        );
    }
    /**
     * @dataProvider dataIsSuccessful
     * @covers ::isSuccessful
     */
    public function testIsSuccessful($data, $status, $expected)
    {
        $request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new CaptureResponse($request, $data, $status);

        $this->assertSame($expected, $response->isSuccessful());
    }
}

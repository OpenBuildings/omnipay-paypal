<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\CreateCardRequest;
use Omnipay\PaypalRest\Message\CreateCardResponse;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\CreateCardResponse
 */
class CreateCardResponseTest extends TestCase
{
    public function dataIsSuccessful()
    {
        return array(
            array(array(), 201, false),
            array(array('state' => 'created'), 201, false),
            array(array('state' => 'ok'), 401, false),
            array(array('state' => 'ok'), 201, true),
        );
    }
    /**
     * @dataProvider dataIsSuccessful
     * @covers ::isSuccessful
     */
    public function testIsSuccessful($data, $status, $expected)
    {
        $request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new CreateCardResponse($request, $data, $status);

        $this->assertSame($expected, $response->isSuccessful());
    }
}

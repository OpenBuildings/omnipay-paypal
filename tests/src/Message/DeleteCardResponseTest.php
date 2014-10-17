<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\DeleteCardRequest;
use Omnipay\PaypalRest\Message\DeleteCardResponse;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\DeleteCardResponse
 */
class DeleteCardResponseTest extends TestCase
{
    public function dataIsSuccessful()
    {
        return array(
            array(array(), 401, false),
            array(array(), 204, true),
        );
    }
    /**
     * @dataProvider dataIsSuccessful
     * @covers ::isSuccessful
     */
    public function testIsSuccessful($data, $status, $expected)
    {
        $request = new DeleteCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new DeleteCardResponse($request, $data, $status);

        $this->assertSame($expected, $response->isSuccessful());
    }
}

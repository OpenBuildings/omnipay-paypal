<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PaypalRest\Message\TokenRequest;
use Omnipay\PaypalRest\Message\TokenResponse;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\TokenResponse
 */
class TokenResponseTest extends TestCase
{
    public function dataIsSuccessful()
    {
        return array(
            array(array(), 201, false),
            array(array('state' => 'created'), 201, false),
            array(array('access_token' => '132123'), 401, false),
            array(array('access_token' => '132123'), 201, true),
        );
    }
    /**
     * @dataProvider dataIsSuccessful
     * @covers ::isSuccessful
     */
    public function testIsSuccessful($data, $status, $expected)
    {
        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new TokenResponse($request, $data, $status);

        $this->assertSame($expected, $response->isSuccessful());
    }

    /**
     * @covers ::getTokenType
     * @covers ::getAccessToken
     * @covers ::getAppId
     * @covers ::getExpiresIn
     */
    public function testGetters()
    {
        $data = array(
            'access_token' => 'asdasd',
            'token_type' => 'Bearer',
            'app_id' => '1231',
            'expires_in' => 28800
        );

        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new TokenResponse($request, $data, 201);

        $this->assertSame('Bearer', $response->getTokenType());
        $this->assertSame('1231', $response->getAppId());
        $this->assertSame('asdasd', $response->getAccessToken());
        $this->assertSame(28800, $response->getExpiresIn());
    }

    /**
     * @covers ::getTokenType
     * @covers ::getAccessToken
     * @covers ::getAppId
     * @covers ::getExpiresIn
     * @covers ::getExpires
     */
    public function testGettersEmpty()
    {
        $data = array();

        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new TokenResponse($request, $data, 201);

        $this->assertNull($response->getTokenType());
        $this->assertNull($response->getAppId());
        $this->assertNull($response->getAccessToken());
        $this->assertNull($response->getExpiresIn());
        $this->assertNull($response->getExpires());
    }

    /**
     * @covers ::getExpires
     */
    public function testExpires()
    {
        $data = array(
            'access_token' => 'asdasd',
            'expires_in' => 28800
        );

        $request = new TokenRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new TokenResponse($request, $data, 201);

        $this->assertGreaterThanOrEqual(time() + 28800, $response->getExpires());
    }
}

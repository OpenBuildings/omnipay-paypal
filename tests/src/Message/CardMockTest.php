<?php

namespace Omnipay\PaypalRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Omnipay;
use Omnipay\PaypalRest\Message\CreateCardRequest;
use Omnipay\PaypalRest\Message\UpdateCardRequest;
use Omnipay\PaypalRest\Message\DeleteCardRequest;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 *
 * @coversDefaultClass Omnipay\PaypalRest\Message\PurchaseRequest
 */
class CardMockTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testCreateCard()
    {
        $this->setMockHttpResponse('CreateCardSuccess.http');

        $request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'card' => $this->getValidCard(),
            'payerId' => 'testPayerId',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\CreateCardResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('CARD-2F417848LC187391NKRABZXQ', $response->getTransactionReference());
    }

    /**
     * @coversNothing
     */
    public function testUpdateCard()
    {
        $this->setMockHttpResponse('UpdateCardSuccess.http');

        $request = new UpdateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'card' => $this->getValidCard(),
            'cardReference' => 'CARD-2F417848LC187391NKRABZXQ',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\UpdateCardResponse', $response);
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('CARD-36F38182Y8875171YKRACCUQ', $response->getTransactionReference());
    }

    /**
     * @coversNothing
     */
    public function testDeleteCard()
    {
        $this->setMockHttpResponse('DeleteCardSuccess.http');

        $request = new DeleteCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(array(
            'cardReference' => 'CARD-2F417848LC187391NKRABZXQ',
        ));

        $response = $request->send();

        $this->assertInstanceOf('Omnipay\PaypalRest\Message\DeleteCardResponse', $response);
        $this->assertTrue($response->isSuccessful());
    }
}

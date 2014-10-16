<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Item;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class UpdateCardRequest extends AbstractPaypalRequest
{
    /**
     * @return string
     */
    public function getEndpoint()
    {
        return '/vault/credit-card/'.$this->getCardReference();
    }

    public function getHttpMethod()
    {
        return 'PATCH';
    }

    /**
     * @param  mixed $data
     * @return \Omnipay\PaypalRest\Message\PurchaseResponse
     */
    public function sendData($data)
    {
        $httpResponse = parent::sendData($data);

        return $this->response = new UpdateCardResponse(
            $this,
            $httpResponse->json(),
            $httpResponse->getStatusCode()
        );
    }

    public function getData()
    {
        $this->validate('cardReference');

        return array(
            'path' => '/',
            'op' => 'replace',
            'value' => $this->getPaypalCard()
        );
    }
}

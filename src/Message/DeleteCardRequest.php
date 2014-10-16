<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Item;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class DeleteCardRequest extends AbstractPaypalRequest
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
        return 'DELETE';
    }

    /**
     * @param  mixed $data
     * @return \Omnipay\PaypalRest\Message\DeleteCardResponse
     */
    public function sendData($data)
    {
        $httpResponse = parent::sendData($data);

        return $this->response = new DeleteCardResponse(
            $this,
            $httpResponse->getBody(true) ? $httpResponse->json() : array(),
            $httpResponse->getStatusCode()
        );
    }

    public function getData()
    {
        $this->validate('cardReference');

        return array();
    }
}

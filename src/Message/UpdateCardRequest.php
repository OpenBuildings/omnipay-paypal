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
        $this->validate('cardReference');

        return '/vault/credit-card/'.$this->getCardReference();
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return 'PATCH';
    }

    /**
     * @param  mixed $data
     * @return Omnipay\PaypalRest\Message\UpdateCardResponse
     */
    public function sendData($data)
    {
        $httpResponse = $this->sendHttpRequest($data);

        return $this->response = new UpdateCardResponse(
            $this,
            $httpResponse->json(),
            $httpResponse->getStatusCode()
        );
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('card');
        $card = $this->getCard();

        $cardFields = array(
            'expire_month' => $card->getExpiryMonth(),
            'expire_year' => $card->getExpiryYear(),
            'first_name' => $card->getFirstName(),
            'last_name' => $card->getLastName(),
            'billing_address' => array(
                'line1' => $card->getAddress1(),
                'line2' => $card->getAddress2(),
                'city' => $card->getCity(),
                'state' => $card->getState(),
                'postal_code' => $card->getPostcode(),
                'country_code' => strtoupper($card->getCountry()),
            )
        );

        $data = array();

        foreach ($cardFields as $field => $value) {
            $data []= array(
                'path' => "/$field",
                'op' => 'add',
                'value' => $value
            );
        }

        return $data;
    }
}

<?php

namespace Omnipay\PaypalRest\Message;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const LIVE = 'https://api.paypal.com/v1';
    const SANDBOX = 'https://api.sandbox.paypal.com/v1';

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->getTestMode() ? self::SANDBOX : self::LIVE;
    }

    public function getPaypalCard()
    {
        $this->validate('card');

        $card = $this->getCard();
        $card->validate();

        return array_filter(array(
            'number' => $card->getNumber(),
            'type' => $card->getBrand(),
            'expire_month' => $card->getExpiryMonth(),
            'expire_year' => $card->getExpiryYear(),
            'cvv2' => $card->getCvv(),
            'first_name' => $card->getFirstName(),
            'last_name' => $card->getLastName(),
            'billing_address' => array_filter(array(
                'line1' => $card->getAddress1(),
                'line2' => $card->getAddress2(),
                'city' => $card->getCity(),
                'state' => $card->getState(),
                'postal_code' => $card->getPostcode(),
                'country_code' => strtoupper($card->getCountry()),
            ))
        ));
    }
}

<?php

namespace Omnipay\PaypalRest\Message;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PaymentResponse extends AbstractResponse
{
    /**
     * Return true if state is "approved"
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return (parent::isSuccessful()
            and isset($this->data['state'])
            and $this->data['state'] === 'approved');
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        if (isset($this->data['intent'])
            and $this->data['intent'] === 'sale'
            and isset($this->data['transactions'][0]['related_resources'][0]['sale']['id'])) {

            return $this->data['transactions'][0]['related_resources'][0]['sale']['id'];
        }

        if (isset($this->data['intent'])
            and $this->data['intent'] === 'authorize'
            and isset($this->data['transactions'][0]['related_resources'][0]['authorization']['id'])) {

            return $this->data['transactions'][0]['related_resources'][0]['authorization']['id'];
        }
    }
}

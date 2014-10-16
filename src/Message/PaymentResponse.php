<?php

namespace Omnipay\PaypalRest\Message;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PaymentResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return (parent::isSuccessful() and $this->data['state'] === 'approved');
    }

    public function getTransactionReference()
    {
        if ($this->data['intent'] === 'sale' and isset($this->data['transactions'][0]['related_resources'][0]['sale']['id'])) {
            return $this->data['transactions'][0]['related_resources'][0]['sale']['id'];
        }

        if ($this->data['intent'] === 'authorize' and isset($this->data['transactions'][0]['related_resources'][0]['authorization']['id'])) {
            return $this->data['transactions'][0]['related_resources'][0]['authorization']['id'];
        }
    }
}

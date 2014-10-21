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
     * @param  integer     $index
     * @param  string      $type
     * @return string|null
     */
    public function getRelatedResourceId($index, $type)
    {
        if (isset($this->data['transactions'][0]['related_resources'][$index][$type]['id'])) {
            return $this->data['transactions'][0]['related_resources'][$index][$type]['id'];
        }
    }

    /**
     * @return string|null
     */
    public function getIntent()
    {
        if (isset($this->data['intent'])) {
            return $this->data['intent'];
        }
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        if ($this->getIntent() === 'sale') {
            return $this->getRelatedResourceId(0, 'sale');
        }

        if ($this->getIntent() === 'authorize') {
            return $this->getRelatedResourceId(0, 'authorization');
        }
    }
}

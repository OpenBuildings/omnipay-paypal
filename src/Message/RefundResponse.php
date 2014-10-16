<?php

namespace Omnipay\PaypalRest\Message;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class RefundResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return (parent::isSuccessful() and $this->data['state'] === 'completed');
    }
}

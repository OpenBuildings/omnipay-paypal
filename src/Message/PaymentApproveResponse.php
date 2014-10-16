<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PaymentApproveResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function getLink($rel)
    {
        if (isset($this->data['links'])) {
            foreach ($this->data['links'] as $link) {
                if ($link['rel'] === $rel) {
                    return $link;
                }
            }
        }

        return null;
    }

    public function isSuccessful()
    {
        return (parent::isSuccessful() and $this->data['state'] === 'created');
    }

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        $link = $this->getLink('approval_url');

        return $link['href'];
    }

    /**
     * Get the required redirect method (either GET or POST).
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return array();
    }
}

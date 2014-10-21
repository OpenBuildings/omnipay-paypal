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
    /**
     * @param  string     $rel
     * @return array|null
     */
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

    /**
     * Return true if state is "created"
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return (parent::isSuccessful()
            and isset($this->data['state'])
            and $this->data['state'] === 'created');
    }

    /**
     * Gets the redirect target url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        $link = $this->getLink('approval_url');

        return $link['href'];
    }

    /**
     * Get the required redirect method (either GET or POST).
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * @return array
     */
    public function getRedirectData()
    {
        return array();
    }
}

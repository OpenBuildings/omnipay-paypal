<?php

namespace Omnipay\PaypalRest\Message;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class TokenResponse extends AbstractResponse
{
    /**
     * @return boolean
     */
    public function isSuccessful()
    {
        return (parent::isSuccessful() AND isset($this->data['access_token']));
    }

    /**
     * @return string|null
     */
    public function getTokenType()
    {
        if (isset($this->data['token_type'])) {
            return $this->data['token_type'];
        }
    }

    /**
     * @return string|null
     */
    public function getAccessToken()
    {
        if (isset($this->data['access_token'])) {
            return $this->data['access_token'];
        }
    }

    /**
     * @return string|null
     */
    public function getAppId()
    {
        if (isset($this->data['app_id'])) {
            return $this->data['app_id'];
        }
    }

    /**
     * @return string|null
     */
    public function getExpiresIn()
    {
        if (isset($this->data['expires_in'])) {
            return $this->data['expires_in'];
        }
    }

    /**
     * @return string|null
     */
    public function getExpires()
    {
        if (isset($this->data['expires_in'])) {
            return time() + $this->data['expires_in'];
        }
    }
}

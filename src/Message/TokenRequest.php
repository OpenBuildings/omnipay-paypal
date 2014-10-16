<?php

namespace Omnipay\PaypalRest\Message;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class TokenRequest extends AbstractRequest
{
    /**
     * @return string
     */
    public function getEndpoint()
    {
        return '/oauth2/token';
    }

    /**
     * @param string $value
     */
    public function setClientId($value)
    {
        return $this->setParameter('clientId', $value);
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->getParameter('clientId');
    }

    /**
     * @param string $value
     */
    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function getData()
    {
        return array();
    }

    /**
     * @param  mixed $data
     * @return array
     */
    public function sendData($data)
    {
        $httpRequest = $this->httpClient->post(
            $this->getServer().$this->getEndpoint(),
            array(
                'Accept' => 'application/json',
            ),
            array('grant_type' => 'client_credentials')
        );

        $httpRequest->setAuth($this->getClientId(), $this->getSecret());

        $httpResponse = $httpRequest->send();

        return $this->response = new TokenResponse(
            $this,
            $httpResponse->json(),
            $httpResponse->getStatusCode()
        );
    }
}

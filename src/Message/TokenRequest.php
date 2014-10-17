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

    /**
     * @return array
     */
    public function getData()
    {
        return array();
    }

    /**
     * @return Guzzle\Http\Message\Response
     */
    public function sendHttpRequest()
    {
        return $this->getHttpRequest()->send();
    }

    /**
     * @return Guzzle\Http\Message\Request
     */
    public function getHttpRequest()
    {
        $httpRequest = $this->httpClient->post(
            $this->getServer().$this->getEndpoint(),
            array('Accept' => 'application/json'),
            array('grant_type' => 'client_credentials')
        );

        $httpRequest->setAuth($this->getClientId(), $this->getSecret());

        return $httpRequest;
    }

    /**
     * @param  mixed $data
     * @return array
     */
    public function sendData($data)
    {
        $httpResponse = $this->sendHttpRequest();

        return $this->response = new TokenResponse(
            $this,
            $httpResponse->json(),
            $httpResponse->getStatusCode()
        );
    }
}

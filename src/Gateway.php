<?php

namespace Omnipay\PaypalRest;

use Omnipay\Common\AbstractGateway;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Gateway extends AbstractGateway
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'PaypalRest';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'clientId' => '',
            'secret' => '',
            'token' => '',
            'partnerAttributionId' => '',
        );
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
    public function setClientId($value)
    {
        return $this->setParameter('clientId', $value);
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
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
    public function getPartnerAttributionId()
    {
        return $this->getParameter('partnerAttributionId');
    }

    /**
     * @param string $value
     */
    public function setPartnerAttributionId($value)
    {
        return $this->setParameter('partnerAttributionId', $value);
    }

    public function getTokenExpires()
    {
        return $this->getParameter('tokenExpires');
    }

    public function setTokenExpires($value)
    {
        return $this->setParameter('tokenExpires', $value);
    }

    public function getTokenResponse()
    {
        return $this->token()->send();
    }

    /**
     * @param bool $forceRegenerate [optional] - If there is not an active token present, should we create one?
     * @return string
     */
    public function getToken()
    {
        if (false === $this->hasToken()) {
            $tokenResponse = $this->getTokenResponse();

            if ($tokenResponse->isSuccessful()) {
                $this->setToken($tokenResponse->getAccessToken());
                $this->setTokenExpires($tokenResponse->getExpires());
            }
        }

        return $this->getParameter('token');
    }


    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    /**
     * Is there a bearer token and is it still valid?
     * @return bool
     */
    public function hasToken()
    {
        $token = $this->getParameter('token');
        $expires = $this->getParameter('tokenExpires');

        return (false === empty($token) and time() < $expires);
    }

    public function createRequestWithToken($class, array $parameters)
    {
        $parameters['token'] = $this->getToken();

        return $this->createRequest($class, $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\AuthorizeRequest
     */
    public function token(array $parameters = array())
    {
        return $this->createRequest(__NAMESPACE__.'\Message\TokenRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\PaymentRequest
     */
    public function purchase(array $parameters = array())
    {
        $parameters['intent'] = 'sale';

        return $this->createRequestWithToken(__NAMESPACE__.'\Message\PaymentRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\PaymentCompleteRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\PaymentCompleteRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\PaymentRequest
     */
    public function authorise(array $parameters = array())
    {
        $parameters['intent'] = 'authorize';

        return $this->createRequestWithToken(__NAMESPACE__.'\Message\PaymentRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\PaymentCompleteRequest
     */
    public function completeAuthorise(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\PaymentCompleteRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\CaptureRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\CaptureRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\VoidRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\CreateCardRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\DeleteCardRequest
     */
    public function deleteCard(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\DeleteCardRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\UpdateCardRequest
     */
    public function updateCard(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\UpdateCardRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\Emp\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        if (false === isset($parameters['type'])) {
            $parameters['type'] = 'sale';
        }

        return $this->createRequestWithToken(__NAMESPACE__.'\Message\RefundRequest', $parameters);
    }
}

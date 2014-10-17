<?php

namespace Omnipay\PaypalRest;

use Omnipay\Common\AbstractGateway;
use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

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

    public function __construct(ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        parent::__construct($httpClient, $httpRequest);

        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
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

    /**
     * @param string $value
     * @return self
     */
    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    /**
     * Is there a bearer token and is it still valid?
     *
     * @return bool
     */
    public function hasToken()
    {
        $token = $this->getParameter('token');
        $expires = $this->getParameter('tokenExpires');

        return (false === empty($token) and time() < $expires);
    }

    /**
     * @param  string $class
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\AbstractRequest
     */
    public function createRequestWithToken($class, array $parameters)
    {
        $parameters['token'] = $this->getToken();

        return $this->createRequest($class, $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\TokenRequest
     */
    public function token(array $parameters = array())
    {
        return $this->createRequest(__NAMESPACE__.'\Message\TokenRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\PaymentRequest
     */
    public function purchase(array $parameters = array())
    {
        $parameters['intent'] = 'sale';

        return $this->createRequestWithToken(__NAMESPACE__.'\Message\PaymentRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\PaymentCompleteRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\PaymentCompleteRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\PaymentRequest
     */
    public function authorise(array $parameters = array())
    {
        $parameters['intent'] = 'authorize';

        return $this->createRequestWithToken(__NAMESPACE__.'\Message\PaymentRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\PaymentCompleteRequest
     */
    public function completeAuthorise(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\PaymentCompleteRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\CaptureRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\VoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\VoidRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\CreateCardRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\DeleteCardRequest
     */
    public function deleteCard(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\DeleteCardRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\UpdateCardRequest
     */
    public function updateCard(array $parameters = array())
    {
        return $this->createRequestWithToken(__NAMESPACE__.'\Message\UpdateCardRequest', $parameters);
    }

    /**
     * @param  array  $parameters
     * @return Omnipay\PaypalRest\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        if (false === isset($parameters['type'])) {
            $parameters['type'] = 'sale';
        }

        return $this->createRequestWithToken(__NAMESPACE__.'\Message\RefundRequest', $parameters);
    }
}

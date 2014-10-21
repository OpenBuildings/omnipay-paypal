<?php

namespace Omnipay\PaypalRest\Message;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractPaypalRequest extends AbstractRequest
{
    /**
     * @return string
     */
    abstract public function getEndpoint();

    /**
     * From Official Paypal SDK
     *
     * @return string
     */
    private function generateRequestId()
    {
        static $pid = -1;
        static $addr = -1;

        if ($pid == -1) {
            $pid = getmypid();
        }

        if ($addr == -1) {
            if (array_key_exists('SERVER_ADDR', $_SERVER)) {
                $addr = ip2long($_SERVER['SERVER_ADDR']);
            } else {
                $addr  = php_uname('n');
            }
        }

        return $addr . $pid . $_SERVER['REQUEST_TIME'] . mt_rand(0, 0xffff);
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * @param  array  $parameters
     * @return self
     */
    public function initialize(array $parameters = array())
    {
        parent::initialize($parameters);

        if (null === $this->getRequestId()) {
            $this->setRequestId($this->generateRequestId());
        }

        return $this;
    }

    /**
     * @param string $value
     */
    public function setRequestId($value)
    {
        return $this->setParameter('requestId', $value);
    }

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->getParameter('requestId');
    }

    /**
     * @param string $value
     */
    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->getParameter('token');
    }

    /**
     * @param  array  $data
     * @return \Guzzle\Http\Message\RequestInterface
     */
    public function getHttpRequest(array $data)
    {
        return $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getServer().$this->getEndpoint(),
            array(
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$this->getToken(),
                'Content-type'  => 'application/json',
            ),
            empty($data) ? '{}' : json_encode($data)
        );
    }

    /**
     * @param  array  $data
     * @return \Guzzle\Http\Message\Response
     */
    public function sendHttpRequest(array $data)
    {
        return $this->getHttpRequest($data)->send();
    }
}

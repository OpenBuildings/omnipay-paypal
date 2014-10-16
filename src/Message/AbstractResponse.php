<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{
    protected $statusCode;

    public function __construct(RequestInterface $request, $data, $statusCode = 200)
    {
        parent::__construct($request, $data);

        $this->statusCode = $statusCode;
    }

    public function getTransactionReference()
    {
        if (isset($this->data['id'])) {
            return $this->data['id'];
        }
    }

    /**
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->getCode() < 400;
    }

    public function getStatus()
    {
        if (isset($this->data['status'])) {
            return $this->data['status'];
        }
    }

    public function getMessage()
    {
        $message = null;

        if (isset($this->data['name']) and isset($this->data['message'])) {
            $message = $this->data['name'].': '.$this->data['message'];
        }

        if (isset($this->data['details'])) {
            $message .= ' ['.json_encode($this->data['details']).']';
        }

        return $message;
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->statusCode;
    }
}

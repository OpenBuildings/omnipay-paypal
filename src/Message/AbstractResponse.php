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
    /**
     * @var integer
     */
    protected $code;

    /**
     * @param RequestInterface $request
     * @param array            $data
     * @param integer          $code
     */
    public function __construct(RequestInterface $request, $data, $code = 200)
    {
        parent::__construct($request, $data);

        $this->code = (int) $code;
    }

    /**
     * @return string|null
     */
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

    /**
     * @return string|null
     */
    public function getStatus()
    {
        if (isset($this->data['status'])) {
            return $this->data['status'];
        }
    }

    /**
     * @return string|null
     */
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
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }
}

<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class RefundRequest extends AbstractPaypalRequest
{
    /**
     * @return string
     */
    public function getEndpoint()
    {
        $this->validate('transactionReference', 'type');

        if (false === in_array($this->getType(), array('sale', 'authorization', 'capture'))) {
            throw new InvalidRequestException('Type can only be "sale", "authorization" or "capture"');
        }

        return "/payments/{$this->getType()}/{$this->getTransactionReference()}/refund";
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getParameter('type');
    }

    /**
     * @param string $value
     */
    public function setType($value)
    {
        return $this->setParameter('type', $value);
    }

    /**
     * @param  mixed          $data
     * @return RefundResponse
     */
    public function sendData($data)
    {
        $httpResponse = $this->sendHttpRequest($data);

        return $this->response = new RefundResponse(
            $this,
            $httpResponse->json(),
            $httpResponse->getStatusCode()
        );
    }

    /**
     * @return array
     */
    public function getData()
    {
        if ($this->getAmount()) {
            $this->validate('currency');

            return array(
                'amount' => array(
                    'total' => $this->getAmount(),
                    'currency' => $this->getCurrency(),
                )
            );
        } else {
            return array();
        }
    }
}

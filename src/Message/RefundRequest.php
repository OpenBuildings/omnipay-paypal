<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Item;

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
        return '/payments/'.$this->getType().'/'.$this->getPurchaseId().'/refund';
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function getPurchaseId()
    {
        return $this->getParameter('purchaseId');
    }

    /**
     * @param string $value
     */
    public function setPurchaseId($value)
    {
        return $this->setParameter('purchaseId', $value);
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
     * @param  mixed $data
     * @return \Omnipay\PaypalRest\Message\PurchaseResponse
     */
    public function sendData($data)
    {
        $httpResponse = parent::sendData($data);

        return $this->response = new RefundResponse(
            $this,
            $httpResponse->json(),
            $httpResponse->getStatusCode()
        );
    }

    public function getData()
    {
        $this->validate('purchaseId', 'type');

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

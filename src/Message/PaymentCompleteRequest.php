<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Item;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PaymentCompleteRequest extends AbstractPaypalRequest
{
    /**
     * @return string
     */
    public function getEndpoint()
    {
        $this->validate('purchaseId');

        return '/payments/payment/'.$this->getPurchaseId().'/execute';
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
    public function getPayerId()
    {
        return $this->getParameter('payerId');
    }

    /**
     * @param string $value
     */
    public function setPayerId($value)
    {
        return $this->setParameter('payerId', $value);
    }

    /**
     * @param  mixed $data
     * @return \Omnipay\PaypalRest\Message\PurchaseResponse
     */
    public function sendData($data)
    {
        $httpResponse = parent::sendData($data);

        return $this->response = new PaymentResponse(
            $this,
            $httpResponse->json(),
            $httpResponse->getStatusCode()
        );
    }

    public function getData()
    {
        $this->validate('payerId');

        return array(
            'payer_id' => $this->getPayerId(),
        );
    }
}

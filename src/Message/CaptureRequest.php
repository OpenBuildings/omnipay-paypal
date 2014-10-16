<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Item;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class CaptureRequest extends AbstractPaypalRequest
{
    /**
     * @return string
     */
    public function getEndpoint()
    {
        return '/payments/authorization/'.$this->getPurchaseId().'/capture';
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
    public function getIsFinalCapture()
    {
        return $this->getParameter('isFinalCapture');
    }

    /**
     * @param string $value
     */
    public function setIsFinalCapture($value)
    {
        return $this->setParameter('isFinalCapture', $value);
    }

    /**
     * @param  mixed $data
     * @return \Omnipay\PaypalRest\Message\PurchaseResponse
     */
    public function sendData($data)
    {
        $httpResponse = parent::sendData($data);

        return $this->response = new CaptureResponse(
            $this,
            $httpResponse->json(),
            $httpResponse->getStatusCode()
        );
    }

    public function getData()
    {
        $this->validate('purchaseId', 'amount', 'currency');

        $data = array(
            'amount' => array(
                'total' => $this->getAmount(),
                'currency' => $this->getCurrency(),
            )
        );

        if ($this->getIsFinalCapture()) {
            $data['is_final_capture'] = true;
        }

        return $data;
    }
}

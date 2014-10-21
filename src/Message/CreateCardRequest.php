<?php

namespace Omnipay\PaypalRest\Message;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class CreateCardRequest extends AbstractPaypalRequest
{
    /**
     * @return string
     */
    public function getEndpoint()
    {
        return '/vault/credit-card';
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return 'POST';
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
     * @return CreateCardResponse
     */
    public function sendData($data)
    {
        $httpResponse = $this->sendHttpRequest($data);

        return $this->response = new CreateCardResponse(
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
        $data = $this->getPaypalCard();

        if ($this->getPayerId()) {
            $data['payer_id'] = $this->getPayerId();
        }

        return $data;
    }
}

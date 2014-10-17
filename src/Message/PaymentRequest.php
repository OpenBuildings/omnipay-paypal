<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Item;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\PaypalRest\CreditCard;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PaymentRequest extends AbstractPaypalRequest
{
    /**
     * @return string
     */
    public function getEndpoint()
    {
        return '/payments/payment';
    }

    /**
     * @return string
     */
    public function getIntent()
    {
        return $this->getParameter('intent');
    }

    /**
     * @param string $value
     */
    public function setIntent($value)
    {
        return $this->setParameter('intent', $value);
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
     * @return Omnipay\PaypalRest\Message\PaymentResponse
     */
    public function sendData($data)
    {
        $httpResponse = $this->sendHttpRequest($data);

        if ($this->getRequiredRedirect()) {
            return $this->response = new PaymentApproveResponse(
                $this,
                $httpResponse->json(),
                $httpResponse->getStatusCode()
            );
        } else {
            return $this->response = new PaymentResponse(
                $this,
                $httpResponse->json(),
                $httpResponse->getStatusCode()
            );
        }
    }

    /**
     * @return array
     */
    public function getTransactionData()
    {
        $this->validate('currency', 'amount');

        $currency = $this->getCurrency();
        $item_list = array();

        if ($this->getItems()) {
            foreach ($this->getItems() as $item) {
                $item_list['items'][] = array(
                    'name' => mb_strimwidth($item->getName(), 0, 126, '…'),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getPrice(),
                    'description' => mb_strimwidth($item->getDescription(), 0, 126, '…'),
                    'currency' => $currency,
                );
            }
        }

        return array(
            'transactions' => array(
                array_filter(array(
                    'description' => $this->getDescription(),
                    'amount' => array(
                        'total' => $this->getAmount(),
                        'currency' => $currency,
                    ),
                    'item_list' => $item_list
                ))
            ),
        );
    }

    /**
     * @return array
     */
    public function getPayerPaypalData()
    {
        $this->validate('returnUrl', 'cancelUrl');

        return array(
            'payer' => array(
                'payment_method' => 'paypal',
            ),
            'redirect_urls' => array(
                'return_url' => $this->getReturnUrl(),
                'cancel_url' => $this->getCancelUrl(),
            ),
        );
    }

    /**
     * @return array
     */
    public function getPayerCardReferenceData()
    {
        $this->validate('cardReference');

        return array(
            'payer' => array(
                'payment_method' => 'credit_card',
                'funding_instruments' => array(
                    array(
                        'credit_card_token' => array_filter(array(
                            'credit_card_id' => $this->getCardReference(),
                            'payer_id' => $this->getPayerId()
                        )),
                    ),
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getPayerCardData()
    {
        return array(
            'payer' => array(
                'payment_method' => 'credit_card',
                'funding_instruments' => array(
                    array(
                        'credit_card' => $this->getPaypalCard()
                    ),
                ),
            ),
        );
    }

    /**
     * @return boolean
     */
    public function getRequiredRedirect()
    {
        return false === ($this->getCardReference() or $this->getCard());
    }

    /**
     * @return array
     */
    public function getPayerData()
    {
        if ($this->getCardReference()) {
            return $this->getPayerCardReferenceData();
        } elseif ($this->getCard()) {
            return $this->getPayerCardData();
        } else {
            return $this->getPayerPaypalData();
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('intent');

        if (false === in_array($this->getIntent(), array('sale', 'authorize'))) {
            throw new InvalidRequestException('Intent can only be "sale" or "authorize"');
        }

        return array_merge_recursive(
            array('intent' => $this->getIntent()),
            $this->getTransactionData(),
            $this->getPayerData()
        );
    }
}

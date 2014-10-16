<?php

namespace Omnipay\PaypalRest\Message;

use Omnipay\Common\Item;
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
     * @return \Omnipay\PaypalRest\Message\PurchaseResponse
     */
    public function sendData($data)
    {
        $httpResponse = parent::sendData($data);

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
                        ))
                    )
                ),
            )
        );
    }

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
            )
        );
    }

    public function getRequiredRedirect()
    {
        return false === ($this->getCardReference() or $this->getCard());
    }

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

    public function getData()
    {
        $this->validate('intent');

        return array_merge_recursive(
            array('intent' => $this->getIntent()),
            $this->getTransactionData(),
            $this->getPayerData()
        );
    }
}

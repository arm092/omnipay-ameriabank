<?php

namespace Omnipay\Ameria\Message\Request;

/**
 * Class ConfirmPaymentRequest
 *
 * @package Omnipay\Ameria\Message
 */
class ConfirmPaymentRequest extends AbstractRequest
{
    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('transactionId', 'amount');

        $data = [];

        $data['PaymentID'] = $this->getTransactionId();

        if ($this->getAmount()) {
            $data['Amount'] = $this->getAmount();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getUrl().'/ConfirmPayment';
    }
}

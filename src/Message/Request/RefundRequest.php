<?php

namespace Omnipay\Ameria\Message\Request;

/**
 * Class RefundRequest
 *
 * @package Omnipay\Ameria\Message
 */
class RefundRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('transactionId', 'amount');

        $data = parent::getData();

        $data['PaymentID'] = $this->getTransactionId();
        $data['Amount']  = $this->getAmount();

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getUrl().'/RefundPayment';
    }
}

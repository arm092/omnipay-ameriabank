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

        $data['orderId'] = $this->getTransactionId();
        $data['amount']  = $this->getAmountInteger();

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getUrl().'/refund.do';
    }
}

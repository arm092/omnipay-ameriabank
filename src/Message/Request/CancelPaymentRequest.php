<?php

namespace Omnipay\Ameria\Message\Request;

/**
 * Class CancelPaymentRequest
 *
 * @package Omnipay\Ameria\Message
 */
class CancelPaymentRequest extends AbstractRequest
{
    /**
     * Prepare data to send
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('transactionId');

        $data              = parent::getData();
        $data['PaymentID'] = $this->getTransactionId();

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getUrl().'/CancelPayment';
    }
}

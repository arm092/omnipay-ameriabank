<?php

namespace Omnipay\Ameria\Message\Request;

/**
 * Class GetOrderStatusExtendedRequest
 * @package Omnipay\Ameria\Message
 */
class GetPaymentDetailsRequest extends AbstractRequest
{
    /**
     * Prepare data to send
     *
     * @return array|mixed
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData() : array
    {
        $this->validate('transactionId');

        $data = parent::getData();

        $data['PaymentID'] = $this->getTransactionId();

        if ($this->getLanguage()) {
            $data['language'] = $this->getLanguage();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint() : string
    {
        return $this->getUrl() . '/GetPaymentDetails';
    }
}

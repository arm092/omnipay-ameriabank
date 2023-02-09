<?php

namespace Omnipay\Ameria\Message\Request;

/**
 * Class ReverseRequest
 * @package Omnipay\Arca\Message
 */
class ReverseRequest extends AbstractRequest
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

        $data['orderId'] = $this->getTransactionId();

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint() : string
    {
        return $this->getUrl() . '/reverse.do';
    }
}

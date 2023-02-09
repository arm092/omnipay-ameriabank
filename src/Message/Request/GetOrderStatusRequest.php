<?php

namespace Omnipay\Ameria\Message\Request;

/**
 * Class GetOrderStatusRequest
 * @package Omnipay\Arca\Message
 */
class GetOrderStatusRequest extends AbstractRequest
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
        return $this->getUrl() . '/getOrderStatus.do';
    }
}

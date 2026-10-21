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
        $this->validatePaymentId();

        $data              = $this->getAuthenticationData();
        $data['PaymentID'] = $this->getPaymentId();

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

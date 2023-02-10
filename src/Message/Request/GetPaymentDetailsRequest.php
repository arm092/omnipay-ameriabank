<?php

namespace Omnipay\Ameria\Message\Request;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Ameria\Message\Response\GetPaymentDetailsResponse;

/**
 * Class GetPaymentDetailsRequest
 *
 * @package Omnipay\Ameria\Message
 */
class GetPaymentDetailsRequest extends AbstractRequest
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
        return $this->getUrl().'/GetPaymentDetails';
    }

    protected function createResponse(string $data, array $headers = []): ResponseInterface
    {
        return $this->response = new GetPaymentDetailsResponse($this, $data, $headers);
    }
}

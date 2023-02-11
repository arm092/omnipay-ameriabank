<?php

namespace Omnipay\Ameria\Message\Request;

use Omnipay\Ameria\Message\Response\AbstractResponse;
use Omnipay\Ameria\Message\Response\GetPaymentDetailsResponse;

/**
 * @package Omnipay\Ameria\Message\Request
 *
 * @method BindingPaymentResponse send
 */
class BindingPaymentRequest extends AbstractBindingAwareRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     */
    public function getData(): array
    {
        $data = parent::getData();

        $data['CardHolderID'] = $this->getCardHolderId();
        if ($this->getCurrency()) {
            $data['Currency'] = str_pad($this->getCurrencyNumeric(), 3, 0, STR_PAD_LEFT);
        }
        $data['Description'] = $this->getDescription();
        $data['OrderId']     = $this->getTransactionId();
        $data['Amount']      = $this->getAmountInteger();
        $data['BackURL']     = $this->getReturnUrl();
        if ($this->getOpaque()) {
            $data['Opaque'] = $this->getOpaque();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getUrl().'/MakeBindingPayment';
    }

    protected function createResponse(string $data, array $headers = []): AbstractResponse
    {
        return $this->response = new GetPaymentDetailsResponse($this, $data, $headers);
    }
}

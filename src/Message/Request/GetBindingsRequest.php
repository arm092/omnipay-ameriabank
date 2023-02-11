<?php

namespace Omnipay\Ameria\Message\Request;

use Omnipay\Ameria\Message\Response\AbstractResponse;
use Omnipay\Ameria\Message\Response\BindingsResponse;
use Omnipay\Ameria\Message\Response\GetPaymentDetailsResponse;

class GetBindingsRequest extends AbstractBindingAwareRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     */
    public function getData(): array
    {
        $data                = parent::getData();
        $data['Username']    = $this->getUsername();
        $data['PaymentType'] = 6; //Binding

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getUrl().'/GetBindings';
    }

    protected function createResponse(string $data, array $headers = []): AbstractResponse
    {
        return $this->response = new BindingsResponse($this, $data, $headers);
    }
}

<?php

namespace Omnipay\Ameria\Message\Request;

/**
 * @package Omnipay\Ameria\Message\Request
 *
 * @method BindingPaymentResponse send
 */
class ActivateBindingRequest extends AbstractBindingAwareRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     */
    public function getData(): array
    {
        $data                 = parent::getData();
        $data['CardHolderID'] = $this->getCardHolderId();

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getUrl().'/ActivateBinding';
    }
}

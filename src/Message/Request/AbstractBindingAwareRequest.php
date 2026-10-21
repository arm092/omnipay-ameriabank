<?php

namespace Omnipay\Ameria\Message\Request;

abstract class AbstractBindingAwareRequest extends AbstractRequest
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
        if ($this->getBindingUsername()) {
            $data['Username'] = $this->getBindingUsername();
        }
        $data['PaymentType'] = 6; //Binding

        return $data;
    }
}

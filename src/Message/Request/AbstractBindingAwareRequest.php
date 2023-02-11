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

    /**
     * Unique ID for binding transactions (is used when needs to do card binding, in other cases it is not required)
     *
     * @return string
     */
    public function getCardHolderId(): string
    {
        return $this->getParameter('card_holder_id');
    }

    /**
     * Set the unique id for card holder
     *
     * @param  string  $value
     *
     * @return $this
     */
    public function setCardHolderId(string $value): AbstractRequest
    {
        return $this->setParameter('card_holder_id', $value);
    }
}

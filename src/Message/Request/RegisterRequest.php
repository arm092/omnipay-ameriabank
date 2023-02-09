<?php

namespace Omnipay\Ameria\Message\Request;

/**
 * Class RegisterRequest
 * @package Omnipay\Arca\Message
 */
class RegisterRequest extends AbstractRequest
{
    /**
     * @return mixed
     */
    public function getTimeout()
    {
        return $this->getParameter('sessionTimeoutSecs');
    }

    /**
     * Set the request sessionTimeoutSecs.
     *
     * @param string $value < 1200 Second (20minute)
     *
     * @return $this
     */
    public function setTimeout($value)
    {
        return $this->setParameter('sessionTimeoutSecs', $value);
    }

    /**
     * Prepare data to send
     *
     * @return array|mixed
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData() : array
    {
        $this->validate('transactionId', 'amount', 'returnUrl');

        $data = parent::getData();
        if ($this->getCurrency()) {
            $data['Currency'] = str_pad($this->getCurrencyNumeric(), 3, 0, STR_PAD_LEFT);
        }
        $data['Description'] = $this->getDescription();
        $data['OrderId'] = $this->getTransactionId();
        $data['Amount'] = $this->getAmountInteger();
        $data['BackURL'] = $this->getReturnUrl();
        if ($this->getJsonParams()) {
            $data['Opaque'] = $this->getJsonParams();
//        $data['Opaque'] = json_encode(["FORCE_3DS2" => true]);
        }
        if ($this->getCardHolderId()) {
            $data['CardHolderID'] = $this->getCardHolderId();
        }
        if ($this->getTimeout()) {
            $data['Timeout'] = $this->getTimeout();
        }
        if ($this->getLanguage()) {
            $data['language'] = $this->getLanguage();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getUrl() . '/InitPayment';
    }
}

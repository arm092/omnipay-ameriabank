<?php

namespace Omnipay\Ameria\Message\Request;

/**
 * @package Omnipay\Ameria\Message\Request
 *
 * @method BindingPaymentResponse send
 */
class DeactivateBindingRequest extends ActivateBindingRequest
{
    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getUrl().'/DeactivateBinding';
    }
}

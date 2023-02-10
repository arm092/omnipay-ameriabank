<?php

namespace Omnipay\Ameria\Message\Response;

class BindingPaymentResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->isNotError();
    }
}

<?php

namespace Omnipay\Ameria\Message\Response;

/**
 * Arca Response.
 *
 * This is the response class for all Arca requests.
 *
 * @see \Omnipay\Ameria\Gateway
 */
class BindingsResponse extends CommonResponse
{
    /**
     * Get Binded cards list
     */
    public function getCardBindings(): array
    {
        return $this->data['CardBindingFileds'] ?? []; //ToDo: Check can be typo
    }
}

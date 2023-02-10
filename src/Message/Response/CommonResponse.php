<?php

namespace Omnipay\Ameria\Message\Response;

/**
 * Arca Response.
 *
 * This is the response class for all Arca requests.
 *
 * @see \Omnipay\Ameria\Gateway
 */
class CommonResponse extends AbstractResponse
{
    public const NO_ERROR   = '00';

    public function getRequestId(): ?string
    {
        if (isset($this->headers['Request-Id'])) {
            return $this->headers['Request-Id'][0];
        }

        return null;
    }
}

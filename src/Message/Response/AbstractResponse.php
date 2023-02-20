<?php

namespace Omnipay\Ameria\Message\Response;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\AbstractResponse as OmnipayAbstractResponse;
use Omnipay\Common\Message\RequestInterface;

abstract class AbstractResponse extends OmnipayAbstractResponse implements RedirectResponseInterface
{
    public const NO_ERROR   = '00';
    public const DEPOSITED  = 2;
    public const AUTHORIZED = 1;
    protected ?string $requestId = null;
    protected array   $headers   = [];

    public function __construct(RequestInterface $request, string $data, array $headers = [])
    {
        parent::__construct($request, $data);

        $this->request = $request;
        $this->data    = json_decode($data, true);
        $this->headers = $headers;
    }

    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->data['ResponseMessage']
            ?? $this->data['Description']
            ?? null;
    }

    /**
     * Get the error code from the response.
     */
    public function getCode(): ?string
    {
        return $this->data['ResponseCode'] ?? $this->data['resposneCode'] ?? null;
    }

    public function getOpaque(): ?string
    {
        return $this->data['Opaque'] ?? null;
    }

    /**
     * Is the transaction successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        if (method_exists(static::class, 'getOrderStatus')) {
            return $this->isCompleted() && $this->isNotError();
        }

        return $this->isNotError();
    }

    /**
     * Is the response has no error
     */
    public function isNotError(): bool
    {
        return $this->getCode() === static::NO_ERROR;
    }

    /**
     * Is the orderStatus completed
     */
    public function isCompleted(): bool
    {
        return $this->getOrderStatus() == self::DEPOSITED;
    }
}

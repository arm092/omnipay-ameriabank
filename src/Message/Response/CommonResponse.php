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
    protected string $endpoint = 'https://servicestest.ameriabank.am/VPOS';

    /**
     * Get response redirect url
     *
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->endpoint.'/Pay?'.http_build_query([
                'id'   => $this->data['PaymentId'],
                'lang' => $this->data['Language'],
            ]);
    }

    /**
     * Set interface language
     *
     * @param  string  $value
     *
     * @return AbstractResponse
     */
    public function setLanguage(string $value): AbstractResponse
    {
        $this->data['Language'] = $value;

        return $this;
    }

    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        if (isset($this->data['orderID'])) {
            return $this->data['orderID'];
        }
        if (isset($this->data['OrderID'])) {
            return $this->data['OrderID'];
        }

        if (isset($this->orderId)) {
            return $this->orderId;
        }

        return null;
    }

    /**
     * Get the orderNumber reference.
     *
     * @return mixed
     */
    public function getOrderNumberReference(): mixed
    {
        if (isset($this->data['MDOrderID'])) {
            return $this->data['MDOrderID'];
        }

        return null;
    }

    /**
     * Get the orderStatus.
     *
     * @return |null
     */
    public function getOrderStatus()
    {
        if (isset($this->data['OrderStatus'])) {
            return $this->data['OrderStatus'];
        }

        return null;
    }

    /**
     * Get the action code description from the response.
     *
     * @return string|null
     */
    public function getActionCodeDescription(): ?string
    {
        if (isset($this->data['actionCodeDescription'])) {
            return $this->data['actionCodeDescription'];
        }

        return null;
    }

    /**
     * Get binding id of customer.
     *
     * @return string|null
     */
    public function getBindingId(): ?string
    {
        if (isset($this->data['BindingID'])) {
            return $this->data['BindingID'];
        }

        return null;
    }

    /**
     * Get customer card information like last four digits, expiration date.
     *
     * @return array
     */
    public function getCardAuthInfo(): array
    {
        if (isset($this->data['CardHolderID'])) {
            return $this->data['CardHolderID'];
        }

        return [];
    }

    /**
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        if (isset($this->headers['Request-Id'])) {
            return $this->headers['Request-Id'][0];
        }

        return null;
    }
}

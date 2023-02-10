<?php

namespace Omnipay\Ameria\Message\Response;

/**
 * Arca Response.
 *
 * This is the response class for GetPaymentDetails request
 *
 * @see \Omnipay\Ameria\Gateway
 */
class GetPaymentDetailsResponse extends AbstractResponse
{
    /**
     * Get the Unique ID|code of the transaction
     *
     * @return null|int|string
     */
    public function getTransactionReference()
    {
        return $this->data['orderID'] ?? $this->data['OrderID'] ?? $this->data['rrn'] ?? null;
    }

    /**
     * Get the Payment system identifier
     */
    public function getOrderNumberReference(): ?string
    {
        return $this->data['MDOrderID'] ?? null;
    }

    /**
     * Get the payment state code
     */
    public function getOrderStatus(): ?int
    {
        return $this->data['OrderStatus'] ?? null;
    }

    /**
     * Get the action code from the response.
     */
    public function getActionCode(): ?string
    {
        return $this->data['ActionCode'] ?? null;
    }

    /**
     * Get binding Binding identifier
     */
    public function getBindingId(): ?string
    {
        return $this->data['BindingID'] ?? null;
    }

    /**
     * Get Unique ID for binding transactions
     */
    public function getCardHolderId(): ?string
    {
        return $this->data['CardHolderID'] ?? null;
    }

    /**
     * Get customer card information like last four digits, expiration date.
     *
     * @return array
     */
    public function getCardAuthInfo(): array
    {
        return [
            'CardNumber' => $this->data['CardNumber'] ?? null,
            'ClientName' => $this->data['ClientName'] ?? null,
            'ExpDate'    => $this->data['ExpDate'] ?? null,
        ];
    }

    public function getRequestId(): ?string
    {
        if (isset($this->headers['Request-Id'])) {
            return $this->headers['Request-Id'][0];
        }

        return null;
    }
}

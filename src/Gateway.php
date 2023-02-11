<?php

namespace Omnipay\Ameria;

use Omnipay\Ameria\Message\Request\BindingPaymentRequest;
use Omnipay\Ameria\Message\Request\ConfirmPaymentRequest;
use Omnipay\Ameria\Message\Request\GetBindingsRequest;
use Omnipay\Ameria\Message\Request\ActivateBindingRequest;
use Omnipay\Ameria\Message\Request\GetPaymentDetailsRequest;
use Omnipay\Ameria\Message\Request\RefundRequest;
use Omnipay\Ameria\Message\Request\InitPaymentRequest;
use Omnipay\Ameria\Message\Request\CancelPaymentRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Ameria\Message\Request\DeactivateBindingRequest;

/**
 * Ameria Gateway
 *
 * @method RequestInterface updateCard(array $options = [])
 * @method NotificationInterface acceptNotification(array $options = [])
 * @method RequestInterface fetchTransaction(array $options = [])
 */
class Gateway extends AbstractGateway
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Ameria';
    }

    /**
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return [
            'client_id' => '',
            'username'  => '',
            'password'  => '',
        ];
    }

    /**
     * Get account client_id.
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->getParameter('client_id');
    }

    /**
     * Set account client_id.
     *
     * @param $value
     *
     * @return $this
     */
    public function setClientId($value): Gateway
    {
        return $this->setParameter('client_id', $value);
    }

    /**
     * Get account login.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getParameter('username');
    }

    /**
     * Set account login.
     *
     * @param $value
     *
     * @return $this
     */
    public function setUsername($value): Gateway
    {
        return $this->setParameter('username', $value);
    }

    /**
     * Get account username for binding payments.
     *
     * @return string|null
     */
    public function getBindingUsername(): ?string
    {
        return $this->getParameter('bindingUsername');
    }

    /**
     * Set account username for binding payments.
     *
     * @param  string|null  $value
     *
     * @return \Omnipay\Ameria\Gateway
     */
    public function setBindingUsername(?string $value): Gateway
    {
        return $this->setParameter('bindingUsername', $value);
    }

    /**
     * Get account password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->getParameter('password');
    }

    /**
     * Set account password.
     *
     * @param $value
     *
     * @return $this
     */
    public function setPassword($value): Gateway
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Create Purchase Request.
     */
    public function purchase(array $options = []): AbstractRequest
    {
        return $this->createRequest(InitPaymentRequest::class, $options);
    }

    /**
     * Create Complete Purchase Request.
     */
    public function completePurchase(array $options = []): AbstractRequest
    {
        return $this->getPaymentDetails($options);
    }

    /**
     * Create RegisterPreAuth Request.
     */
    public function authorize(array $options = []): AbstractRequest
    {
        return $this->createRequest(InitPaymentRequest::class, $options);
    }

    /**
     * Create getOrderStatusExtended Request.
     */
    public function getPaymentDetails(array $options = []): AbstractRequest
    {
        return $this->createRequest(GetPaymentDetailsRequest::class, $options);
    }

    /**
     * Create Deposit Request.
     */
    public function capture(array $options = []): AbstractRequest
    {
        return $this->createRequest(ConfirmPaymentRequest::class, $options);
    }

    /**
     * Create Reverse Request.
     */
    public function void(array $options = []): AbstractRequest
    {
        return $this->createRequest(CancelPaymentRequest::class, $options);
    }

    /**
     * Create Refund Request.
     */
    public function refund(array $options = []): AbstractRequest
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    public function createCard(array $options = []): AbstractRequest
    {
        return $this->createRequest(BindingPaymentRequest::class, $options);
    }

    public function getBindings(array $options = []): AbstractRequest
    {
        return $this->createRequest(GetBindingsRequest::class, $options);
    }

    public function enableCard(array $options = []): AbstractRequest
    {
        return $this->createRequest(ActivateBindingRequest::class, $options);
    }

    public function disableCard(array $options = []): AbstractRequest
    {
        return $this->createRequest(DeactivateBindingRequest::class, $options);
    }
}

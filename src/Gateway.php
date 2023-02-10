<?php

namespace Omnipay\Ameria;

use Omnipay\Ameria\Message\Request\BindingPaymentRequest;
use Omnipay\Ameria\Message\Request\ConfirmPaymentRequest;
use Omnipay\Ameria\Message\Request\GetBindingsRequest;
use Omnipay\Ameria\Message\Request\GetPaymentDetailsRequest;
use Omnipay\Ameria\Message\Request\GetOrderStatusRequest;
use Omnipay\Ameria\Message\Request\RefundRequest;
use Omnipay\Ameria\Message\Request\RegisterPreAuthRequest;
use Omnipay\Ameria\Message\Request\InitPaymentRequest;
use Omnipay\Ameria\Message\Request\ReverseRequest;
use Omnipay\Ameria\Message\Request\VerifyEnrollmentRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Arca Gateway
 *
 * @method RequestInterface authorize(array $options = [])
 * @method RequestInterface completeAuthorize(array $options = [])
 * @method RequestInterface capture(array $options = [])
 * @method RequestInterface void(array $options = [])
 * @method RequestInterface createCard(array $options = [])
 * @method RequestInterface updateCard(array $options = [])
 * @method RequestInterface deleteCard(array $options = [])
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
     *
     * @param  array  $options
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function purchase(array $options = []): AbstractRequest
    {
        return $this->createRequest(InitPaymentRequest::class, $options);
    }

    /**
     * Create Complete Purchase Request.
     *
     * @param  array  $options
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function completePurchase(array $options = []): AbstractRequest
    {
        return $this->getPaymentDetails($options);
    }

    /**
     * Create RegisterPreAuth Request.
     *
     * @param  array  $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function registerPreAuth(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(RegisterPreAuthRequest::class, $parameters);
    }

    /**
     * Create getOrderStatusExtended Request.
     *
     * @param  array  $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function getPaymentDetails(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(GetPaymentDetailsRequest::class, $parameters);
    }

    /**
     * Create verifyEnrollment Request.
     *
     * @param  array  $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function verifyEnrollment(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(VerifyEnrollmentRequest::class, $parameters);
    }

    /**
     * Create Deposit Request.
     *
     * @param  array  $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function deposit(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(ConfirmPaymentRequest::class, $parameters);
    }

    /**
     * Create Reverse Request.
     *
     * @param  array  $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function reverse(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(ReverseRequest::class, $parameters);
    }

    /**
     * Create Refund Request.
     *
     * @param  array  $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function refund(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(RefundRequest::class, $parameters);
    }

    /**
     * @param  array  $parameters
     *
     * @return \Omnipay\Ameria\Message\Request\BindingPaymentRequest|AbstractRequest
     */
    public function bindingPayment(array $parameters = []): BindingPaymentRequest
    {
        return $this->createRequest(BindingPaymentRequest::class, $parameters);
    }

    /**
     * @param  array  $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function getBindings(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(GetBindingsRequest::class, $parameters);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = [])
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
    }
}

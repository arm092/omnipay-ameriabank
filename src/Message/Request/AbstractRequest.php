<?php

namespace Omnipay\Ameria\Message\Request;

use Omnipay\Ameria\Message\Response\CommonResponse;
use Omnipay\Common\Message\AbstractRequest as CommonAbstractRequest;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class AbstractRequest
 *
 * @package Omnipay\Ameria\Message
 */
abstract class AbstractRequest extends CommonAbstractRequest
{
    /**
     * Live Endpoint URL.
     *
     * @var string URL
     */
    protected string $endpoint = 'https://services.ameriabank.am/VPOS/api/VPOS';

    /**
     * Test Endpoint URL.
     *
     * @var string
     */
    protected string $testEndpoint = 'https://servicestest.ameriabank.am/VPOS/api/VPOS';

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->getParameter('client_id');
    }

    /**
     * Set merchant id
     *
     * @param $value
     *
     * @return $this
     */
    public function setClientId($value): AbstractRequest
    {
        return $this->setParameter('client_id', $value);
    }

    /**
     * @return mixed
     */
    public function getUsername()
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
    public function setUsername($value): AbstractRequest
    {
        return $this->setParameter('username', $value);
    }

    /**
     * @return string
     */
    public function getBindingUsername(): ?string
    {
        return $this->getParameter('bindingUsername');
    }

    /**
     * Set account login.
     *
     * @param $value
     *
     * @return $this
     */
    public function setBindingUsername($value): AbstractRequest
    {
        return $this->setParameter('bindingUsername', $value);
    }

    /**
     * Set account password.
     *
     * @return mixed
     */
    public function getPassword()
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
    public function setPassword($value): AbstractRequest
    {
        return $this->setParameter('password', $value);
    }

    abstract public function getEndpoint();

    public function getPaymentId(): ?string
    {
        $value = $this->getTransactionReference() ?: $this->getTransactionId();

        return $value === null ? null : (string) $value;
    }

    protected function validatePaymentId(): void
    {
        if ($this->getPaymentId() === null || $this->getPaymentId() === '') {
            throw new InvalidRequestException('The transactionReference parameter is required (bank PaymentID).');
        }
    }

    public function getCardHolderId(): ?string
    {
        return $this->getParameter('card_holder_id');
    }

    public function setCardHolderId(string $value): AbstractRequest
    {
        return $this->setParameter('card_holder_id', $value);
    }

    /**
     * Get url. Depends on  test mode.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->endpoint;
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getOpaque()
    {
        return $this->getParameter('opaque');
    }

    /**
     * Set the request jsonParams.
     * Fields of additional information
     *
     * @param  string  $value
     *
     * @return $this
     */
    public function setOpaque(string $value): AbstractRequest
    {
        return $this->setParameter('opaque', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data): ResponseInterface
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $body = $data ? json_encode($data, JSON_THROW_ON_ERROR) : null;

        $httpResponse = $this->httpClient->request($this->getHttpMethod(), $this->getEndpoint(), $headers, $body);

        return $this->createResponse($httpResponse->getBody()->getContents(), $httpResponse->getHeaders());
    }

    /**
     * @param  string  $data
     * @param  array  $headers
     *
     * @return CommonResponse
     */
    protected function createResponse(string $data, array $headers = []): ResponseInterface
    {
        return $this->response = new CommonResponse($this, $data, $headers);
    }

    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('client_id');

        return ['ClientID' => $this->getClientId()] + $this->getAuthenticationData();
    }

    protected function getAuthenticationData(): array
    {
        $this->validate('username', 'password');

        return [
            'Username' => $this->getUsername(),
            'Password' => $this->getPassword(),
        ];
    }
}

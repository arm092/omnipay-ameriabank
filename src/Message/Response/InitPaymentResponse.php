<?php

namespace Omnipay\Ameria\Message\Response;

/**
 * Ameria InitPayment Response.
 *
 * This is the response class for all Arca requests.
 *
 * @see \Omnipay\Ameria\Gateway
 */
class InitPaymentResponse extends AbstractResponse
{
    public const NO_ERROR = 1;
    protected string $endpoint     = 'https://services.ameriabank.am/VPOS/Payments';
    protected string $testEndpoint = 'https://servicestest.ameriabank.am/VPOS/Payments';

    /**
     * Get response redirect url
     */
    public function getRedirectUrl(): string
    {
        return $this->getEndpoint().'?'.http_build_query([
                'id'   => $this->data['PaymentID'],
                'lang' => $this->getLanguage(),
            ]);
    }

    public function getEndpoint(): string
    {
        return ($this->getTestMode() ? $this->testEndpoint : $this->endpoint).'/Pay';
    }

    /**
     * Get interface language
     */
    public function getLanguage(): string
    {
        return $this->data['Language'] ?? 'en';
    }

    /**
     * Set interface language
     */
    public function setLanguage(string $value): AbstractResponse
    {
        $this->data['Language'] = $value;

        return $this;
    }

    /**
     * Get test mode
     */
    public function getTestMode(): bool
    {
        return $this->data['TestMode'] ?? false;
    }

    /**
     * Set test mode (true for enabling)
     */
    public function setTestMode(bool $value): AbstractResponse
    {
        $this->data['TestMode'] = $value;

        return $this;
    }

    public function getRequestId(): ?string
    {
        if (isset($this->headers['Request-Id'])) {
            return $this->headers['Request-Id'][0];
        }

        return null;
    }

    /**
     * Is the response has no error
     */
    public function isNotError(): bool
    {
        return isset($this->data['PaymentID']) && $this->getCode() == static::NO_ERROR;
    }

    public function isRedirect(): bool
    {
        return true;
    }
}

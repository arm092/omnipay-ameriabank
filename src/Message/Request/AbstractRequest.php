<?php

namespace Omnipay\Ameria\Message\Request;

use Omnipay\Ameria\Message\Response\CommonResponse;
use Omnipay\Common\Message\AbstractRequest as CommonAbstractRequest;
use Omnipay\Common\Message\ResponseInterface;

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
    public function getJsonParams()
    {
        return $this->getParameter('jsonParams');
    }

    /**
     * Set the request jsonParams.
     * Fields of additional information
     *
     * @param  string  $value
     *
     * @return $this
     */
    public function setJsonParams(string $value): AbstractRequest
    {
        return $this->setParameter('jsonParams', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data): ResponseInterface
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $body = $data ? http_build_query($data, '', '&') : null;

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
        $this->validate('client_id', 'username', 'password');

        return [
            'ClientID' => $this->getClientId(),
            'Username' => $this->getUsername(),
            'Password' => $this->getPassword(),
        ];
    }
}

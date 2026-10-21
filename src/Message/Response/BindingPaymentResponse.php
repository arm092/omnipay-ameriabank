<?php

namespace Omnipay\Ameria\Message\Response;

class BindingPaymentResponse extends GetPaymentDetailsResponse
{
    public function isRedirect(): bool
    {
        return !empty($this->data['AcsUrl'])
            && !empty($this->data['PaReq'])
            && !empty($this->data['TermUrl']);
    }

    public function isSuccessful(): bool
    {
        return !$this->isRedirect() && parent::isSuccessful();
    }

    public function getRedirectUrl(): ?string
    {
        return $this->isRedirect() ? $this->data['AcsUrl'] : null;
    }

    public function getRedirectMethod(): string
    {
        return 'POST';
    }

    public function getRedirectData(): array
    {
        if (!$this->isRedirect()) {
            return [];
        }

        $data = ['PaReq' => $this->data['PaReq'], 'TermUrl' => $this->data['TermUrl']];
        if (isset($this->data['MD'])) {
            $data['MD'] = $this->data['MD'];
        }

        return $data;
    }
}

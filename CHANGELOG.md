# Changelog

## 2.0.0

### Migration from 1.x

- InitPayment and MakeBindingPayment now send major currency units, matching
  ConfirmPayment and RefundPayment. A value of `10.25` means 10.25 AMD, not 1025
  AMD. Remove application workarounds for the old scaling before upgrading.
- InitPaymentResponse::isSuccessful() now returns false because registration is
  not proof of payment. Use isRedirect() for hosted checkout, then verify payment
  through completePurchase().
- Details responses return bank PaymentID through getTransactionReference(),
  retaining the requested ID when GetPaymentDetails omits it. Read merchant
  OrderID through getTransactionId(). Follow-up operations accept the standard
  transactionReference parameter; the legacy transactionId alias still works.
- Malformed JSON and invalid request encodings now throw exceptions.
- Public response return types have been clarified, including nullable redirect
  URLs. Review custom subclasses when upgrading.

### Fixes and additions

- Capture includes Username and Password. Follow-up operations use the documented
  authentication fields and do not require ClientID.
- Test redirects inherit the initiating request's test mode.
- Failed registration cannot redirect to a payment page.
- Binding operations consistently use bindingUsername when configured.
- Binding payments support POST redirects with AcsUrl, PaReq and TermUrl; MD is
  forwarded unchanged when supplied. Non-interactive binding payments still work
  without returnUrl.
- CardHolderID can be supplied to InitPayment to register a card binding.
- Authorization checks require a successful response code; completeAuthorize()
  is available. Preauthorization still requires bank terminal configuration.
- isCompleted() safely returns false for responses without an order status.
- Dependencies refreshed with PHP 7.4 compatibility and payment scenario tests.

Bank sandbox transactions and real 3-D Secure have not been exercised for this
release. Tests use HTTP mocks. See README for setup and integration requirements.

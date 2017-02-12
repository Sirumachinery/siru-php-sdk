# Siru Payment Gateway PHP SDK

Siru Payment Gateway Software development kit for PHP 5.6+.

## Installation

The SDK uses composer to manage dependencies. It depends on GuzzleHttp to for making HTTP requests.

- Download or clone SDK from Github.
- Install [composer](http://getcomposer.org)
- Install required dependencies with `composer install`

## Usage

To get started, you need your merchantId and merchant secret. If you don't have your credentials, 
contact [Siru Mobile](https://sirumobile.com) to discuss which payment methods are available to you and we will send you your sandbox credentials.

Go through our [API documentation](https://sirumobile.com/developers) to learn more about each API and message payloads.

Create instance of `\Siru\Signature`.

```PHP
/**
 * Signature class is used to sign outgoing messages and verify
 * responses from API. Replace $merchantId and $secret with your own credentials.
 */
$signature = new \Siru\Signature($merchantId, $secret);
```

Then use `\Siru\API` to call the API you need. For example to create new payment, you will use getPaymentApi():

```PHP
// Pass signature as parameter to API
$api = new \Siru\API($signature);

// Select sandbox environment (default)
$api->useStagingEndpoint();

// Create payment
try {

  $transaction = $api->getPaymentApi()
    ->set('basePrice', '5.00')
    ->set('purchaseCountry', 'FI')
    // .. set all required fields described in API documentation.
    ->set('customerNumber', '0441234567')
    ->createPayment();
  
  header('location: ' . $transaction['redirect']);
  exit();
  
} catch(\Siru\Exception\ApiException $e) {
  echo "API reported following errors:<br />";
  foreach($e->getErrorStack() as $error) {
    echo $error . "<br />";
  }
}

```

You can also use `\Siru\Signature` as standalone to create signature for your own code.
```PHP
/**
 * Imaginary example on using Signature without using \Siru\API.
 */
$paymentRequestFields = [
  // ... required fields as described in API documentation.
];

$hash = $signature->createMessageSignature($paymentRequestFields, [], Signature::FILTER_EMPTY | Signature::SORT_FIELDS);
$paymentRequestFields['signature'] = $hash;
$paymentRequestJson = json_encode($paymentRequestFields);

// Send request using what ever HTTP
$response = $myHttpClient->send('https://staging.sirumobile.com', 'POST', $paymentRequestJson);

// Then you would check API response status, parse the JSON string in response body
// and redirect user to the payment page.
```

When user returns from Siru payment page and also when Siru sends payment status notifications to your callback URL,
you will need to verify that the data is actually from Siru Mobile. For this you can use isNotificationAuthentic() method.
```PHP
// In your redirectAfter* URL:
$signature = new \Siru\Signature($merchantId, $secret);

if($signature->isNotificationAuthentic($_GET)) {
  // User was redirected from Siru payment page and query parameters are authentic
}
```
```PHP
// In your notifyAfter* URL:
$signature = new \Siru\Signature($merchantId, $secret);

$entityBody = file_get_contents('php://input');
$entityBodyAsJson = json_decode($entityBody, true);

if($signature->isNotificationAuthentic($entityBodyAsJson)) {
  // Notification was sent by Siru Mobile and is authentic
}
```

## API documentation

API documentation is available [here](https://sirumobile.com/developers).

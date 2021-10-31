# CoinPayment
CoinPayment Api Solution For The Laravel Applications

[![Latest Stable Version](http://poser.pugx.org/munna/coin-payment/v)](https://packagist.org/packages/munna/coin-payment) 
[![Total Downloads](http://poser.pugx.org/munna/coin-payment/downloads)](https://packagist.org/packages/munna/coin-payment) 
[![License](http://poser.pugx.org/munna/coin-payment/license)](https://packagist.org/packages/munna/coin-payment) 
[![PHP Version Require](http://poser.pugx.org/munna/coin-payment/require/php-7.2)](https://packagist.org/packages/munna/coin-payment)


## Installation
You can install this package via composer:
```bash
$ composer require munna/coin-payment
```

## ENV File Configurations
env variables
```env
COINPAYMENT_PUBLIC_KEY=your_public_key
COINPAYMENT_PRIVATE_KEY=your_private_key
COINPAYMENT_CURRENCY=USD
COINPAYMENT_IPN_ACTIVATE=true
COINPAYMENT_MARCHANT_ID=your_marchant_id
COINPAYMENT_IPN_SECRET=your_ipn_secret
COINPAYMENT_IPN_URL=
COINPAYMENT_IPN_DEBUG_EMAIL=your_email
COINPAYMENTS_API_FORMAT=json
```

# Getting Started
You can use class instance or facade instance like.
```php
. . .

use Munna\CoinPayment\CoinPayment;
$coinpaymnt = new Coinpayment();
$basic = $coinpaymnt->basicInfo();


//By Facade Class
use Munna\CoinPayment\Facade\CoinPayment;
$basic = CoinPayment::basicInfo();

// Return Data as json
. . .
```

```json
{
  "error": "ok",
  "result": {
    "uername": "your_user_name",
    "username": "your_user_name",
    "merchant_id": "your_marchant",
    "email": "your_email@gmail.com",
    "public_name": "Your public name",
    "time_joined": 1635623736,
    "kyc_status": false,
    "kyc_volume_limit": 100000000000,
    "kyc_volume_used": 0,
    "swych_tos_accepted": false
  },
  "status": true,
  "message": "Request Has Been Successful",
  "params": {
    "version": 1,
    "cmd": "get_basic_info",
    "key": "your_key",
    "format": "json",
    "ipn_url": null
  }
}
```


# CoinPayment Services As Methods
```php

use Munna\CoinPayment\CoinPayment;
$mc = new CoinPayment();

$envVariabl = $mc->checkEnv();
$checkProperty = $mc->checkProperty();
$checkSettings = $mc->checkSettings();
$address = $mc->getAddress("BTC"); // paramater is your targeted coin name
$txDetails = $mc->txnInfo("CPFJ5EA5DZI1KRY1KKX4CXHXQW");  // parameter is txn id
$rates = $mc->rates(); // get rates
$balances = $mc->balances(); // get balanace
$txnLists = $mc->txnLists(); // get transactions lists
$withdrawDetails = $mc->withdrawDetails("CWFJ007ZT8ZFEZFUWKIKA3WF6Q");  // parameter is withdraw id

// withdraw amount
$address = "mmGSjBhsqZBm68N1rJnM7MPTNx1KVkrMxT"; // your targeted address
$data = [ 
    'amount' => 0.5,
    'currency' => "LTCT",
    'address' => $address,
    'auto_confirm' => 1,  // auto confirm is withour email confirmation, 0 for email confirmation
];
$withdraw = $mc->withdraw($data); // withdraw method

// create tx fields are required
$array = [
    'amount' => 0.5,  // usd amount
    'currency' => 'USD',
    'currency2' => 'LTCT',
    'buyer_email' => 'buyer@gmail.com',
    'buyer_name' => 'Buyer name',
];
$txn = $mc->createTx($array);


$withdrawList = $mc->withdrawList(); // withdrw lists
$withdrawInfo = $mc->withdrawInfo('CWFJ64IBAPZ5OJXNH2ZZKRROVO');  // parameter is withdraw id

```




# CoinPayment Services As Methods By Facade Class
```php

use Munna\CoinPayment\Facade\CoinPayment;

$envVariabl = CoinPayment::checkEnv();
$checkProperty = CoinPayment::checkProperty();
$checkSettings = CoinPayment::checkSettings();
$address = CoinPayment::getAddress("BTC"); // paramater is your targeted coin name
$txDetails = CoinPayment::txnInfo("CPFJ5EA5DZI1KRY1KKX4CXHXQW");  // parameter is txn id
$rates = CoinPayment::rates(); // get rates
$balances = CoinPayment::balances(); // get balanace
$txnLists = CoinPayment::txnLists(); // get transactions lists
$withdrawDetails = CoinPayment::withdrawDetails("CWFJ007ZT8ZFEZFUWKIKA3WF6Q");  // parameter is withdraw id

// withdraw amount
$address = "mmGSjBhsqZBm68N1rJnM7MPTNx1KVkrMxT"; // your targeted address
$data = [ 
    'amount' => 0.5,
    'currency' => "LTCT",
    'address' => $address,
    'auto_confirm' => 1,  // auto confirm is withour email confirmation, 0 for email confirmation
];
$withdraw = CoinPayment::withdraw($data); // withdraw method

// create tx fields are required
$array = [
    'amount' => 0.5,  // usd amount
    'currency' => 'USD',
    'currency2' => 'LTCT',
    'buyer_email' => 'buyer@gmail.com',
    'buyer_name' => 'Buyer name',
];
$txn = CoinPayment::createTx($array);

$withdrawList = CoinPayment::withdrawList(); // withdrw lists
$withdrawInfo = CoinPayment::withdrawInfo('CWFJ64IBAPZ5OJXNH2ZZKRROVO');  // parameter is withdraw id

```




# IPN Route & IPN webhook Management
Except this path `/coinpayment/ipn` into csrf proccess in `App\Http\Middleware\VerifyCsrfToken` 
```php
. . .
/**
  * The URIs that should be excluded from CSRF verification.
  * This URL must be post url
  * @var array
  */
protected $except = [
    '/coinpayment/ipn' //your ipn route
]; 


// in your web.php
// its just an example
$route->post('coinpayment/ipn', [IpnController::class, 'ipnWebHook']);

// in IpnController.php controller or class
public function ipnWebHook(Request $get){
    
    // $get instance should be return txn_id as transaction id
    
    $coinpayment = new CoinPayment();    
    // by txn_id call the txn Information
    $info = $coinpayment->txnInfo($get->txn_id);
    // here is your txn information to check txn is confirmed or not
    // here is the json format of txn info
    // and manage txn by its status
    // when status = 100 then txn is confirmed otherwise not confirmed
}
. . .
```

```json
"error": "ok",
"result": {
  "time_created": 1635657533,
  "time_expires": 1635661133,
  "status": 100, // status code 100 means completed otherwise it will be 0
  "status_text": "Complete", // status Complete means complete otherwise Waiting for buyer fund...
  "type": "coins",
  "coin": "LTCT",
  "amount": 260000,
  "amountf": "0.00260000",
  "received": 260000,
  "receivedf": "0.00260000",
  "recv_confirms": 0,
  "payment_address": "mmGSjBhsqZBm68N1rJnM7MPTNx1KVkrMxT",
  "time_completed": 1635657662,
  "sender_ip": "103.92.205.5",
  "checkout": {
    "currency": "USD",
    "amount": 50000000,
    "test": 0,
    "item_number": "",
    "item_name": "",
    "details": [],
    "invoice": "",
    "custom": "",
    "ipn_url": "",
    "amountf": 0.5
  },
  "shipping": []
},
```

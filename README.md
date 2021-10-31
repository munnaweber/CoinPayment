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


# IPN Route 
Except this path `/coinpayment/ipn` into csrf proccess in `App\Http\Middleware\VerifyCsrfToken` 
```php
. . .
/**
  * The URIs that should be excluded from CSRF verification.
  *
  * @var array
  */
protected $except = [
    '/coinpayment/ipn' //your ipn route
]; 
. . .
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

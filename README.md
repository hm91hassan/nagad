<p align="center" ><img src="https://raw.githubusercontent.com/code4mk/lara-nagad/master/nagad%20payment.png"></p>

# lara-nagad `Bangladesh Nagad`
Laravel Nagad payment `BD`

# Installation

```bash
composer require luova/nagad
```

# Setup

## 1 ) vendor publish (config)

```bash
php artisan vendor:publish --tag=nagad
```

## 1.1 ) if you are using Laravel before version 5.4, manually register the service provider in your config/app.php file

```php
Luova\Nagad\NagadServiceProvider::class
```

## 2 ) Config setup

* `config/nagad.php`

```php
<?php

return [
    'projectPath' => env('APP_URL', 'http://127.0.0.1:8000/nagad/callback'),
    'sandbox' => env("NAGAD_API_SENDBOX", "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs"),
    'live' => env("NAGAD_API_LIVE", "https://api.mynagad.com/api/dfs"),
    'public_key' => env('NAGAD_PUBLIC_KEY', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjBH1pFNSSRKPuMcNxmU5jZ1x8K9LPFM4XSu11m7uCfLUSE4SEjL30w3ockFvwAcuJffCUwtSpbjr34cSTD7EFG1Jqk9Gg0fQCKvPaU54jjMJoP2toR9fGmQV7y9fz31UVxSk97AqWZZLJBT2lmv76AgpVV0k0xtb/0VIv8pd/j6TIz9SFfsTQOugHkhyRzzhvZisiKzOAAWNX8RMpG+iqQi4p9W9VrmmiCfFDmLFnMrwhncnMsvlXB8QSJCq2irrx3HG0SJJCbS5+atz+E1iqO8QaPJ05snxv82Mf4NlZ4gZK0Pq/VvJ20lSkR+0nk+s/v3BgIyle78wjZP1vWLU4wIDAQAB'),
    'private_key' => env('NAGAD_PRIVATE_KEY', 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCJakyLqojWTDAVUdNJLvuXhROV+LXymqnukBrmiWwTYnJYm9r5cKHj1hYQRhU5eiy6NmFVJqJtwpxyyDSCWSoSmIQMoO2KjYyB5cDajRF45v1GmSeyiIn0hl55qM8ohJGjXQVPfXiqEB5c5REJ8Toy83gzGE3ApmLipoegnwMkewsTNDbe5xZdxN1qfKiRiCL720FtQfIwPDp9ZqbG2OQbdyZUB8I08irKJ0x/psM4SjXasglHBK5G1DX7BmwcB/PRbC0cHYy3pXDmLI8pZl1NehLzbav0Y4fP4MdnpQnfzZJdpaGVE0oI15lq+KZ0tbllNcS+/4MSwW+afvOw9bazAgMBAAECggEAIkenUsw3GKam9BqWh9I1p0Xmbeo+kYftznqai1pK4McVWW9//+wOJsU4edTR5KXK1KVOQKzDpnf/CU9SchYGPd9YScI3n/HR1HHZW2wHqM6O7na0hYA0UhDXLqhjDWuM3WEOOxdE67/bozbtujo4V4+PM8fjVaTsVDhQ60vfv9CnJJ7dLnhqcoovidOwZTHwG+pQtAwbX0ICgKSrc0elv8ZtfwlEvgIrtSiLAO1/CAf+uReUXyBCZhS4Xl7LroKZGiZ80/JE5mc67V/yImVKHBe0aZwgDHgtHh63/50/cAyuUfKyreAH0VLEwy54UCGramPQqYlIReMEbi6U4GC5AQKBgQDfDnHCH1rBvBWfkxPivl/yNKmENBkVikGWBwHNA3wVQ+xZ1Oqmjw3zuHY0xOH0GtK8l3Jy5dRL4DYlwB1qgd/Cxh0mmOv7/C3SviRk7W6FKqdpJLyaE/bqI9AmRCZBpX2PMje6Mm8QHp6+1QpPnN/SenOvoQg/WWYM1DNXUJsfMwKBgQCdtddE7A5IBvgZX2o9vTLZY/3KVuHgJm9dQNbfvtXw+IQfwssPqjrvoU6hPBWHbCZl6FCl2tRh/QfYR/N7H2PvRFfbbeWHw9+xwFP1pdgMug4cTAt4rkRJRLjEnZCNvSMVHrri+fAgpv296nOhwmY/qw5Smi9rMkRY6BoNCiEKgQKBgAaRnFQFLF0MNu7OHAXPaW/ukRdtmVeDDM9oQWtSMPNHXsx+crKY/+YvhnujWKwhphcbtqkfj5L0dWPDNpqOXJKV1wHt+vUexhKwus2mGF0flnKIPG2lLN5UU6rs0tuYDgyLhAyds5ub6zzfdUBG9Gh0ZrfDXETRUyoJjcGChC71AoGAfmSciL0SWQFU1qjUcXRvCzCK1h25WrYS7E6pppm/xia1ZOrtaLmKEEBbzvZjXqv7PhLoh3OQYJO0NM69QMCQi9JfAxnZKWx+m2tDHozyUIjQBDehve8UBRBRcCnDDwU015lQN9YNb23Fz+3VDB/LaF1D1kmBlUys3//r2OV0Q4ECgYBnpo6ZFmrHvV9IMIGjP7XIlVa1uiMCt41FVyINB9SJnamGGauW/pyENvEVh+ueuthSg37e/l0Xu0nm/XGqyKCqkAfBbL2Uj/j5FyDFrpF27PkANDo99CdqL5A4NQzZ69QRlCQ4wnNCq6GsYy2WEJyU2D+K8EBSQcwLsrI7QL7fvQ=='),


    'apiCredentials' => [
        'merchant_id' => env("NAGAD_MERCHENT_ID", ''), // Your merchant ID
        'wallet' => env("NAGAD_WALLET", ''), // Your merchant Wallet number
    ],
    'apiUrl' => [
        'initialize' => "/check-out/initialize/",
        'complete' => "/check-out/complete/",
        'verify' => "/verify/payment/",
        'callback' => "/callback/nagad", // Your callback url
    ],
    'currencyCode' => '050', // payment currency code
    'status' => 'sandbox', //sandbox or live
];
```

# env setup

```bash
NAGAD_MERCHENT_ID=""
NAGAD_WALLET=""
NAGAD_API_SENDBOX=""
NAGAD_API_LIVE=""
NAGAD_PUBLIC_KEY=""
NAGAD_PRIVATE_KEY="" 

```

# Usage

## get callback url

```php
<?php
use NagadPayment;

$redirectUrl = NagadPayment::tnxID($id)
             ->amount($amount)
             ->getRedirectUrl();
return $redirectUrl;
```

## verify payment // callback

```php
<?php
use NagadPayment;

$verify = (object) NagadPayment::verify();
if($verify->status === 'Success'){
    $order = json_decode($verify->additionalMerchantInfo);
    $order_id = $order->tnx_id;
    // your functional task with order_id
}
if ($verify->status === 'Aborted') {
    dd($verify);
    // redirect or other what you want
}
dd($verify);

```

# Note:

`~Sandbox`

* Need a merchant account.
* Register a Nagad number and need sandbox balance (contact with nagad)

`~ Live`

* Need a merchant account (live server)
* Contact with Nagad and provide your live server ip address.
* provide support id ($sid) the nagad office

# Live mode tips 

`Sandbox works fine but when you deploy your project on server you can't get any response and don't work payment system`

## How to enable nagad gateway on server 

* Contact with nagad, provide your ip and support ID  which you will get from temporary route `get-support-id` . Nagad will be white-listed your ip and approve your merchant. Now your nagad gateway work properly on server.

~ temporary route (copy and paste)

```php
Route::get('get-support-id',function(){
    $sid = NagadPayment::tnxID(1)
                 ->amount(100)
                 ->getSupportID();
    return $sid;
})
```


# Any query

* hm91hassan@gmail.com

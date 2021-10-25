<?php

namespace Luova\Nagad\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

trait LNagad
{
    public function nagadInitialize($data)
    {

        $default = [
            'amount' => 0,
            'transaction_id' => null,
        ];
        // dd($default);

        $final = array_merge($default, $data);

        date_default_timezone_set('Asia/Dhaka');


        if ($final['amount'] <= 0) {
            notify()->error('Your Cart is empty !');
            return redirect()->back();
        }


        $MerchantID = config('nagad.apiCredentials.merchant_id');
        // dd($Mer?chantID);
        $DateTime = Date('YmdHis');
        $amount = $final['amount'];
        $OrderId = $final['transaction_id'];
        $PostURL = config('nagad.' . config('nagad.status')) . config('nagad.apiUrl.initialize') . $MerchantID . "/" . $OrderId;
        $merchantCallbackURL = url(config('nagad.apiUrl.callback'));

        $SensitiveData = array(
            'merchantId' => $MerchantID,
            'datetime' => $DateTime,
            'orderId' => $final['transaction_id'],
            'challenge' => $final['transaction_id']
        );

        $PostData = array(
            'accountNumber' => config('nagad.apiCredentials.wallet'), //Replace with Merchant Number
            'dateTime' => $DateTime,
            'sensitiveData' => $this->EncryptDataWithPublicKey(json_encode($SensitiveData)),
            'signature' => $this->SignatureGenerate(json_encode($SensitiveData))
        );


        $Result_Data = $this->HttpPostMethod($PostURL, $PostData);



        if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
            if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

                $PlainResponse = json_decode($this->DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);

                if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {

                    $paymentReferenceId = $PlainResponse['paymentReferenceId'];
                    $randomServer = $PlainResponse['challenge'];

                    $SensitiveDataOrder = array(
                        'merchantId' => $MerchantID,
                        'orderId' => $OrderId,
                        'currencyCode' => config('nagad.currencyCode'),
                        'amount' => $amount,
                        'challenge' => $randomServer
                    );

                    $merchantAdditionalInfo = '{"Service Name": "' . config('nagad.projectPath') . '"}';

                    $PostDataOrder = array(
                        'sensitiveData'             => $this->EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                        'signature'                 => $this->SignatureGenerate(json_encode($SensitiveDataOrder)),
                        'merchantCallbackURL'       => $merchantCallbackURL,
                        'additionalMerchantInfo'    => json_decode($merchantAdditionalInfo)
                    );

                    $OrderSubmitUrl = config('nagad.' . config('nagad.status')) . config('nagad.apiUrl.complete') . $paymentReferenceId;


                    $Result_Data_Order = $this->HttpPostMethod($OrderSubmitUrl, $PostDataOrder);

                    if (isset($Result_Data_Order['status']) && $Result_Data_Order['status'] == "Success") {
                        $url = $Result_Data_Order['callBackUrl'];

                        return Redirect::to($url)->send();
                    } else {
                        echo json_encode($Result_Data_Order);
                    }
                } else {
                    echo json_encode($PlainResponse);
                }
            }
        }
    }




    private function generateRandomString($length = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function EncryptDataWithPublicKey($data)
    {
        $pgPublicKey = config('nagad.public_key');
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";
        // echo $public_key;
        // exit();
        $key_resource = openssl_get_publickey($public_key);
        openssl_public_encrypt($data, $cryptText, $key_resource);
        return base64_encode($cryptText);
    }

    private function SignatureGenerate($data)
    {
        $merchantPrivateKey = config('nagad.private_key');
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        // echo $private_key;
        // exit();
        openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    private function HttpPostMethod($PostURL, $PostData)
    {
        $url = curl_init($PostURL);
        $postToken = json_encode($PostData);
        $header = array(
            'Content-Type:application/json',
            'X-KM-Api-Version:v-0.2.0',
            'X-KM-IP-V4:' . $this->get_client_ip(),
            'X-KM-Client-Type:PC_WEB'
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($url, CURLOPT_HEADER, 1);




        $resultData = curl_exec($url);
        $ResultArray = json_decode($resultData, true);
        $header_size = curl_getinfo($url, CURLINFO_HEADER_SIZE);
        curl_close($url);
        $headers = substr($resultData, 0, $header_size);
        $body = substr($resultData, $header_size);
        // print_r($body);
        // print_r($headers);
        return $ResultArray;
    }

    private function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    private function DecryptDataWithPrivateKey($cryptText)
    {
        $merchantPrivateKey = "MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCJakyLqojWTDAVUdNJLvuXhROV+LXymqnukBrmiWwTYnJYm9r5cKHj1hYQRhU5eiy6NmFVJqJtwpxyyDSCWSoSmIQMoO2KjYyB5cDajRF45v1GmSeyiIn0hl55qM8ohJGjXQVPfXiqEB5c5REJ8Toy83gzGE3ApmLipoegnwMkewsTNDbe5xZdxN1qfKiRiCL720FtQfIwPDp9ZqbG2OQbdyZUB8I08irKJ0x/psM4SjXasglHBK5G1DX7BmwcB/PRbC0cHYy3pXDmLI8pZl1NehLzbav0Y4fP4MdnpQnfzZJdpaGVE0oI15lq+KZ0tbllNcS+/4MSwW+afvOw9bazAgMBAAECggEAIkenUsw3GKam9BqWh9I1p0Xmbeo+kYftznqai1pK4McVWW9//+wOJsU4edTR5KXK1KVOQKzDpnf/CU9SchYGPd9YScI3n/HR1HHZW2wHqM6O7na0hYA0UhDXLqhjDWuM3WEOOxdE67/bozbtujo4V4+PM8fjVaTsVDhQ60vfv9CnJJ7dLnhqcoovidOwZTHwG+pQtAwbX0ICgKSrc0elv8ZtfwlEvgIrtSiLAO1/CAf+uReUXyBCZhS4Xl7LroKZGiZ80/JE5mc67V/yImVKHBe0aZwgDHgtHh63/50/cAyuUfKyreAH0VLEwy54UCGramPQqYlIReMEbi6U4GC5AQKBgQDfDnHCH1rBvBWfkxPivl/yNKmENBkVikGWBwHNA3wVQ+xZ1Oqmjw3zuHY0xOH0GtK8l3Jy5dRL4DYlwB1qgd/Cxh0mmOv7/C3SviRk7W6FKqdpJLyaE/bqI9AmRCZBpX2PMje6Mm8QHp6+1QpPnN/SenOvoQg/WWYM1DNXUJsfMwKBgQCdtddE7A5IBvgZX2o9vTLZY/3KVuHgJm9dQNbfvtXw+IQfwssPqjrvoU6hPBWHbCZl6FCl2tRh/QfYR/N7H2PvRFfbbeWHw9+xwFP1pdgMug4cTAt4rkRJRLjEnZCNvSMVHrri+fAgpv296nOhwmY/qw5Smi9rMkRY6BoNCiEKgQKBgAaRnFQFLF0MNu7OHAXPaW/ukRdtmVeDDM9oQWtSMPNHXsx+crKY/+YvhnujWKwhphcbtqkfj5L0dWPDNpqOXJKV1wHt+vUexhKwus2mGF0flnKIPG2lLN5UU6rs0tuYDgyLhAyds5ub6zzfdUBG9Gh0ZrfDXETRUyoJjcGChC71AoGAfmSciL0SWQFU1qjUcXRvCzCK1h25WrYS7E6pppm/xia1ZOrtaLmKEEBbzvZjXqv7PhLoh3OQYJO0NM69QMCQi9JfAxnZKWx+m2tDHozyUIjQBDehve8UBRBRcCnDDwU015lQN9YNb23Fz+3VDB/LaF1D1kmBlUys3//r2OV0Q4ECgYBnpo6ZFmrHvV9IMIGjP7XIlVa1uiMCt41FVyINB9SJnamGGauW/pyENvEVh+ueuthSg37e/l0Xu0nm/XGqyKCqkAfBbL2Uj/j5FyDFrpF27PkANDo99CdqL5A4NQzZ69QRlCQ4wnNCq6GsYy2WEJyU2D+K8EBSQcwLsrI7QL7fvQ==";
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($cryptText), $plain_text, $private_key);
        return $plain_text;
    }


    private function HttpGet($url)
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $file_contents = curl_exec($ch);
        echo curl_error($ch);
        curl_close($ch);
        return $file_contents;
    }

    private function NagadPyamentVerify($payment_ref_id)
    {

        $url            = config('nagad.' . config('nagad.status')) . config('nagad.apiUrl.verify') . $payment_ref_id;
        $json           = $this->HttpGet($url);
        return  json_decode($json, true);;
    }
}

<?php

namespace App\Services;

use Carbon\Carbon;

class UbotPaymentService {
    public const UBOT_PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1t0Exp+7O41LGd0zrWW1
dqdTLr4YJ2qkS70O1Q7tiTFczRldW0/QevaFT9RPmjl8V9b5fwMM/YlBv+yICon0
rAH4uGTMJJc8TbpvXtK7UbwmDPDQ4HTNWTgZmJ2feaEZn/FKOxgmMY7bowgC2Nvz
0rWQv0ZsOlLJl3SZSuqSjpjfmmg3bXIYum1sv4TzMreOiuhctCMbdm9W1ONmJKZj
luOXKoxiuYUt4NUEh0vRLFkD4SJAhAILe4eRXgMcaTpPJWrnWnCMXJ9LYd7BQMKn
QKgA+uRLCBhnMz7BLL4Qygx5b+aFxRLRxxgMNAfljB008rDi/8Gh0Ph8zhtS4R/B
JQIDAQAB
-----END PUBLIC KEY-----";

    // 用於產生 UBot AES Key
    public const SECRET_KEY = "-----BEGIN SECRET KEY-----
Wng3M29zMXhyNnNOM3YzcVhnMDlqSTVackVSb0FjWmZMUzI2Sk5GL2FGaTd6NnZT
VzZKNFNBb0wzQ1FLQlM3c3UyKzZlVXVCMGV2N3VBa0VZa3ppc2hWTTFlVEdrdWR4
NjZtNmRDUkIwT1FHc3NXcFdBTzhBKzZDSnBPSlZ0eUxWLzA2RHMzZnlmSVVnUXpW
YkRqRnpYdUxjYk80RE5WcnRFRkl5cUtTOGh6UEpwY0hDMG8zQ1RHblF6M2pQUGdP
RGRUYnVLRUUxOVFINTh3LzlQRGNnUHM1RmRkT05DQkpLekpQSklhUFJ6cnV5a0VV
M2h1WVFHYjlmQzRjTFNqM3U3SkxkWmZrbXZ3VTNCZG1pQ0pHSEdnaVV4M2IxRWQz
WXpkV2xMemVsSDhOOWtMcGxnd2J4WjVrTzBuQkNTcm92VVVud1VWMXdmbjQzMjMy
SjltK093PT0=
-----END SECRET KEY-----";

    public const UBOT_IV = 'UBOTSECRETIVSEED';

    private $dataString;
    private $data;
    private $mac;
    private $signature;

    public function __construct($requestData)
    {
        $this->dataString = $requestData['data'];
        $this->data = json_decode($this->dataString, true);
        $this->mac = $requestData['mac'];
        $this->signature = $requestData['signature'];
    }

    public function validate() {
        return $this->validateSignature() && $this->validateMac();
    }

    public function virtualAccount() {
        return $this->data['ecacc'];
    }

    public function txTime() {
        $date = $this->data['date'];
        $time = $this->data['time'];

        // 20160301161214
        return Carbon::parse($date.$time);
    }

    // amt: '00000000124500'（小數點兩位）
    public function amount() {
        return intval($this->data['amt']) / 100;
    }

    public function txSeq() {
        return $this->data['txseq'];
    }

    // 轉出銀行
    public function wdBank() {
        return $this->data['wdbank'];
    }

    // 轉出帳號
    public function wdAcc() {
        return $this->data['wdacc'];
    }

    private function getSecretKeyContent() {
        $lines = explode("\n", self::SECRET_KEY);
        array_shift($lines); // remove first line
        array_pop($lines);   // remove end line

        return join('', $lines);
    }

    private function getEncryptKey() {
        return base64_decode(base64_decode($this->getSecretKeyContent()));
    }

    private function getUBotPublicKey() {
        return openssl_pkey_get_public(self::UBOT_PUBLIC_KEY);
    }

    private function getUBotAESKey() {
        $encryptKey = $this->getEncryptKey();
        $publicKey = $this->getUBotPublicKey();

        $decrypted = null;
        $result = openssl_public_decrypt($encryptKey, $decrypted, $publicKey, OPENSSL_PKCS1_PADDING);

        if($result == FALSE) {
            echo "Decrypt error";
            return;
        }

        return $decrypted;
    }

    private function getUBotAESIV() {
        return self::UBOT_IV;
    }

    // 明文—SHA256--->Base64--->AES CBC/PKCS5Padding--->Base64--->mac
    private function calculateMac() {
        $aesKey = $this->getUBotAESKey();
        $iv = $this->getUBotAESIV();
        $sha256HashedData = base64_encode(hash('sha256', $this->dataString, true));
        $aesEncryptedData =  base64_encode(openssl_encrypt($sha256HashedData, "AES-128-CBC", base64_decode($aesKey), OPENSSL_RAW_DATA, $iv))

        return $aesEncryptedData;
    }

    // signature ---> Base64 decode ---> RSA verify --->mac，利用提供之公鑰驗章
    private function validateSignature() {
        $publicKey = $this->getUBotPublicKey();

        return !!openssl_verify($this->dataString, base64_decode($this->signature), $publicKey, OPENSSL_ALGO_SHA256);
    }

    private function validateMac() {
        $generatedMac = $this->calculateMac();

        return $generatedMac == $this->mac;
    }
}

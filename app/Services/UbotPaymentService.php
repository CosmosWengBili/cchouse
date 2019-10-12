<?php

namespace App\Services;

use Carbon\Carbon;

class UbotPaymentService {
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
        $lines = explode("\n", env('UBOT_SECRET_KEY'));
        array_shift($lines); // remove first line
        array_pop($lines);   // remove end line

        return join('', $lines);
    }

    private function getEncryptKey() {
        return base64_decode(base64_decode($this->getSecretKeyContent()));
    }

    private function getUBotPublicKey() {
        return openssl_pkey_get_public(env('UBOT_PUBLIC_KEY'));
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
        return env('UBOT_IV');
    }

    // 明文—SHA256--->Base64--->AES CBC/PKCS5Padding--->Base64--->mac
    private function calculateMac() {
        $aesKey = $this->getUBotAESKey();
        $iv = $this->getUBotAESIV();
        $sha256HashedData = base64_encode(hash('sha256', $this->dataString, true));
        $aesEncryptedData = base64_encode(openssl_encrypt($sha256HashedData, "AES-128-CBC", base64_decode($aesKey), OPENSSL_RAW_DATA, $iv));

        return $aesEncryptedData;
    }

    // signature ---> Base64 decode ---> RSA verify --->mac，利用提供之公鑰驗章
    private function validateSignature() {
        $publicKey = $this->getUBotPublicKey();

        return !!openssl_verify($this->mac, base64_decode($this->signature), $publicKey, OPENSSL_ALGO_SHA256);
    }

    private function validateMac() {
        $generatedMac = $this->calculateMac();

        return $generatedMac == $this->mac;
    }
}

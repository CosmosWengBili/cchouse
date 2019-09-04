<?php

namespace App\Services;

use Mitake\Client;
use Mitake\Message\LongMessage;
use Mitake\Message\Response;

class SmsService {
    public function send(string $mobile, string $content): Response {
        \Log::info("===========SMS Message==========");
        \Log::info($mobile);
        \Log::info($content);
        \Log::info("================================");

        $message = (new LongMessage())->setDstaddr($mobile)->setSmbody($content);
        return $this->client()->sendLongMessage($message);
    }

    private function client() {
        return $client = new Client(env('MITAKE_USERNAME'), env('MITAKE_PASSWORD'));
    }
}

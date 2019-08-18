<?php

namespace App\Services;

use Mitake\Client;
use Mitake\Message\Message;
use Mitake\Message\Response;

class SmsService {
    public static function send(string $mobile, string $content): Response {
        \Log::info("===========SMS Message==========");
        \Log::info($mobile);
        \Log::info($content);
        \Log::info("================================");

        $message = (new Message())->setDstaddr($mobile)->setSmbody($content);
        return self::client()->send($message);
    }

    private static function client() {
        return $client = new Client(env('MITAKE_USERNAME'), env('MITAKE_PASSWORD'));
    }
}

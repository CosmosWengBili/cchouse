<?php

namespace App\Services;
// Disable SDK for expired api endpoint
// use Mitake\Client;
// use Mitake\Message\LongMessage;
// use Mitake\Message\Response;

use GuzzleHttp\Client;

class SmsService {
    public function send(string $mobile, string $content) {
        \Log::info("===========SMS Message==========");
        \Log::info($mobile);
        \Log::info($content);
        \Log::info("================================");

        $base_url = env('MITAKE_HOST').'/api/mtk/SmSend';
        $api_url = $base_url.'?username='.env('MITAKE_USERNAME')
                    .'&password='.env('MITAKE_PASSWORD')
                    .'&dstaddr='.$mobile
                    .'&smbody='.mb_convert_encoding($content, "BIG5", "UTF-8");;
        $client = new Client();
        $res = $client->request('GET', $api_url);
        
        return $res;
        // $message = (new LongMessage())->setDstaddr($mobile)->setSmbody($content);
        // return $this->client()->sendLongMessage($message);
    }

    // private function client() {
    //     return $client = new Client(env('MITAKE_USERNAME'), env('MITAKE_PASSWORD'));
    // }
}

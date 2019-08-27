<?php

namespace App\Responser;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class ReceivableResponser
{
    private $template = 
    '<PaySvcRs>'.
        '<StatusCode>$statusCode</StatusCode>'.
        '<StatusDesc>$message</StatusDesc>'.
        '<PmtAddRs>'.
            '<UserID></UserID>'.
            '<TxnSeq>$txSeq</TxnSeq>'.
            '<TxnDate>$txDate</TxnDate>'.
            '<TxnAmt>$amount</TxnAmt>'.
        '</PmtAddRs>'.
    '</PaySvcRs>';

    private $xml;
    private $data = [
        '$statusCode' => '',
        '$message'    => '',
        '$txSeq'      => '',
        '$txDate'     => '',
        '$amount'     => '',
    ];

    public function __construct($xml) {
        $this->xml = $xml;
        $this->data['$txSeq'] = $xml->PmtAddRq->TDateSeqNo;
        $this->data['$txDate'] = $xml->PmtAddRq->TxnDate;
        $this->data['$amount'] = $xml->PmtAddRq->TxAmount;
    }

    public function success($msg = '') {
        $this->data['$statusCode'] = 0;
        if (env('APP_ENV') === 'production') {
            $this->data['$message'] = $msg;
        } else {
            $this->data['$message'] = 'PR_KEY=' . $this->xml->PmtAddRq->PR_Key1;
        }
        return $this;
    }

    public function error($code, $msg = '') {
        $this->data['$statusCode'] = $code;
        $this->data['$message'] = $msg;
        return $this;
    }
    // output data
    public function get() {
        return strtr($this->template, $this->data);
    }
}

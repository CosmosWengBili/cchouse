<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script
  src="https://code.jquery.com/jquery-3.4.1.js"
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
  crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<h2>自動沖銷 API</h2>
<label for="">金額</label>
<input id="amount">
<label for="">虛擬帳號</label>
<input id="virtual_account">
<label for="">發送時間</label>
<input id="tx_date">
<input id="reversal_submit" type="button" value="送出">
<script>
    $('#reversal_submit').click(function(){
        var url = "{{env('APP_URL')}}"
        var bank_code = {{config('finance.bank_code')}}
        var data = `
            <PaySvcRq>
            <PmtAddRq>
            <TDateSeqNo>${tx_date.value}000029216</TDateSeqNo>
            日期+序號)
            <TxnDate>${tx_date.value}</TxnDate>
            <TxnTime>080000</TxnTime>
            <ValueDate>${tx_date.value}</ValueDate>
            <TxAmount>${amount.value}</TxAmount>
            <BankID>0081000</BankID>
            <ActNo>00708804344</ActNo>
            <MAC></MAC>
            <PR_Key1>${virtual_account.value}</PR_Key1>
            </PmtAddRq>
            </PaySvcRq>             
        `;
        $.post(url + '/api/bank/webhook',data, function (res) {})
    })

</script>

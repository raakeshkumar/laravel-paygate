<div class="col-6 offset-3">
    <div class="mx-auto">
        <h1>Payment Button</h1>
        <form action="https://secure.paygate.co.za/payweb3/process.trans" method="POST">
            <input type="hidden" name="PAY_REQUEST_ID" value="{{ $output['PAY_REQUEST_ID'] }}">
            <input type="hidden" name="CHECKSUM" value="{{ $output['CHECKSUM'] }}">
            <button type="submit" class="btn btn-success pr-5 pl-5">Pay</button>
        </form>
    </div>
</div>
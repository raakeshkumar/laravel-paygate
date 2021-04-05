<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function initiate() {

        $DateTime = new \DateTime();

        $data = array(
            'PAYGATE_ID'       => env('PAYGATE_ID'),
            'REFERENCE'        => uniqid('pgtest_'),
            'AMOUNT'           => 3299,
            'CURRENCY'         => 'ZAR', //https: //docs.paygate.co.za/#country-codes
            'RETURN_URL'       => route('payment_response'),
            'TRANSACTION_DATE' => $DateTime->format('Y-m-d H:i:s'),
            'LOCALE'           => 'en-za', //https: //docs.paygate.co.za/#locale-codes
            'COUNTRY'          => 'ZAF', // https: //docs.paygate.co.za/#country-codes
            'EMAIL'            => 'developer@mailinator.com',
            'NOTIFY_URL'       => route('payment_notify'),
        );

        $checksum = md5(implode('', $data) . env('PAYGATE_SECRET'));

        $data['CHECKSUM'] = $checksum;

        $response = Http::asForm()->post(env('PAYGATE_INITIATE_URL'), $data);
        //return $response->body();

        parse_str($response->body(), $output);

        if (!empty($output)) {
            Transaction::create([
                'pay_request_id'     => $output['PAY_REQUEST_ID'],
                'paygate_id'         => $output['PAYGATE_ID'],
                'reference'          => $output['REFERENCE'],
                'checksum'           => $output['CHECKSUM'],
                'transaction_status' => 'initiated',
            ]);
            return view('payment.form', compact('output'));
        }

        // You can show some error messages here or redirect to another URL
        return "Something went wrong";

    }

    public function notify(Request $request) {
        // Since this URL is being called by external system, we will need to excempt CSRF token for this.

        $transaction = Transaction::where('paygate_id', $request->PAYGATE_ID)
            ->where('pay_request_id', $request->PAY_REQUEST_ID)
            ->where('transaction_status', 'initiated')
            ->first();

        if (!empty($transaction)) {

            $data = array(
                'PAYGATE_ID'     => env('PAYGATE_ID'),
                'PAY_REQUEST_ID' => $transaction->pay_request_id,
                'REFERENCE'      => $transaction->reference,
            );

            $checksum         = md5(implode('', $data) . env('PAYGATE_SECRET'));
            $data['CHECKSUM'] = $checksum;
            $response         = Http::asForm()->post(env('PAYGATE_QUERY_URL'), $data);

            parse_str($response->body(), $output);

            //PAYGATE_ID=10011072130&PAY_REQUEST_ID=23B785AE-C96C-32AF-4879-D2C9363DB6E8&REFERENCE=pgtest_123456789&TRANSACTION_STATUS=1&RESULT_CODE=990017&AUTH_CODE=5T8A0Z&CURRENCY=ZAR&AMOUNT=3299&RESULT_DESC=Auth+Done&TRANSACTION_ID=78705178&RISK_INDICATOR=AX&PAY_METHOD=CC&PAY_METHOD_DETAIL=Visa&CHECKSUM=f57ccf051307d8d0a0743b31ea379aa1
            if ($output->TRANSACTION_STATUS == '1') {
                // Transaction successful
                $transaction->update([
                    'reference'          => $request->REFERENCE,
                    'transaction_status' => 'successful',
                    'result_code'        => $request->RESULT_CODE,
                    'auth_code'          => $request->AUTH_CODE,
                    'currency'           => $request->CURRENCY,
                    'amount'             => $request->AMOUNT,
                    'result_desc'        => $request->RESULT_DESC,
                    'transaction_id'     => $request->TRANSACTION_ID,
                    'pay_method'         => $request->PAY_METHOD,
                    'pay_method_detail'  => $request->PAY_METHOD_DETAIL,
                    'vault_id'           => $request->VAULT_ID,
                    'payvault_data_1'    => $request->PAYVAULT_DATA_1,
                    'payvault_data_2'    => $request->PAYVAULT_DATA_2,
                    'checksum'           => $request->CHECKSUM,
                ]);

                return "OK";
            }

            $transaction->update([
                'reference'          => $request->REFERENCE,
                'transaction_status' => 'failed',
                'result_code'        => $request->RESULT_CODE,
                'auth_code'          => $request->AUTH_CODE,
                'currency'           => $request->CURRENCY,
                'amount'             => $request->AMOUNT,
                'result_desc'        => $request->RESULT_DESC,
                'transaction_id'     => $request->TRANSACTION_ID,
                'pay_method'         => $request->PAY_METHOD,
                'pay_method_detail'  => $request->PAY_METHOD_DETAIL,
                'vault_id'           => $request->VAULT_ID,
                'payvault_data_1'    => $request->PAYVAULT_DATA_1,
                'payvault_data_2'    => $request->PAYVAULT_DATA_2,
                'checksum'           => $request->CHECKSUM,
            ]);
            return "Payment couldn't be verified";

        }

        return "Something went wrong";
    }

    public function pg_response(Request $request) {
        $transaction = Transaction::where('pay_request_id', $request->PAY_REQUEST_ID)
            ->first();

        if (empty($transaction)) {
            return "Something went wrong";
        }

        return view("payment.response", compact('transaction'));
    }
}

<?php 
namespace Munna\CoinPayment\Helper;
use Munna\DotEnvEditor\Facades\DotEnvEditor;

trait CoinPaymentHelper {

    /**
     * @method helper for env file
     */
    public function required_env_data(){
        $env = DotEnvEditor::env_array();
        return [
            "COINPAYMENT_PUBLIC_KEY" => $env["COINPAYMENT_PUBLIC_KEY"],
            "COINPAYMENT_PRIVATE_KEY" => $env["COINPAYMENT_PRIVATE_KEY"],
            "COINPAYMENT_CURRENCY" => $env["COINPAYMENT_CURRENCY"],
            "COINPAYMENT_IPN_ACTIVATE" => $env["COINPAYMENT_IPN_ACTIVATE"],
            "COINPAYMENT_MARCHANT_ID" => $env["COINPAYMENT_MARCHANT_ID"],
            "COINPAYMENT_IPN_SECRET" => $env["COINPAYMENT_IPN_SECRET"],
            "COINPAYMENTS_API_FORMAT" => $env["COINPAYMENTS_API_FORMAT"],
            "COINPAYMENT_IPN_URL" => $env["COINPAYMENT_IPN_URL"],
            "COINPAYMENT_IPN_DEBUG_EMAIL" => $env["COINPAYMENT_IPN_DEBUG_EMAIL"]
        ];
    }

    /**
     * 
     * @method check coinpayment command tools
     */
    public function cpd($data){
        $CREATE_TRANSACTION = 'create_transaction';
        $CREATE_WITHDRAWAL = 'create_withdrawal';
        $CREATE_TRANSFER = 'create_transfer';
        $GET_TX_INFO = 'get_tx_info';
        $GET_CALLBACK_ADDRESS = 'get_callback_address';
        $BALANCES = 'balances';
        $RATES = 'rates';
        $gtLists = "get_tx_ids";
        $withHistory = "get_withdrawal_history";
        $withInfo = "get_withdrawal_info";
        
        if($data == 'cr-tx'){
            return $CREATE_TRANSACTION;
        }elseif($data == 'cr-wt'){
            return $CREATE_WITHDRAWAL;
        }elseif($data == 'cr-ts'){
            return $CREATE_TRANSFER;
        }elseif($data == 'gt-tx'){
            return $GET_TX_INFO;
        }elseif($data == 'gt-addr'){
            return $GET_CALLBACK_ADDRESS;
        }elseif($data == 'gt-bal'){
            return $BALANCES;
        }elseif($data == 'gt-rate'){
            return $RATES;
        }elseif($data == 'gt-tx-list'){
            return $gtLists;
        }elseif($data == 'gt-wt-list'){
            return $withHistory;
        }elseif($data == 'gt-wt-info'){
            return $withInfo;
        }else{
            return null;
        }
    }

}
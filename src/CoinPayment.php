<?php 
namespace Munna\CoinPayment;

use Munna\CoinPayment\Helper\HelperJson;
use Munna\CoinPayment\Helper\CoinPaymentHelper;

class CoinPayment {
    /**
     * @method all methods from helper
     */
    use CoinPaymentHelper;

    /**
     * @property required initital property
     */
    public $apiUrl;
    public $public_key;
    public $private_key;
    public $ipn_secret;
    public $format;
    public $ipn_url;
    public $marchant;
    public $debug_mail;
    public $fiat;
    public $params;
    /**
     * @method constructor
     */
    public function __construct($data){
        $env = $this->required_env_data();
        $this->params = isset($data['params']) ? $data['params'] : true;
        $this->apiUrl = "https://www.coinpayments.net/api.php";
        $this->public_key = $env['COINPAYMENT_PUBLIC_KEY'] ?? null;
        $this->private_key = $env['COINPAYMENT_PRIVATE_KEY'] ?? null;
        $this->marchant = $env['COINPAYMENT_MARCHANT_ID'] ?? null;
        $this->debug_mail = $env['COINPAYMENT_IPN_DEBUG_EMAIL'] ?? null;
        $this->ipn_secret = $env['COINPAYMENT_IPN_SECRET'] ?? null;
        $this->fiat = isset($data['curreny']) ? $data['curreny'] : $env['COINPAYMENT_CURRENCY'] ?? "USD";
        $this->format = "json";
        $this->ipn_url = isset($data['ipn']) ? $data['ipn'] : $env["COINPAYMENT_IPN_URL"] ?? null;
        if($this->ipn_url == null){
            throw new HelperJson(false, 'IPN url doesn\'t set. Please set your ipn\'s url');
        }

    }

    /**
     * 
     * @method getAddress for corresponding coin
     */
    public function getAddress($currency, $ipnUrl = ''){
        $req = array(
            'currency' => $currency,
            'ipn_url' => $ipnUrl ?? $this->ipn_url,
        );
        return $this->apiCall($this->cpd('gt-addr'), $req);
    }

    /**
     * 
     * @method get tx data from api
     */
    public function txnInfo($txID, $all = true){
        $req = array(
            'txid' => $txID,
            'full' => (int)$all
        );
        return $this->apiCall($this->cpd('gt-tx'), $req);
    }


    /**
     * 
     * @method create new transactions
     */
    public function createTx($additional){
        $amount = (double) $additional['amount'];
        $currencyIn = $additional['currency'];
        $currencyOut = $additional['currency2'];
        $success_url = isset($additional['success_url']) ? $additional['success_url'] : null;
        $cancel_url = isset($additional['cancel_url']) ? $additional['cancel_url'] : null;
        $acceptableFields = [
            'address', 'buyer_email', 'buyer_name',
            'item_name', 'item_number', 'invoice', 'custom', 'ipn_url'
        ];
        $request = [
            'amount' => $amount,
            'currency1' => $currencyIn,
            'currency2' => $currencyOut,
            'success_url' => $success_url,
            'cancel_url' => $cancel_url
        ];

        foreach ($acceptableFields as $field) {
            if (isset($additional[$field])) {
                $request[$field] = $additional[$field];
            }
        }
        return $this->apiCall($this->cpd('cr-tx'), $request);
    }

    /**
     * 
     * @method for call rates
     */

    public function txnLists($data = []){
        return $this->apiCall($this->cpd('gt-tx-list'), $data);
    }

    /**
     * 
     * @method for getting withdraw history
     */

    public function withdrawList($data = []){
        return $this->apiCall($this->cpd('gt-wt-list'), $data);
    }

    /**
     * 
     * @method for getting withdraw info
     */

    public function withdrawInfo($id){
        return $this->apiCall($this->cpd('gt-wt-info'), ['id' => $id]);
    }
  
    /**
     * 
     * @method for getting withdraw info
     */

    public function basicInfo(){
        return $this->apiCall('get_basic_info');
    }
    

   
    /**
     * 
     * @method for call rates
     */

    public function rates($accepted = true, $usd = null){
        return $this->apiCall($this->cpd('gt-rate'), ['accepted' => true]);
    }

    /**
     * 
     * @method for getting balances
     */

    public function balances($all = false){
        return $this->apiCall($this->cpd('gt-bal'), array('all' => $all ? 1 : 0));
    }


    /**
     * 
     * @method call the withdraw methods
     */
    public function withdraw($data){
        $reqData = array(
            'ipn_url' => isset($data['ipn_url']) ? $data['ipn_url'] : $this->ipn_url,
        );
        $req = array_merge($data, $reqData);
        return $this->apiCall($this->cpd('cr-wt'), $req);
    }

    /**
     * @method call the coin payment api
     * 
     */
    public function apiCall($cmd, $req = array()){
        // Set the API command and required fields
        $req['version'] = 1;
        $req['cmd'] = $cmd;
        $req['key'] = $this->public_key;
        $req['format'] = isset($req['format']) && !empty($req['ipn_url']) ? $req['format'] : $this->format;
        $req['ipn_url'] = isset($req['ipn_url']) && !empty($req['ipn_url']) ? $req['ipn_url'] : $this->ipn_url;

        // Generate the query string
        $postData = http_build_query($req, '', '&');

        // Calculate the HMAC signature on the POST data
        $hmac = hash_hmac('sha512', $postData, $this->private_key);

        // Create cURL handle and initialize (if needed)
        $cUrl = curl_init($this->apiUrl);
        curl_setopt($cUrl, CURLOPT_FAILONERROR, true);
        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cUrl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($cUrl, CURLOPT_HTTPHEADER, array('HMAC: ' . $hmac));
        curl_setopt($cUrl, CURLOPT_POSTFIELDS, $postData);

        if (!($data = curl_exec($cUrl))){
            return ['status' => false, 'message' => 'Request failed try again'];
        }
        $response = json_decode($data, true);
        if($response['error'] != "ok"){
            return ['status' => false, 'message' => $response['error']];
        }
        $data = \array_merge($response, ['status' => true, 'message' => "Request Has Been Successful"], $this->params == true ? ['params' => $req] : []);
        return $data;
    }


     /**
      * @method checking facades

      */
      public function checkEnv(){
          $env = $this->required_env_data();
          throw new HelperJson(true, 'Env file data has been read', [ 'env' => $env]);
      }

      /**
      * @method checking property
      */
      public function checkProperty(){
        throw new HelperJson(true, 'All Property has been used', [ 
            "properties" => [
                'public_key' => $this->public_key,
                'private_key' => $this->private_key,
                'ipn_secret' => $this->ipn_secret,
                'format' => $this->format,
                'ipn_url' => $this->ipn_url,
                'marchant' => $this->marchant,
                'debug_mail' => $this->debug_mail,
                'fiat' => $this->fiat,
            ]
        ]);
    }

    /**
     * @method check settings
     */
    public function checkSettings(){
        $error = 0;
        if($this->public_key == null){
            $error += 1;
        }
        if($this->private_key == null){
            $error += 1;
        }
        if($this->ipn_secret == null){
            $error += 1;
        }
        if($this->ipn_url == null){
            $error += 1;
        }
        if($this->marchant == null){
            $error += 1;
        }
        if($this->debug_mail == null){
            $error += 1;
        }
        $status = $error > 0 ? false : true;
        $message = $error == 0 ? 'Settings has been done' : 'Please check the documents carefully. Settings has not completed yet.';
        throw new HelperJson($status, $message, [ 
            "properties" => [
                'public_key' => $this->public_key ? $this->public_key : null,
                'private_key' => $this->private_key ? $this->private_key : null,
                'ipn_secret' => $this->ipn_secret ? $this->ipn_secret : null,
                'format' => $this->format ? $this->format : null,
                'ipn_url' => $this->ipn_url ? $this->ipn_url : null,
                'marchant' => $this->marchant ? $this->marchant : null,
                'debug_mail' => $this->debug_mail ? $this->debug_mail : null,
                'fiat' => $this->fiat ? $this->fiat : null,
            ]
        ]);
    }
    
      

}
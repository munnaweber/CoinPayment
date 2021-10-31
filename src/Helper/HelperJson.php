<?php 
namespace Munna\CoinPayment\Helper;
use Exception;

class HelperJson extends Exception{

    /**
     * @property $status, $messsage, $array
     */

     public $status, $message, $array;

    /**
     * @method constructor
     */

     public function __construct($status, $messsage, $array = []){
        $this->status = $status;
        $this->message = $messsage;
        $this->array = $array;
     }

     /**
      * @method auto render exceptions
      */
    public function render($request){
        $newArray = ['status' => $this->status, 'message' => $this->message];
        $data = array_merge($newArray, $this->array);
        return response($data);
    }

}
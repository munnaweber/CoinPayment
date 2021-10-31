<?php 

namespace Munna\CoinPayment\Facade;
use Illuminate\Support\Facades\Facade;

class CoinPayment extends Facade{
    /**
     * @method facade accessing
     */
    protected static function getFacadeAccessor(){
        return 'CoinPayment';
    }
}
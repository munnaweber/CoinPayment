<?php
namespace Munna\CoinPayment;

use Munna\CoinPayment\CoinPayment;
use Illuminate\Support\ServiceProvider;

class CoinPaymentServiceProvider extends ServiceProvider {

    /**
     * @method register
     */
    public function register(){
        $this->app->singleton('CoinPayment', function($app){
            return new CoinPayment();
        });
    }

    /**
     * @method boot
     */

     public function boot(){
         
     }

}
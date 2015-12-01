<?php

/* 
 * Stelo Payment module
 * Developed By Rodrigo Ribeiro  
 * https://br.linkedin.com/in/rodrigoferreirasantosribeiro
 * 
 * Funded by Stelo
 * http://www.stelo.com.br/
 * 
 * License: OSL 3.0
 */

class Stelo_Wallet_Block_Checkout_Success extends Mage_Checkout_Block_Onepage_Success
{
    public function __construct() {
        parent::__construct();
        
        $this->setTemplate('wallet/checkout/success.phtml');
    }
}
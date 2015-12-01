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

class Stelo_Wallet_Block_Lightbox extends Mage_Checkout_Block_Success {

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('wallet/lightbox.phtml');
        
    }

   
}
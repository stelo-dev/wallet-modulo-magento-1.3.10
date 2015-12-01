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
class Stelo_Wallet_Model_Resource_Walletcustom extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init( 'wallet/walletcustom', 'id');
    }
}
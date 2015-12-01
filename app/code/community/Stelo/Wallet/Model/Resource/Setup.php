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
class Stelo_Wallet_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup {
    public function startSetup()
    {
        $this->getConnection()->startSetup();
        return $this;
    }

    public function endSetup()
    {
        $this->getConnection()->endSetup();
        return $this;
    }
}
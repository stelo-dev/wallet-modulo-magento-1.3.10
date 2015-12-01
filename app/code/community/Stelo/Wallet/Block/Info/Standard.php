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

class Stelo_Wallet_Block_Info_Standard extends Mage_Payment_Block_Info
{
    protected $_paymentSpecificInformation = null;
     
    protected function _construct()
    {
        parent::_construct();
        
        
       
        $this->setTemplate('wallet/info/standard.phtml');
    }
    
    public function getInstallment($order){
        
        $_order = $order;
        $incrementid = $_order->getData('increment_id');
        
        $steloData = Mage::getModel("wallet/api")->checkInstallment($incrementid);
        
        
        return $steloData;
        
    }
}
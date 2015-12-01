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


class Stelo_Wallet_Block_Adminhtml_Sales_Order extends Mage_Adminhtml_Block_Sales_Order {

    public function  __construct()
    {

        parent::__construct();

        $steloId = "";
        Mage::getModel('wallet/api')->checkStatus($steloId);

    }
}
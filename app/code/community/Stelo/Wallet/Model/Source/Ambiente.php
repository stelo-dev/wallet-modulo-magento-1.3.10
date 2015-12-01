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

class Stelo_Wallet_Model_Source_Ambiente
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('adminhtml')->__('Em Teste')),
            array('value' => '1', 'label' =>  Mage::helper('adminhtml')->__('Em Produção')),
        );
    }
}
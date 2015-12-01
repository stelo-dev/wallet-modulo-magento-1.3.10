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

class Stelo_Wallet_Block_Customer_Login extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();

        $clientId =  Mage::getStoreConfig('payment/sso/clientId');
        //$link = urlencode("http://localhost/magentosso/index.php/wallet/index/sso");
        $link =  urlencode(Mage::getUrl('wallet/index/sso',  array('_nosid' => true, '_secure' => true)));

          if(Mage::getStoreConfig('payment/wallet/ambiente')){
            $baseurl = 'https://login.stelo.com.br/';
        }
        else{
           $baseurl = 'https://login.hml.stelo.com.br/';;
        }
      
        $url = $baseurl.'sso/auth/v1/oauth2/authorize?response_type=code&client_id='.$clientId.'&redirect_uri='.$link.'&scope=resource.READ&state=818e21123';
        $this->setUrlStelo($url);

    }

}

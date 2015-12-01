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

class Stelo_Wallet_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        
    }

    //Aguarda uma chamada da Stelo para alterar o status de um pedido
    public function listenerAction() {
//baseurl /wallet/index/listener


        $var = $GLOBALS['HTTP_RAW_POST_DATA'];

        if (!empty($var)) {
            $var = json_decode($var);

            $steloId = $var->steloId;

            $msg = Mage::getModel('wallet/api')->checkStatus($steloId);
            echo $msg;
        }
    }

    public function ssoAction() {

        
        $clientId = Mage::getStoreConfig('payment/sso/clientId');
        $secretClientId = Mage::getStoreConfig('payment/sso/clientSecret');
        //$link = urlencode("http://localhost/magentosso/index.php/wallet/index/sso");
        $isSecure = Mage::app()->getStore()->isCurrentlySecure();
        if($isSecure){
             $link = urlencode(Mage::getUrl('wallet/index/sso',  array('_nosid' => true, '_secure' => true)));
        }else{
            $link = urlencode(Mage::getUrl('wallet/index/sso',  array('_nosid' => true)));
        }
        
        $code = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');


        if (Mage::getStoreConfig('payment/wallet/ambiente')) {
            $url = "https://api.stelo.com.br/sso/auth/v1/oauth2/token/";
        } else {
            $url = "http://200.142.203.223/sso/auth/v1/oauth2/token/";
        }
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Enconding: utf-8"
        );
        $body = "grant_type=authorization_code&code=" . $code . "&client_id=".$clientId."&client_secret=".$secretClientId."&redirect_uri=" . $link;

        
        $returnRequest = Mage::getModel("wallet/api")->SendSSO($url, $header, $body);


        if (Mage::getStoreConfig('payment/wallet/ambiente')) {
            $url = "https://api.stelo.com.br/sso/auth/v1/oauth2/customer/";
        } else {
            $url = "http://200.142.203.223/sso/auth/v1/oauth2/customer/";
        }

        $returnRequest = json_decode($returnRequest);
        $accessToken = $returnRequest->access_token;

        $header = array(
            "Authorization: Bearer " . $accessToken
        );
        $body = false;

        $returnRequest = Mage::getModel("wallet/api")->SendSSO($url, $header, $body);

        $steloCustomer = json_decode($returnRequest);
     
        $this->_saveCustomerStelo($steloCustomer);

  
        Mage::getSingleton('core/session')->setPaymentSteloForce("true");
        
       
        echo '<script>window.parent.location.reload();</script>';
    }

    protected function _saveCustomerStelo($steloCustomer) {

        if ($steloCustomer) {


            $websiteId = Mage::app()->getWebsite()->getId();
            $store = Mage::app()->getStore();

            $customer_name = $steloCustomer->name;
            $customer_lastname = "";
            $customer_email = $steloCustomer->email;
            $customer_cpf = $steloCustomer->cpf;
            $customer_rg = $steloCustomer->rg;
            $customer_dob = $steloCustomer->birthDate;
            $customer_genger = $steloCustomer->gender;
            $customer_mobile = ""; 
            foreach ($steloCustomer->phones as $phone) {
               
                if ($phone->type == 2) {
                    $customer_mobile = $phone->number;
                }
                if ($phone->type == 1) {
                    $customer_address_telephone = $phone->number;
                }
            }
            
            if(empty($customer_address_telephone)){
               $customer_address_telephone = $customer_mobile; 
            }


            $_dob = $steloCustomer->birthDate;
            $_taxvat = $steloCustomer->cpf;


            if (count(explode(' ', $customer_name)) >= 2)
                list($customer_name, $customer_lastname) = explode(' ', $customer_name, 2);


            $customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($customer_name)
                    ->setLastname($customer_lastname)
                    ->setEmail($customer_email)
                    ->setPassword(rand(100000, 900000))
                    ->setDob($customer_dob);
            $customer->setTaxvat($customer_cpf);
            $customer->setCpfcnpj($customer_cpf);
            $customer->setCpfCnpj($customer_cpf);
            $customer->setTipopessoa(1); 
            $customer->setCpf($customer_cpf);
            $customer->setRg($customer_rg);


            try {

                //verificando grupo
                $targetGroup = Mage::getModel('customer/group');
                $targetGroup->load('Stelo', 'customer_group_code');

                if($targetGroup->getId())
                {
                     $customer->setGroupId($targetGroup->getId());
                }

                //salvando cliente
                $customer->save();



                foreach ($steloCustomer->address as $address) {

                 

                    $customer_address_street1 = $address->street;

                    $customer_address_street2 = $address->number;
                    $customer_address_street3 = $address->complement;
                    $customer_address_street4 = $address->neighborhood;
                    $customer_address_city = $address->city;

                    if($statecheck = $this->checkState($address->state))
                         $customer_address_state_id = $statecheck;

                    $customer_address_state = $address->state; 
                    $customer_address_zipcode = $address->zipCode;

                    $customer_address_country = "BR";

                    $customer = Mage::getModel("customer/customer");
                    $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                    $customer->loadByEmail($customer_email); //load customer by email id - See more at: http://www.techdilate.com/code/magento-get-customer-details-by-email-id/#sthash.lqbLu0RH.dpuf
                    //criando endereço
                    $_custom_address = array(
                        'firstname' => $customer_name,
                        'lastname' => $customer_lastname,
                        'street' => array(
                            '0' => $customer_address_street1,
                            '1' => $customer_address_street2,
                            '2' => $customer_address_street3,
                            '3' => $customer_address_street4
                        ),
                        'number'=> $customer_address_street2,
                        'neighborhood'=> $customer_address_street4,
                        'city' => $customer_address_city,
                        'region_id' => $customer_address_state_id,
                        'region' => $customer_address_state,
                        'postcode' => $customer_address_zipcode,
                        'country_id' => 'BR', /* Croatia */
                        'telephone' => $customer_address_telephone,
                        'fax' => $customer_mobile
                    );

                    $customAddress = Mage::getModel('customer/address');
                    $customAddress->setData($_custom_address)
                            ->setCustomerId($customer->getId())
                            ->setIsDefaultBilling('1')
                            ->setIsDefaultShipping('1')
                            ->setSaveInAddressBook('1');

                    try {
                        $customAddress->save();
                    } catch (Exception $ex) {
                        //Zend_Debug::dump($ex->getMessage());
                    }
                }

                //logando cliente
                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                $customer->loadByEmail(trim($customer_email));
                Mage::getSingleton('customer/session')->loginById($customer->getId());
            } catch (Exception $e) {

               
                //logando cliente
                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                $customer->loadByEmail(trim($customer_email));

              //  $targetGroup = Mage::getModel('customer/group');
              //  $targetGroup->load('Stelo', 'customer_group_code');

                if($targetGroup->getId())
                {
                    $customer->setGroupId($targetGroup->getId());
                    $customer->save(); 
                }

                Mage::getSingleton('customer/session')->loginById($customer->getId());
            }
        }
    }

    protected function checkState($state)
    {
        switch ($state) {
            case 'AC': $uf =  "Acre"; break;
            case 'AL': $uf =  "Alagoas"; break;
            case 'AP': $uf =  "Amapá"; break;
            case 'AM': $uf =  "Amazonas"; break;
            case 'BA': $uf =  "Bahia"; break;
            case 'CE': $uf =  "Ceará"; break;
            case 'DF': $uf =  "Distrito Federal"; break;
            case 'ES': $uf =  "Espírito Santo"; break;
            case 'GO': $uf =  "Goiás"; break;
            case 'MA': $uf =  "Maranhão"; break;
            case 'MT': $uf =  "Mato Grosso"; break;
            case 'MS': $uf =  "Mato Grosso do Sul"; break;
            case 'MG': $uf =  "Minas Gerais"; break;
            case 'PA': $uf =  "Pará"; break;
            case 'PB': $uf =  "Paraíba"; break;
            case 'PR': $uf =  "Paraná"; break;
            case 'PE': $uf =  "Pernambuco"; break;
            case 'PI': $uf =  "Piauí"; break;
            case 'RJ': $uf =  "Rio de Janeiro"; break;
            case 'RN': $uf =  "Rio Grande do Norte"; break;
            case 'RS': $uf =  "Rio Grande do Sul"; break;
            case 'RO': $uf =  "Rondônia"; break;
            case 'RR': $uf =  "Roraima"; break;
            case 'SC': $uf =  "Santa Catarina"; break;
            case 'SP': $uf =  "São Paulo"; break;
            case 'SE': $uf =  "Sergipe"; break;
            case 'TO': $uf =  "Tocantins"; break;

        }

        $regions = Mage::getModel('directory/country')->load('BR')->getRegions();
        foreach($regions as $region)
        {
            if($uf == $region['name'] || $state == $region['name'])
                return $region['region_id'];
        }

        return false;


    }

}

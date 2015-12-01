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
class Stelo_Wallet_Model_Observer extends Varien_Object {

    public function sendWallet($observer) {
    /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getPayment()->getMethodInstance()->getCode() == 'wallet') {
            $wallet = Mage::getModel('wallet/wallet');
            $customerData = $wallet->getCustomerData($order);
            $orderData = $wallet->getOrderData();
            $orderData['orderId'] = $order->getIncrementId();
            $paymentData = $wallet->getPaymentData($order);

            $jsonStrutcture = array(
                "orderData" => $orderData,
                "paymentData" => $paymentData,
                "customerData" => $customerData
            );

            $url = Mage::helper('wallet')->getUrl();
            $url .= "/wallet/transactions";

            $body = json_encode($jsonStrutcture);
            
			
            $clientId = Mage::getStoreConfig('payment/wallet/clientId');
            $clientSecret = Mage::getStoreConfig('payment/wallet/clientSecret');
            $auth = base64_encode($clientId . ":" . $clientSecret);

            $header = array(
                "Authorization: " . $auth,
                "Content-Type: application/json"
            );

            Mage::log($body, null, "request.log", true);
            
            $returnRequest = Mage::getModel("wallet/api")->SendTemplate($url, $header, $body, "CURLOPT_POST");
                       Mage::log($returnRequest, null, "return.log", true);
            $returnRequest = json_decode($returnRequest);
            $hrefUrl = "@href";
            $urlWallet = $returnRequest->link[2]->$hrefUrl;
            $steloId = sprintf('%.0f', $returnRequest->orderData->orderId);
            Mage::getModel('wallet/api')->createNewSteloOrder($orderData['orderId'], $steloId, "new", $urlWallet);
            Mage::getSingleton('core/session')->setWalletUrl($urlWallet);
            Mage::getModel('wallet/api')->checkStatus($steloId);
        
        }
    }
    
  public function addButtonCancel($observer) {

      $block = $observer->getBlock();

      if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {

          $_order = $observer->getBlock()->getOrder();
          $orderRealId = $_order->getRealOrderId();
          $order = new Mage_Sales_Model_Order();
          $order->loadByIncrementId($orderRealId);
          $payment_method = $order->getPayment()->getMethodInstance()->getCode();

          //$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "?orderId=" . $orderRealId;
          $url = Mage::helper("adminhtml")->getUrl("wallet/adminhtml_index/cancelorder", array("orderid" => $orderRealId , "simpleid" => $_order->getId()));


          if($payment_method == "wallet" || $payment_method == "subadquirencia"){

              $block->addButton('cancelarPedido', array(
                  'label'     => Mage::helper('Sales')->__('Cancelar Pedido Stelo'),
                  'onclick'   => "confirmSetLocation('Deseja solicitar o cancelamento dessa compra na stelo?', '{$url}')",
                  'class'     => 'go'
              ), 0, 100, 'header', 'header');

          }

    }

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order) {

            //$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "?consultaStelo=true";
            $url = Mage::helper("adminhtml")->getUrl("wallet/adminhtml_index/statusorder");

            $block->addButton('checkStatusStelo', array(
                'label'     => Mage::helper('Sales')->__('Consultar Status Stelo'),
                'onclick'   => "setLocation('{$url}')",
                'class'     => 'go'
            ), 0, 100, 'header', 'header');



        }

  }

}
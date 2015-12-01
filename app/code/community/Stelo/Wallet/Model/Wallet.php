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

class Stelo_Wallet_Model_Wallet extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'wallet';
    protected $_formBlockType = 'wallet/form_wallet';
    protected $_infoBlockType = 'wallet/info_standard';

    //Dados do comprador
    public function getCustomerData($order) {

        $order = $order->getQuote();
        $billingAddress["street"] = $order->getBillingAddress()->getStreet1();
        $billingAddress["number"] = $order->getBillingAddress()->getStreet2();
        if (!isset($billingAddress["number"]))
            $billingAddress["number"] = $order->getBillingAddress()->getNumero();
        $billingAddress["complement"] = $order->getBillingAddress()->getStreet3();
        $billingAddress["neighborhood"] = $order->getBillingAddress()->getStreet4();
        if (!isset($billingAddress["neighborhood"]))
            $billingAddress["neighborhood"] = $order->getBillingAddress()->getBairro();
        $billingAddress["zipCode"] = preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getPostcode());
        $billingAddress["city"] = $order->getBillingAddress()->getCity();
        $billingAddress["state"] = $order->getBillingAddress()->getRegion();
        $billingAddress["state"] = Mage::helper('wallet')->ConvertState(utf8_decode($billingAddress["state"]));
        $billingAddress["country"] = "BR";



        $shippingAddress["street"] = $order->getShippingAddress()->getStreet1();
        $shippingAddress["number"] = $order->getShippingAddress()->getStreet2();
        if (!isset($shippingAddress["number"]))
            $shippingAddress["number"] = $order->getShippingAddress()->getNumero();
        $shippingAddress["complement"] = $order->getShippingAddress()->getStreet3();
        $shippingAddress["neighborhood"] = $order->getShippingAddress()->getStreet4();
        if (!isset($shippingAddress["neighborhood"]))
            $shippingAddress["neighborhood"] = $order->getShippingAddress()->getBairro();
        $shippingAddress["zipCode"] = preg_replace("/[^0-9]/", "", $order->getShippingAddress()->getPostcode());
        $shippingAddress["city"] = $order->getShippingAddress()->getCity();
        $shippingAddress["state"] = $order->getShippingAddress()->getRegion();
        $shippingAddress["state"] = Mage::helper('wallet')->ConvertState(utf8_decode($shippingAddress["state"]));
        $shippingAddress["country"] = "BR";

        if(empty($order->getBillingAddress()->getFax())){
            $number = "";
        }else
        {
            $number = $order->getBillingAddress()->getFax();
        }

        $phoneData = array(
            array(
                'type' => 'LANDLINE', //phoneType
                'number' => $order->getBillingAddress()->getTelephone()
            ),
            array(
                'type' => 'CELL', //phoneType
                'number' => $number
            ),
        );

        $customerData["customerIdentity"] = preg_replace("/[^0-9]/", "", $order->getCustomerTaxvat());
        if (empty($customerData["customerIdentity"]))
            $customerData["customerIdentity"] = preg_replace("/[^0-9]/", "", $order->getCustomerCpf());

        if (empty($customerData["customerIdentity"]))
            $customerData["customerIdentity"] = preg_replace("/[^0-9]/", "", $order->getCustomer()->getCpfcnpj());

        if (empty($customerData["customerIdentity"]))
            $customerData["customerIdentity"] = preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getCpfcnpj());

        $customerData["customerName"] = $order->getCustomerFirstname() . " " . $order->getCustomerLastname();
        $customerData["customerEmail"] = $order->getCustomerEmail();
       // $customerData["birthDate"] = substr($order->getCustomer()->_data["dob"], 0, 10);
        $customerData["billingAddress"] = $billingAddress;
        $customerData["shippingAddress"] = $shippingAddress;
        $customerData["phoneData"] = $phoneData;



        return $customerData;
    }

    //Captura dados da ordem
    public function getOrderData() {

        $orderData = array();
        $orderData['plataformId'] = "1";
        $orderData['transactionType'] = "w";
        $orderData['shippingBehavior'] = "default";
        $orderData['country'] = "BR";

        return $orderData;
    }

    public function getPaymentData($order) {

        $totals = Mage::getSingleton('checkout/cart')->getQuote()->getTotals();
        $grandTotal = $totals["grand_total"]->getValue();

        /*if (isset($totals['discount']) && $totals['discount']->getValue()) {
            $discount = Mage::helper('core')->currency($totals['discount']->getValue()); //Discount value if applied
            $discount = number_format($discount, 2, '.', '');
        } else {
            $discount = '';
        }
        */
        if (isset($totals['tax']) && $totals['tax']->getValue()) {
            $tax = $totals['tax']->getValue(); //Tax value if present
        } else {
            $tax = '';
        }
         
        $quote = $order->getQuote();
        $discount = $order->getDiscountAmount();

        $paymentData["amount"] =  number_format($grandTotal, 2, '.', '');
        $paymentData["discountAmount"] = number_format($discount, 2, '.', '');
        $paymentData["freight"] =  number_format($quote->getShippingAddress()->getShippingAmount(), 2, '.', '');
        $paymentData["currency"] = "BRL";
//        $paymentData["maxInstallment"] = Mage::getStoreConfig('payment/wallet/maxInstallment');

       
        $cartItems = $quote->getAllVisibleItems();
        $cont = 0;
        foreach ($cartItems as $item) {

            $productId = $item->getProductId();
            $product = Mage::getModel('catalog/product')->load($productId)->getData();
          
            
            $itemsData["productName"] = $item->getName();
            $itemsData["productPrice"] =   Mage::helper('tax')->getPrice($item->getProduct(), $item->getProduct()->getFinalPrice(), true);
            $itemsData["productQuantity"] = $item->getQty();
            $itemsData["productSku"] = substr(ereg_replace("[^a-zA-Z0-9_]", "", $item->getSku()), 0, 8);

            $itemsCollection[$cont] = $itemsData;

            $cont++;
        }

        $paymentData["cartData"] = $itemsCollection;

        return $paymentData;
    }
    
       public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('wallet/onepage/success', array('_secure' => true));
    }

}
?>

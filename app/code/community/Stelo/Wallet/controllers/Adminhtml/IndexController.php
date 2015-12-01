<?php

class Stelo_Wallet_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
     
        
    }

    public function statusorderAction()
    {
        $return = Mage::getModel('wallet/api')->checkStatus();
        Mage::getSingleton("core/session")->addSuccess("Os status dos pedidos foram atualizados");

        $this->_redirect("adminhtml/sales_order");
    }

    public function cancelorderAction()
    {
        $par = $this->getRequest()->getParams();
        if(isset($par['orderid'])){

            $return = Mage::getModel('wallet/api')->cancelOrder($par['orderid']);
            $return = json_encode($return);

            Mage::getSingleton("core/session")->addSuccess("A solicitação de cancelamento do pedido foi realizada com sucesso");
        }

        $this->_redirect("adminhtml/sales_order/view", array('order_id' => $par['simpleid']));

    }

}
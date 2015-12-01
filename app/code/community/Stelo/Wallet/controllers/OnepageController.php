<?php
/*
 * Stelo payment module
 * 
 * Cristhian Andrade
 * http://cristhian.net
 * 
 * Funded by Stelo
 * http://www.stelo.com.br/
 * 
 * License: OSL 3.0
 */

class Stelo_Wallet_OnepageController extends Mage_Checkout_Controller_Action{

  public function successAction(){

    $session = $this->getOnepage()->getCheckout();
    if (!$session->getLastSuccessQuoteId()) {
        $this->_redirect('checkout/cart');
        return;
    }

    $lastQuoteId = $session->getLastQuoteId();
    $lastOrderId = $session->getLastOrderId();
    $lastRecurringProfiles = $session->getLastRecurringProfileIds();
    if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
        $this->_redirect('checkout/cart');
        return;
    }

    //$session->clear();
    $this->loadLayout();
    $this->_initLayoutMessages('checkout/session');
    Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
    $this->renderLayout();

  }

  public function getOnepage()
  {
      return Mage::getSingleton('checkout/type_onepage');
  }

}

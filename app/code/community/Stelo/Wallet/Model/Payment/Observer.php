<?php
/**
 * Copyright © 2011
 * netz98 new media GmbH. All rights reserved.
 *
 * The use and redistribution of this software, either compiled or uncompiled,
 * with or without modifications are permitted provided that the
 * following conditions are met:
 *
 * 1. Redistributions of compiled or uncompiled source must contain the above
 * copyright notice, this list of the conditions and the following disclaimer:
 *
 * 2. All advertising materials mentioning features or use of this software must
 * display the following acknowledgement:
 * "This product includes software developed by the netz98 new media GmbH, Mainz."
 *
 * 3. The name of the netz98 new media GmbH may not be used to endorse or promote
 * products derived from this software without specific prior written permission.
 *
 * 4. License holders of the netz98 new media GmbH are only permitted to
 * redistribute altered software, if this is licensed under conditions that contain
 * a copyleft-clause.
 *
 * This software is provided by the netz98 new media GmbH without any express or
 * implied warranties. netz98 is under no condition liable for the functional
 * capability of this software for a certain purpose or the general usability.
 * netz98 is under no condition liable for any direct or indirect damages resulting
 * from the use of the software.
 * Liability and Claims for damages of any kind are excluded.
 */

/**
 * Observer to limit access to customer groups by customer group
 *
 * @category n98
 * @package Netz98_CustomerGroup
 */
class Stelo_Wallet_Model_Payment_Observer
{
    /**
     * @var string
     */
    const PAYMENT_SSO_ACTIVE = 'payment/sso/active';

    /**
     * Check if customer group can use the payment method
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    public function methodIsAvailable(Varien_Event_Observer $observer)
    {

        if(Mage::getStoreConfigFlag(self::PAYMENT_SSO_ACTIVE) && Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $paymentMethodInstance = $observer->getMethodInstance();
            /* @var $paymentMethodInstance Mage_Payment_Model_Method_Abstract */
            $result = $observer->getResult();
            $targetGroup = Mage::getModel('customer/group');
            $targetGroup->load(Mage::getSingleton('customer/session')->getCustomerGroupId());

            if($paymentMethodInstance->getCode() != 'wallet' && $targetGroup->getData('customer_group_code') == 'Stelo')
            {
                $result->isAvailable = false;
            } 

            return true;
        }
    }
}

<?php

class Phoenix_VarnishCache_Model_Personalisationcookie {
    const CONFIG_ENABLED                      = 'system/personalisation_cookie/enabled';
    const CONFIG_COOKIE_KEY                   = 'system/personalisation_cookie/cookie_key';
    const CONFIG_SEND_CART_COUNT              = 'system/personalisation_cookie/send_cart_count';
    const CONFIG_SEND_CART_SUBTOTAL           = 'system/personalisation_cookie/send_cart_subtotal';
    const CONFIG_SEND_WISHLIST_COUNT          = 'system/personalisation_cookie/send_wishlist_count';
    const CONFIG_SEND_CUSTOMER_FIRST_NAME     = 'system/personalisation_cookie/send_customer_first_name';
    const CONFIG_SEND_CUSTOMER_FULL_NAME      = 'system/personalisation_cookie/send_customer_full_name';
    const CONFIG_SEND_CUSTOMER_EMAIL          = 'system/personalisation_cookie/send_customer_email';
    const CONFIG_SELECTOR_CART_COUNT          = 'system/personalisation_cookie/selector_cart_count';
    const CONFIG_SELECTOR_CART_SUBTOTAL       = 'system/personalisation_cookie/selector_cart_subtotal';
    const CONFIG_SELECTOR_WISHLIST_COUNT      = 'system/personalisation_cookie/selector_wishlist_count';
    const CONFIG_SELECTOR_CUSTOMER_FIRST_NAME = 'system/personalisation_cookie/selector_customer_first_name';
    const CONFIG_SELECTOR_CUSTOMER_FULL_NAME  = 'system/personalisation_cookie/selector_customer_full_name';
    const CONFIG_SELECTOR_CUSTOMER_EMAIL      = 'system/personalisation_cookie/selector_email';
    
    public function setDynamicHeaderCookie(){
        if (Mage::getStoreConfig(self::CONFIG_ENABLED)) {
            $oSession = Mage::getSingleton('customer/session');
            if($oSession->isLoggedIn()){
                $aCookieData = array();
                
                if (Mage::getStoreConfig(self::CONFIG_SEND_CART_SUBTOTAL)) {
                    $vCartSubtotal = '$'.__(number_format(Mage::getSingleton('checkout/session')->getQuote()->getSubtotal(),2));
                    $aCookieData['cart_subtotal'] = $vCartSubtotal;
                }
                
                if (Mage::getStoreConfig(self::CONFIG_SEND_CART_COUNT)) {
                    $aCookieData['cart_count'] = Mage::helper('checkout/cart')->getSummaryCount();
                }
                
                if (Mage::getStoreConfig(self::CONFIG_SEND_WISHLIST_COUNT)) {
                    $aCookieData['wishlist_count'] = Mage::helper('wishlist')->getItemCount();
                }
                
                if (Mage::getStoreConfig(self::CONFIG_SEND_CUSTOMER_FIRST_NAME)) {
                    $aCookieData['customer_first_name'] = $oSession->getCustomer()->getFirstname();
                }
                
                if (Mage::getStoreConfig(self::CONFIG_SEND_CUSTOMER_FULL_NAME)) {
                    $aCookieData['customer_full_name'] = trim($oSession->getCustomer()->getFirstname()." ".$oSession->getCustomer()->getLastname());
                }
                
                if (Mage::getStoreConfig(self::CONFIG_SEND_CUSTOMER_EMAIL)) {
                    $aCookieData['customer_email'] = $oSession->getCustomer()->getEmail();
                }
                
                $vCookieJson = Mage::helper('core')->jsonEncode($aCookieData);
                Mage::getModel('core/cookie')->set(Mage::getStoreConfig(self::CONFIG_COOKIE_KEY),$vCookieJson,3600,'/',Mage::app()->getRequest()->getHttpHost(),false,false);
            }
        }
    }
    
    public function unsetDynamicHeaderCookie(){
        $oSession = Mage::getSingleton('customer/session');
        if(!$oSession->isLoggedIn()){
            Mage::getModel('core/cookie')->delete(Mage::getStoreConfig(self::CONFIG_COOKIE_KEY));
        }
    }

}

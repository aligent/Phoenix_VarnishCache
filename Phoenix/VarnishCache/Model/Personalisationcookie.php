<?php

class Phoenix_VarnishCache_Model_Personalisationcookie {
    const CONFIG_ENABLED                      = 'system/personalisation_cookie/enabled';
    const CONFIG_COOKIE_KEY                   = 'system/personalisation_cookie/cookie_key';
    const CONFIG_SEND_TO_ALL_USERS            = 'system/personalisation_cookie/send_to_all_users';
    const CONFIG_SEND_CART_COUNT              = 'system/personalisation_cookie/send_cart_count';
    const CONFIG_SEND_CART_SUBTOTAL           = 'system/personalisation_cookie/send_cart_subtotal';
    const CONFIG_SEND_WISHLIST_COUNT          = 'system/personalisation_cookie/send_wishlist_count';
    const CONFIG_SEND_CUSTOMER_FIRST_NAME     = 'system/personalisation_cookie/send_customer_first_name';
    const CONFIG_SEND_CUSTOMER_FULL_NAME      = 'system/personalisation_cookie/send_customer_full_name';
    const CONFIG_SEND_CUSTOMER_EMAIL          = 'system/personalisation_cookie/send_customer_email';
    const CONFIG_SEND_LOGGED_IN               = 'system/personalisation_cookie/send_logged_in';
    const CONFIG_SELECTOR_CART_COUNT          = 'system/personalisation_cookie/selector_cart_count';
    const CONFIG_SELECTOR_CART_SUBTOTAL       = 'system/personalisation_cookie/selector_cart_subtotal';
    const CONFIG_SELECTOR_WISHLIST_COUNT      = 'system/personalisation_cookie/selector_wishlist_count';
    const CONFIG_SELECTOR_CUSTOMER_FIRST_NAME = 'system/personalisation_cookie/selector_customer_first_name';
    const CONFIG_SELECTOR_CUSTOMER_FULL_NAME  = 'system/personalisation_cookie/selector_customer_full_name';
    const CONFIG_SELECTOR_CUSTOMER_EMAIL      = 'system/personalisation_cookie/selector_email';
    const CONFIG_SELECTOR_LOGGED_IN           = 'system/personalisation_cookie/selector_logged_in';
    const CONFIG_SELECTOR_LOGGED_OUT          = 'system/personalisation_cookie/selector_logged_out';
    
    protected $_aCookieData = array();
    
    public function setCookieValue($vKey, $vValue) {
        $this->_aCookieData[$vKey] = $vValue;
    }
    
    public function updatePersonalisationCookie(){
        if (Mage::getStoreConfig(self::CONFIG_ENABLED)
                && !Mage::registry('personalisation_cookie_set')) {
            $oSession = Mage::getSingleton('customer/session');
            $bLoggedIn = $oSession->isLoggedIn();
            
            if (Mage::getStoreConfig(self::CONFIG_SEND_CART_SUBTOTAL)) {
                $vCartSubtotal = '$'.__(number_format(Mage::getSingleton('checkout/session')->getQuote()->getSubtotal(),2));
                $this->setCookieValue('cart_subtotal', $vCartSubtotal);
            }

            if (Mage::getStoreConfig(self::CONFIG_SEND_CART_COUNT)) {
                $this->setCookieValue('cart_count', Mage::helper('checkout/cart')->getSummaryCount() !== null ? Mage::helper('checkout/cart')->getSummaryCount() : 0);
            }

            if (Mage::getStoreConfig(self::CONFIG_SEND_WISHLIST_COUNT)) {
                $this->setCookieValue('wishlist_count',  Mage::helper('wishlist')->getItemCount());
            }

            if (Mage::getStoreConfig(self::CONFIG_SEND_CUSTOMER_FIRST_NAME)) {
                $this->setCookieValue('customer_first_name',  $bLoggedIn ? $oSession->getCustomer()->getFirstname() : '');
            }

            if (Mage::getStoreConfig(self::CONFIG_SEND_CUSTOMER_FULL_NAME)) {
                $this->setCookieValue('customer_full_name',  $bLoggedIn ? trim($oSession->getCustomer()->getFirstname()." ".$oSession->getCustomer()->getLastname()) : '');
            }

            if (Mage::getStoreConfig(self::CONFIG_SEND_CUSTOMER_EMAIL)) {
                $this->setCookieValue('customer_email',  $bLoggedIn ? $oSession->getCustomer()->getEmail() : '');
            }

            if (Mage::getStoreConfig(self::CONFIG_SEND_LOGGED_IN)) {
                $this->setCookieValue('logged_in',  $bLoggedIn ? 'true' : '');
            }
            
            Mage::dispatchEvent('personalisation_cookie_data_prepared', array('cookie_model' => $this));

            $vCookieJson = Mage::helper('core')->jsonEncode($this->_aCookieData);
            Mage::getModel('core/cookie')->set($this->getCookieName(),$vCookieJson,3600,null,null,false,false);
            Mage::register('personalisation_cookie_set',true);
        }
    }
    
    public function getCookieName() {
        return Mage::getStoreConfig(self::CONFIG_COOKIE_KEY);;
    }
    
    public function  deletePersonalisationCookie() {
        Mage::getModel('core/cookie')->delete(Mage::getStoreConfig(self::CONFIG_COOKIE_KEY),null,null,false,false);
    }
    
    
    public function checkHasPersonalisationCookie() {
        if (Mage::getStoreConfig(self::CONFIG_SEND_TO_ALL_USERS)) {
            $vCookie = Mage::getModel('core/cookie')->get(Mage::getStoreConfig(self::CONFIG_COOKIE_KEY));
            if ($vCookie === false) {
                $this->updatePersonalisationCookie();
            }
        }
    }

}

<?php

class Phoenix_VarnishCache_Model_Personalisationcookie {
    const CONFIG_ENABLED                      = 'system/personalisation_cookie/enabled';
    const CONFIG_COOKIE_KEY                   = 'system/personalisation_cookie/cookie_key';
    const CONFIG_SEND_TO_ALL_USERS            = 'system/personalisation_cookie/send_to_all_users';
    const CONFIG_SEND_CART_COUNT              = 'system/personalisation_cookie/send_cart_count';
    const CONFIG_SEND_CART_SUBTOTAL           = 'system/personalisation_cookie/send_cart_subtotal';
    const CONFIG_SEND_CART_SUBTOTAL_EX_SHIPPING = 'system/personalisation_cookie/send_cart_subtotal_ex_shipping';
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
    const CONFIG_MESSAGES_COOKIE_ENABLED      = 'system/personalisation_cookie/enable_messages_cookie';

    const COOKIE_MESSAGES_KEY = 'mage_msgs';

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
                $oQuote = Mage::getSingleton('checkout/session')->getQuote();
                $vCartGrandTotal = $oQuote->getGrandTotal();

                if (Mage::getStoreConfig(self::CONFIG_SEND_CART_SUBTOTAL_EX_SHIPPING)) {
                    $vCartShippingTotal = ($oQuote->getShippingAddress()->getShippingAmount() + $oQuote->getShippingAddress()->getShippingTaxAmount());
                    $vCartGrandTotal -= $vCartShippingTotal;
                }

                $this->setCookieValue('cart_subtotal', Mage::helper('core')->formatPrice($vCartGrandTotal, false));
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
            Mage::getModel('core/cookie')->set($this->getCookieName(),$vCookieJson,Mage::getStoreConfig('web/cookie/cookie_lifetime'),null,null,false,false);
            Mage::register('personalisation_cookie_set',true);
        }
    }
    
    public function getCookieName() {
        return Mage::getStoreConfig(self::CONFIG_COOKIE_KEY);;
    }
    
    public function  deletePersonalisationCookie() {
        Mage::getModel('core/cookie')->delete($this->getCookieName(),null,null,false,false);
    }
    
    
    public function checkHasPersonalisationCookie() {
        /**
         * using $_GET instead of request object because that's how core uses it
         * @see Mage_Core_Model_App::_checkGetStore()
         *
         * if store is switched regenerate personal cookie.
         * otherwise mini-cart will display incorrect cart count
         */
        if (isset($_GET['___store']) || (Mage::registry('personalisation_cookie_force_regenerate') === true)) {
            $this->updatePersonalisationCookie();
        }elseif (Mage::getStoreConfig(self::CONFIG_SEND_TO_ALL_USERS)) {
            $vCookie = Mage::getModel('core/cookie')->get($this->getCookieName());
            if ($vCookie === false) {
                $this->updatePersonalisationCookie();
            }
        }
    }

    public function setMessageCookie($oObserver) {
        if (Mage::getStoreConfigFlag(self::CONFIG_MESSAGES_COOKIE_ENABLED) == false){
            return $this;
        }
        $aMessageCodes = array();
        $aSessionObjects = array(Mage::getSingleton('customer/session'), Mage::getSingleton("checkout/session"), Mage::getSingleton('catalog/session'));
        foreach ($aSessionObjects as $oSession) {
            $aMessages = $oSession->getMessages(true);
            if (!empty($aMessages)) {
                foreach ($aMessages->getItems() as $oMessage) {
                    if($oMessage->getType() == 'success'){
                        $aMessageCodes[] = $oMessage->getCode();

                    } else{
                        // Need to add errors/notices/warnings back into session because getMessages above has removed them.
                        switch ($oMessage->getType()) {
                            case Mage_Core_Model_Message::ERROR:
                                $oSession->addError($oMessage->getCode());
                                break;
                            case Mage_Core_Model_Message::NOTICE:
                                $oSession->addNotice($oMessage->getCode());
                                break;
                            case Mage_Core_Model_Message::WARNING:
                                $oSession->addWarning($oMessage->getCode());
                                break;

                        }
                        Mage::app()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));  //redirect to a page we know is not cached to display errors/warnings
                        break;
                    }
                }
            }
        }

        // set collected messages in messages cookie
        if (!empty($aMessageCodes)) {
            // TODO: Provide front end to render this cookie
            $vEncodedMessages = json_encode($aMessageCodes);
            setrawcookie(self::COOKIE_MESSAGES_KEY,
                rawurlencode($vEncodedMessages),
                time() + Mage::getStoreConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_LIFETIME),
                '/',
                Mage::getStoreConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_DOMAIN),
                false,
                false);
        }
    }

}

<?php
    
class Phoenix_Varnishcache_Block_Personalisationcookie extends Mage_Core_Block_Template {
    const CACHE_LIFETIME = 14400; // 4hrs
    
    public function _construct() {
        $this->addData(
            array(
                'cache_lifetime' => self::CACHE_LIFETIME,
                'cache_tags' => array(Mage_Core_Model_Config::CACHE_TAG)
            )
        );
    }
    
    public function getCacheKeyInfo() {
        return array(
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->isLoggedIn(),
            $this->getTemplate(),
        );
        
    }
    
    
    public function isEnabled() {
        if (!$this->getData('enabled')) {
            $this->setData('enabled', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_ENABLED));
        }
        return $this->getData('enabled');
    }
    
    public function getCookieKey() {
        if (!$this->getData('cookie_key')) {
            $this->setData('cookie_key', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_COOKIE_KEY));
        }
        return $this->getData('cookie_key');
    }

    public function isCartCountEnabled() {
        if (!$this->getData('send_cart_count')) {
            $this->setData('send_cart_count', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SEND_CART_COUNT));
        }
        return $this->getData('send_cart_count');
    }

    public function getCartCountSelector() {
        if (!$this->getData('cart_count_selector')) {
            $this->setData('cart_count_selector', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SELECTOR_CART_COUNT));
        }
        return $this->getData('cart_count_selector');
    }

    public function isCartSubtotalEnabled() {
        if (!$this->getData('send_cart_subtotal')) {
            $this->setData('send_cart_subtotal', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SEND_CART_SUBTOTAL));
        }
        return $this->getData('send_cart_subtotal');
    }

    public function getCartSubtotalSelector() {
        if (!$this->getData('cart_subtotal_selector')) {
            $this->setData('cart_subtotal_selector', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SELECTOR_CART_SUBTOTAL));
        }
        return $this->getData('cart_subtotal_selector');
    }

    public function isWishlistCountEnabled() {
        if (!$this->getData('send_wishlist_count')) {
            $this->setData('send_wishlist_count', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SEND_WISHLIST_COUNT));
        }
        return $this->getData('send_wishlist_count');
    }

    public function getWishlistCountSelector() {
        if (!$this->getData('wishlist_count_selector')) {
            $this->setData('wishlist_count_selector', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SELECTOR_WISHLIST_COUNT));
        }
        return $this->getData('wishlist_count_selector');
    }

    public function isCustomerFirstNameEnabled() {
        if (!$this->getData('send_customer_first_name')) {
            $this->setData('send_customer_first_name', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SEND_CUSTOMER_FIRST_NAME));
        }
        return $this->getData('send_customer_first_name');
    }

    public function getCustomerFirstNameSelector() {
        if (!$this->getData('customer_first_name_selector')) {
            $this->setData('customer_first_name_selector', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SELECTOR_CUSTOMER_FIRST_NAME));
        }
        return $this->getData('customer_first_name_selector');
    }

    public function isCustomerFullNameEnabled() {
        if (!$this->getData('send_customer_full_name')) {
            $this->setData('send_customer_full_name', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SEND_CUSTOMER_FULL_NAME));
        }
        return $this->getData('send_customer_full_name');
    }

    public function getCustomerFullNameSelector() {
        if (!$this->getData('customer_full_name_selector')) {
            $this->setData('customer_full_name_selector', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SELECTOR_CUSTOMER_FULL_NAME));
        }
        return $this->getData('customer_full_name_selector');
    }

    public function isCustomerEmailEnabled() {
        if (!$this->getData('send_customer_email')) {
            $this->setData('send_customer_email', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SEND_CUSTOMER_EMAIL));
        }
        return $this->getData('send_customer_email');
    }

    public function getCustomerEmailSelector() {
        if (!$this->getData('customer_email_selector')) {
            $this->setData('customer_email_selector', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SELECTOR_CUSTOMER_EMAIL));
        }
        return $this->getData('customer_email_selector');
    }
    
    public function isLoggedInToggleEnabled(){
        if (!$this->getData('send_logged_in')) {
            $this->setData('send_logged_in', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SEND_LOGGED_IN));
        }
        return $this->getData('send_logged_in');
    }
    
    public function getLoggedInSelector() {
        if (!$this->getData('logged_in_selector')) {
            $this->setData('logged_in_selector', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SELECTOR_LOGGED_IN));
        }
        return $this->getData('logged_in_selector');
    }
    
    public function getLoggedOutSelector() {
        if (!$this->getData('logged_out_selector')) {
            $this->setData('logged_out_selector', Mage::getStoreConfig(Phoenix_VarnishCache_Model_Personalisationcookie::CONFIG_SELECTOR_LOGGED_OUT));
        }
        return $this->getData('logged_out_selector');
    }
    
}
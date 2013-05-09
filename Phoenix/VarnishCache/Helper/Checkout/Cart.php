<?php
/**
 * Rewrite the checkout/cart helper with a version of getCurrentUrl that doesn't
 * reply on SERVER_PORT which may be inaccurate on Nginx when running behind
 * varnish.  On Nginx the SERVER_PORT variable contains the backend Nginx server
 * port not the port number that received the usr's request.
 */
class Phoenix_VarnishCache_Helper_Checkout_Cart extends Mage_Checkout_Helper_Cart {
    /**
     * Retrieve current url
     *
     * @return string
     */
    public function getCurrentUrl() {
        return Mage::helper('varnishcache/core_url')->getCurrentUrl();
    }
}
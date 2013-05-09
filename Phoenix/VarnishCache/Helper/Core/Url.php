<?php
/**
 * Rewrite the core/url helper with a version of getCurrentUrl that doesn't
 * reply on SERVER_PORT which may be inaccurate on Nginx when running behind
 * varnish.  On Nginx the SERVER_PORT variable contains the backend Nginx server
 * port not the port number that received the usr's request.
 */ 
class Phoenix_VarnishCache_Helper_Core_Url extends Mage_Core_Helper_Url {
    /**
     * Retrieve current url
     *
     * @return string
     */
    public function getCurrentUrl() {
        $request = Mage::app()->getRequest();
        $bSecure = ($request->getScheme() == Mage_Core_Controller_Request_Http::SCHEME_HTTPS);
        $vBaseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, $bSecure);

        // Parse the base url and remove and path that might be part of the base (e.g. /shop)
        $iPathLength = strlen(parse_url($vBaseUrl, PHP_URL_PATH));
        if ($iPathLength > 0) {
            $vBaseUrl = substr($vBaseUrl, 0, $iPathLength * -1);
        }

        $vUrl = rtrim($vBaseUrl, '/') . $request->getServer('REQUEST_URI');
        return $vUrl;
    }

}
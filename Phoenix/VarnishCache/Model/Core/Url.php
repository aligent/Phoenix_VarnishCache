<?php

/**
 * Class Phoenix_VarnishCache_Model_Core_Url
 *
 * Rewrite the core/url model to replace the value of the form_key parameter with a place holder if it's added to a url.
 * This will allow the cookie cutter javascript to replace it later.
 */
class Phoenix_VarnishCache_Model_Core_Url extends Mage_Core_Model_Url {

    /**
     * Build url by requested path and parameters
     * Replaces any form key with a place holder
     *
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null) {
        if (isset($routeParams['form_key'])) {
            $routeParams['form_key'] = Phoenix_VarnishCache_Model_Observer::FORM_KEY_PLACEHOLDER;
        }
        return parent::getUrl($routePath, $routeParams);
    }
}

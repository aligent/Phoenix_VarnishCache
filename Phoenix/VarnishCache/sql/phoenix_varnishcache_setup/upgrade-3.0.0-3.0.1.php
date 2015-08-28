<?php
$installer = $this;
$installer->startSetup();

$aCommonExcludedRoutes = array('checkout', 'customer', 'moneybookers', 'paypal', 'wishlist', 'ustorelocator', 'monkey', 'ebizmarts_autoresponder', 'promos', 'feeds', 'oi');

$aDisabledRoutes = explode("\n", trim(Mage::getStoreConfig('system/varnishcache/disable_routes')));

foreach ($aCommonExcludedRoutes as $vRoute) {
    $vRoute = trim($vRoute);
    if (!empty($vRoute) && !in_array($vRoute, $aDisabledRoutes)) {
        $aDisabledRoutes[] = $vRoute;
    }
}

$installer->setConfigData('system/varnishcache/disable_routes', implode("\n", $aDisabledRoutes));

$installer->endSetup();
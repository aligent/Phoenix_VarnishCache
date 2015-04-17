<?php
$installer = $this;
$installer->startSetup();
$installer->setConfigData('system/varnishcache/disable_routes', 'checkout
customer
moneybookers
paypal
wishlist
ustorelocator
onesaas-connect
monkey
ebizmarts_autoresponder
promos
feeds
oi');

$installer->endSetup();
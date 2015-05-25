<?php
/**
 * PageCache powered by Varnish
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@phoenix-media.eu so we can send you a copy immediately.
 *
 * @category   Phoenix
 * @package    Phoenix_VarnishCache
 * @copyright  Copyright (c) 2011 PHOENIX MEDIA GmbH & Co. KG (http://www.phoenix-media.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Phoenix_VarnishCache_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_VARNISH_CACHE_ENABLED  = 'system/varnishcache/enabled';
    const XML_PATH_VARNISH_CACHE_DEBUG    = 'system/varnishcache/debug';

    /**
     * Check whether Varnish cache is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_VARNISH_CACHE_ENABLED);
    }

    /**
     * Check whether debuging is enabled
     *
     * @return bool
     */
    public function isDebug()
    {
        if (Mage::getStoreConfigFlag(self::XML_PATH_VARNISH_CACHE_DEBUG)) {
            return true;
        }
        return false;
    }

    /**
     * Log debugging data
     *
     * @param string|array
     * @return void
     */
    public function debug($debugData)
    {
        if ($this->isDebug()) {
            Mage::log($debugData, null, 'varnish_cache.log');
        }
    }

    /**
     * @return string
     */
    public function getLicenseFilePath()
    {
        return Mage::getBaseDir() . DS . 'LICENSE_VARNISH_CACHE.lic';
    }

    /**
     * @return string
     */
    public function getLicenseCheckUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('*/varnishCache/checkLicense');
    }

    /**
     * Get Varnish control model
     *
     * @return Phoenix_VarnishCache_Model_Control
     */
    public function getCacheControl()
    {
        return Mage::getSingleton('varnishcache/control');
    }


    /**
     * Will localise and add a message to session in a way that's compatible
     * with unit test and over "non admin" environments.
     *
     * Setup scripts may cause a cache purge, and we won't necessarily
     * have a valid admin session while setup scripts are running.  All
     * setup scripts are executed before the requests is dispatched, so
     * if the request hasn't been dispatched yet, then we can assume
     * setup scripts are running.
     *
     * @param $message
     */
    public function addSuccess($message) {
        if (Mage::app()->getRequest()->isDispatched()) {
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('varnishcache')->__($message)
            );
        }
    }

}

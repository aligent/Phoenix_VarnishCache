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

class Phoenix_VarnishCache_Model_Control_Sitewide
    extends Phoenix_VarnishCache_Model_Control_Abstract
{
    /**
     * Purge all cache items for all stores
     *
     * @return Phoenix_VarnishCache_Model_Control_Sitewide
     */
    public function purge($iStoreId = null)
    {
        $oHelper = Mage::helper('varnishcache');
        if ($oHelper->isEnabled()) {
            $oCacheHelper = Mage::helper('varnishcache/cache');
            $vDomains = $oCacheHelper->getStoreDomainList($iStoreId);
            /** @var $oControl Phoenix_VarnishCache_Model_Control */
            $oControl = $oHelper->getCacheControl();
            $oControl->clean($vDomains);

            // Setup scripts may cause a cache purge, and we won't necessarily
            // have a valid admin session while setup scripts are running.  All
            // setup scripts are executed before the requests is dispatched, so
            // if the request hasn't been dispatched yet, then we can assume
            // setup scripts are running.
            if (Mage::app()->getRequest()->isDispatched()) {
                $this->_getSession()->addSuccess(
                    Mage::helper('varnishcache')->__('Varnish cache has been purged.')
                );
            }
        }
        return $this;
    }

}

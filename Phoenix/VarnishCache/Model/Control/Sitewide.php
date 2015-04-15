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

            Mage::helper('varnishcache')->addSuccess('Varnish cache has been purged.');
        }
        return $this;
    }

}

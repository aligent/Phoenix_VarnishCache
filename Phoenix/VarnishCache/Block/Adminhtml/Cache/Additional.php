<?php
/**
 * PageCache powered by Varnish
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_VARNISH_CACHE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.phoenix-media.eu/license/license_varnish_cache.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@phoenix-media.eu so we can send you a copy immediately.
 *
 * @category   Phoenix
 * @package    Phoenix_VarnishCache
 * @copyright  Copyright (c) 2011 PHOENIX MEDIA GmbH & Co. KG (http://www.phoenix-media.de)
 * @license    http://www.phoenix-media.eu/license/license_varnish_cache.txt
 */

/**
 * System cache management additional block
 *
 * @category    Phoenix
 * @package     Phoenix_VarnishCache
 */
class Phoenix_VarnishCache_Block_Adminhtml_Cache_Additional extends Mage_Adminhtml_Block_Template
{
    /**
     * Get clean cache url
     *
     * @return string
     */
    public function getCleanVarnishCacheUrl()
    {
        return $this->getUrl('*/varnishCache/clean');
    }

    /**
     * Check if block can be displayed
     *
     * @return bool
     */
    public function canShowButton()
    {
        return Mage::helper('varnishcache')->isEnabled();
    }

    /**
     * Get store selection
     *
     * @return string
     */
    public function getStoreOptions()
    {
        $options = array(array('value' => '', 'label' => Mage::helper('varnishcache')->__('All stores')));

        $stores = Mage::getModel('adminhtml/system_config_source_store')->toOptionArray();

        return array_merge($options, $stores);
    }

    /**
     * Get content types
     */
    public function getContentTypeOptions()
    {
        $options = array(array('value' => '', 'label' => Mage::helper('varnishcache')->__('All content types')));
        foreach (Mage::getModel('varnishcache/control')->getContentTypes() as $value => $label) {
            $options[] = array('value' => $value, 'label' => $label);
        }
        return $options;
    }
}

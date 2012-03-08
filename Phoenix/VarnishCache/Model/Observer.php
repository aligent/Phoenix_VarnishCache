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

class Phoenix_VarnishCache_Model_Observer
{
    const SET_CACHE_HEADER_FLAG = 'VARNISH_CACHE_CONTROL_HEADERS_SET';

    /**
     * Retrieve session model
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    protected function _isCacheEnabled()
    {
        return Mage::helper('varnishcache')->isEnabled();
    }

    /**
     * Get Varnish control model
     *
     * @return Phoenix_VarnishCache_Model_Control
     */
    protected function _getCacheControl()
    {
        return Mage::getSingleton('varnishcache/control');
    }

    /**
     * Clean all Varnish cache items
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_VarnishCache_Model_Observer
     */
    public function cleanCache(Varien_Event_Observer $observer)
    {
        if ($this->_isCacheEnabled()) {
            $this->_getCacheControl()->clean(Mage::helper('varnishcache/cache')->getStoreDomainList());

            $this->_getSession()->addSuccess(
                Mage::helper('varnishcache')->__('The Varnish cache has been cleaned.')
            );
        }
        return $this;
    }

    /**
     * Clean media (CSS/JS) cache
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_VarnishCache_Model_Observer
     */
    public function cleanMediaCache(Varien_Event_Observer $observer)
    {
        if ($this->_isCacheEnabled()) {
            $this->_getCacheControl()->clean(
            	Mage::helper('varnishcache/cache')->getStoreDomainList(),
            	'^/media/(js|css|css_secure)/'
            );

            // also clean HTML files
            $this->_getCacheControl()->clean(
            	Mage::helper('varnishcache/cache')->getStoreDomainList(),
            	'.*',
            	Phoenix_VarnishCache_Model_Control::CONTENT_TYPE_HTML
            );

            $this->_getSession()->addSuccess(
                Mage::helper('varnishcache')->__('The JavaScript/CSS cache has been cleaned on the Varnish servers.')
            );
        }
        return $this;
    }

    /**
     * Clean catalog images cache
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_VarnishCache_Model_Observer
     */
    public function cleanCatalogImagesCache(Varien_Event_Observer $observer)
    {
        if ($this->_isCacheEnabled()) {
            $this->_getCacheControl()->clean(
            	Mage::helper('varnishcache/cache')->getStoreDomainList(),
            	'^/media/catalog/product/cache/',
            	Phoenix_VarnishCache_Model_Control::CONTENT_TYPE_IMAGE
            );

            // also clean HTML files
            $this->_getCacheControl()->clean(
            	Mage::helper('varnishcache/cache')->getStoreDomainList(),
            	'.*',
            	Phoenix_VarnishCache_Model_Control::CONTENT_TYPE_HTML
            );

            $this->_getSession()->addSuccess(
                Mage::helper('varnishcache')->__('The catalog image cache has been cleaned on the Varnish servers.')
            );
        }
        return $this;
    }

    /**
     * Set appropriate cache control headers
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_VarnishCache_Model_Observer
     */
    public function setCacheControlHeaders(Varien_Event_Observer $observer)
    {
        if ($this->_isCacheEnabled()) {
            if (!Mage::registry(self::SET_CACHE_HEADER_FLAG)) {
                Mage::helper('varnishcache/cache')->setCacheControlHeaders();
                Mage::register(self::SET_CACHE_HEADER_FLAG, true);
            }
        }
        return $this;
    }

    /**
     * If the page has been cached by the FPC and a NO_CACHE cookie has
     * been set, the cached Cache-Control header might allow caching of the
     * page while the NO_CACHE cookie which should prevent it.
     * To sanitize this conflict we will force a TTL=0 before sending out
     * the page.
     */
    public function sanitizeCacheControlHeader()
    {
        Mage::helper('varnishcache/cache')->sanitizeCacheControlHeader();
    }

    /**
     * Disable page caching by setting no-cache header
     *
     * @param Varien_Event_Observer $observer | null
     * @return Mage_PageCache_Model_Observer
     */
    public function disablePageCaching($observer = null)
    {
        if ($this->_isCacheEnabled() || Mage::app()->getStore()->isAdmin()) {
            Mage::helper('varnishcache/cache')->setNoCacheHeader();
        }
        return $this;
    }

    /**
     * Disable page caching for visitor by setting no-cache cookie
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_PageCache_Model_Observer
     */
    public function disablePageCachingPermanent($observer = null)
    {
        if ($this->_isCacheEnabled()) {
            Mage::helper('varnishcache/cache')->setNoCacheCookie();
        }
        return $this;
    }

    /**
     * Purge category
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_VarnishCache_Model_Observer
     */
    public function purgeCatalogCategory(Varien_Event_Observer $observer)
    {
        try {
            $category = $observer->getEvent()->getCategory();
            if (!Mage::registry('varnishcache_catalog_category_purged_' . $category->getId())) {
                Mage::getModel('varnishcache/control_catalog_category')->purge($category);
                Mage::register('varnishcache_catalog_category_purged_' . $category->getId(), true);
            }
        } catch (Exception $e) {
            Mage::helper('varnishcache')->debug('Error on save category purging: '.$e->getMessage());
        }
        return $this;
    }

    /**
     * Purge product
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_VarnishCache_Model_Observer
     */
    public function purgeCatalogProduct(Varien_Event_Observer $observer)
    {
        try {
            $product = $observer->getEvent()->getProduct();
            if (!Mage::registry('varnishcache_catalog_product_purged_' . $product->getId())) {
                Mage::getModel('varnishcache/control_catalog_product')->purge($product, true, true);
                Mage::register('varnishcache_catalog_product_purged_' . $product->getId(), true);
            }
        } catch (Exception $e) {
            Mage::helper('varnishcache')->debug('Error on save product purging: '.$e->getMessage());
        }
        return $this;
    }

    /**
     * Purge Cms Page
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_VarnishCache_Model_Observer
     */
    public function purgeCmsPage(Varien_Event_Observer $observer)
    {
        try {
            $page = $observer->getEvent()->getObject();
            if (!Mage::registry('varnishcache_cms_page_purged_' . $page->getId())) {
                Mage::getModel('varnishcache/control_cms_page')->purge($page);
                Mage::register('varnishcache_cms_page_purged_' . $page->getId(), true);
            }
        } catch (Exception $e) {
            Mage::helper('varnishcache')->debug('Error on save cms page purging: '.$e->getMessage());
        }
        return $this;
    }

    /**
     * Purge product
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_VarnishCache_Model_Observer
     */
    public function purgeCatalogProductByStock(Varien_Event_Observer $observer)
    {
        try {
            $item = $observer->getEvent()->getItem();
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if (!Mage::registry('varnishcache_catalog_product_purged_' . $product->getId())) {
                Mage::getModel('varnishcache/control_catalog_product')->purge($product, true, true);
                Mage::register('varnishcache_catalog_product_purged_' . $product->getId(), true);
            }
        } catch (Exception $e) {
            Mage::helper('varnishcache')->debug('Error on save product purging: '.$e->getMessage());
        }
        return $this;
    }
}

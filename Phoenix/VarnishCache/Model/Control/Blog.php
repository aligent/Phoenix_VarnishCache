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

class Phoenix_VarnishCache_Model_Control_Blog
    extends Phoenix_VarnishCache_Model_Control_Abstract
{
    const XML_PATH_BLOG_HOME_PAGE = 'blog/blog/route';

    protected $_helperName = 'varnishcache/control_blog';

    /**
     * Purge Cms Page
     *
     * @param Mage_Cms_Model_Page $page
     * @return Phoenix_VarnishCache_Model_Control_Cms_Page
     */
    public function purge(AW_Blog_Model_Post $oPost)
    {
        if ($this->_canPurge()) {

            $aStoreIds = Mage::getResourceModel('varnishcache/blog_store_collection')
                ->addPageFilter($oPost->getId())
                ->getAllIds();

            if (count($aStoreIds) && current($aStoreIds) == 0) {
                $aStoreIds = Mage::getResourceModel('core/store_collection')
                    ->setWithoutDefaultFilter()
                    ->getAllIds();
            }

            foreach ($aStoreIds as $iStoreId) {
                $vBlogRootUrl = Mage::getStoreConfig(self::XML_PATH_BLOG_HOME_PAGE, $iStoreId);
                if ($vBlogRootUrl == '') {
                    $vBlogRootUrl = 'blog';
                }
                
                // Clean post url
                $vPostUrl = Mage::app()->getStore($iStoreId)->getUrl(null, array('_direct' => $vBlogRootUrl.'/'.$oPost->getIdentifier()));
                $aBits = parse_url($vPostUrl);
                $vHost = $aBits['host'];
                $vPath = rtrim($aBits['path'], '/');
                $this->_getCacheControl()->clean($vHost, '^' . $vPath . '/{0,1}$');
                
                // Clean blog home page
                $vPostUrl = Mage::app()->getStore($iStoreId)->getUrl(null, array('_direct' => $vBlogRootUrl));
                $aBits = parse_url($vPostUrl);
                $vHost = $aBits['host'];
                $vPath = rtrim($aBits['path'], '/');
                $this->_getCacheControl()->clean($vHost, '^' . $vPath . '/{0,1}$');
            }

            $this->_getSession()->addSuccess(
            	Mage::helper('varnishcache')->__('Varnish cache for "%s" has been purged.', $oPost->getTitle())
            );

        }
        return $this;
    }

}

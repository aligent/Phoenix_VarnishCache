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

class Phoenix_VarnishCache_Adminhtml_VarnishCacheController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    public function cleanAction()
    {
        try {
            if (Mage::helper('varnishcache')->isEnabled()) {
                // get domains for purging
                $domains = Mage::helper('varnishcache/cache')
                    ->getStoreDomainList($this->getRequest()->getParam('stores', 0));

                // clean Varnish cache
                Mage::getModel('varnishcache/control')
                    ->clean($domains, '.*', $this->getRequest()->getParam('content_types', '.*'));

                $this->_getSession()->addSuccess(
                    Mage::helper('varnishcache')->__('The Varnish cache has been cleaned.')
                );
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('varnishcache')->__('An error occurred while clearing the Varnish cache.')
            );
        }
        $this->_redirect('*/cache/index');
    }
}

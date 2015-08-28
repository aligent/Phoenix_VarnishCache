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
 * @copyright 2014 Aligent Consulting.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.aligent.com.au/
 */
class Phoenix_VarnishCache_Model_Resource_Mysql4_Enterprise_Catalog_Product extends Enterprise_Catalog_Model_Resource_Product {

    /**
     * Returns an array of rewrites that relate to a product ID
     *
     * @param $productId
     * @return array
     */
    public function getAllRewritesByProductId($productId) {

        $select = $this->_getReadAdapter()->select()->distinct()
            ->from(array('url_rewrite' => $this->getTable('enterprise_urlrewrite/url_rewrite')), array('request_path', 'is_system'))
            ->join(
                array('default_ur' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                "url_rewrite.target_path like concat(default_ur.target_path, '%')",
                array()
            )
            ->join(
                array('default_urc' => $this->getTable('enterprise_catalog/product')),
                "default_ur.url_rewrite_id = default_urc.url_rewrite_id",
                array()
            )
            ->where('default_urc.product_id = ?', $productId)
            ->where('default_urc.store_id = 0');

        return $this->_getReadAdapter()->fetchAll($select);
    }
}
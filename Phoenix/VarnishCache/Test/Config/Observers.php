<?php

class Phoenix_VarnishCache_Test_Config_Observers extends EcomDev_PHPUnit_Test_Case_Config {

    /**
     * Test front end event observers are registered.
     * 
     * @test
     */
    public function testFrontendEventObservers(){
        $this->assertModelAlias('varnishcache/observer', 'Phoenix_VarnishCache_Model_Observer');
        $this->assertEventObserverDefined('frontend','controller_action_postdispatch','varnishcache/observer','setCacheControlHeaders');
        $this->assertEventObserverDefined('frontend','http_response_send_before','varnishcache/observer','sanitizeCacheControlHeader');
        $this->assertEventObserverDefined('frontend','core_session_abstract_add_message','varnishcache/observer','checkDisableCachingOnAddMessage');
        $this->assertEventObserverDefined('frontend','checkout_cart_product_add_after','varnishcache/observer','checkDisableCachingOnAddToCart');
        $this->assertEventObserverDefined('frontend','customer_login','varnishcache/observer','checkDisableCachingOnLogin');
        $this->assertEventObserverDefined('frontend','catalog_product_compare_add_product','varnishcache/observer','checkDisableCachingOnAddToCompare');
        $this->assertEventObserverDefined('frontend','wishlist_add_product','varnishcache/observer','checkDisableCachingOnAddToWishlist');
    }
    
    /**
     * Test personalisation cookie event observers are registered.
     * 
     * @test
     */
    public function testPersonalisationCookieEventObservers(){
        $this->assertModelAlias('varnishcache/personalisationcookie', 'Phoenix_VarnishCache_Model_Personalisationcookie');
        $this->assertEventObserverDefined('frontend','customer_login','varnishcache/personalisationcookie','updatePersonalisationCookie');
        $this->assertEventObserverDefined('frontend','customer_logout','varnishcache/personalisationcookie','deletePersonalisationCookie');
        $this->assertEventObserverDefined('frontend','wishlist_add_product','varnishcache/personalisationcookie','updatePersonalisationCookie');
        $this->assertEventObserverDefined('frontend','wishlist_update_item','varnishcache/personalisationcookie','updatePersonalisationCookie');
        $this->assertEventObserverDefined('frontend','controller_action_postdispatch_checkout_cart_index','varnishcache/personalisationcookie','updatePersonalisationCookie');
        $this->assertEventObserverDefined('frontend','checkout_onepage_controller_success_action','varnishcache/personalisationcookie','updatePersonalisationCookie');
        $this->assertEventObserverDefined('frontend','checkout_cart_save_after','varnishcache/personalisationcookie','updatePersonalisationCookie');
        $this->assertEventObserverDefined('frontend','http_response_send_before','varnishcache/personalisationcookie','checkHasPersonalisationCookie');
    }
    
    /**
     * Test admin event observers are registered.
     * 
     * @test
     */
    public function testAdminHtmlEventObservers(){
        $this->assertModelAlias('varnishcache/observer', 'Phoenix_VarnishCache_Model_Observer');
        $this->assertEventObserverDefined('adminhtml','controller_action_predispatch_adminhtml','varnishcache/observer','disablePageCaching');
        $this->assertEventObserverDefined('adminhtml','controller_action_postdispatch_adminhtml_cache_flushAll','varnishcache/observer','cleanCache');
        $this->assertEventObserverDefined('adminhtml','controller_action_postdispatch_adminhtml_cache_flushSystem','varnishcache/observer','cleanCache');
        $this->assertEventObserverDefined('adminhtml','clean_media_cache_after','varnishcache/observer','cleanMediaCache');
        $this->assertEventObserverDefined('adminhtml','clean_catalog_images_cache_after','varnishcache/observer','cleanCatalogImagesCache');
        $this->assertEventObserverDefined('adminhtml','catalog_category_save_after','varnishcache/observer','purgeCatalogCategory');
        $this->assertEventObserverDefined('adminhtml','catalog_product_save_after','varnishcache/observer','purgeCatalogProduct');
        $this->assertEventObserverDefined('adminhtml','cms_page_save_after','varnishcache/observer','purgeCmsPage');
    }
    
    /**
     * Test global event observers are registered.
     * 
     * @test
     */
    public function testGlobalEventObservers(){
        $this->assertModelAlias('varnishcache/observer', 'Phoenix_VarnishCache_Model_Observer');
        $this->assertEventObserverDefined('global','cataloginventory_stock_item_save_after','varnishcache/observer','purgeCatalogProductByStock');
        $this->assertEventObserverDefined('global','blog_post_save_after','varnishcache/observer','purgeBlogByPost');
        $this->assertEventObserverDefined('global','blog_comment_save_after','varnishcache/observer','purgeBlogByComment');
    }
    
}
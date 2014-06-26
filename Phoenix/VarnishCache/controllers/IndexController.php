<?php

class Phoenix_VarnishCache_IndexController extends Mage_Core_Controller_Front_Action {

    public function personalisationcookieAction() {
        Mage::getSingleton('varnishcache/personalisationcookie')->updatePersonalisationCookie();
        $oResponse = $this->getResponse();
        $oResponse->setBody('{"success": true}');
        $oResponse->setHeader('Content-type', 'application/json');
    }

}

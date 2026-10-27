<?php

namespace WPCreativeIdea\QuickEnquiryUs\Handler;
use WPCreativeIdea\QuickEnquiryUs\Handler\FrontendHandler;

class ActionHandler
{
    public function register() {
        $this->activationAddons();
    }

    public function activationAddons() {
      
        $qeusActiveModule = get_option('qeus_active_module', []);

        if (isset($qeusActiveModule['qeus_module']) && $qeusActiveModule['qeus_module'] === 'yes') {
            $frontendHandler = new FrontendHandler();
            $frontendHandler->register();
        } 
    }
}

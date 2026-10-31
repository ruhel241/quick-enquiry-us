<?php

namespace Ruhel241\QuickProductEnquiry\Handler;
use Ruhel241\QuickProductEnquiry\Handler\FrontendHandler;

class ActionHandler
{
    public function register() {
        $this->activationAddons();
    }

    public function activationAddons() {
      
        $qpefwActiveModule = get_option('qpefw_active_module', []);

        if (isset($qpefwActiveModule['qpefw_module']) && $qpefwActiveModule['qpefw_module'] === 'yes') {
            $frontendHandler = new FrontendHandler();
            $frontendHandler->register();
        } 
    }
}

<?php

/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Wakka
 * @link http://code.zikula.org/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Wakka_Controller_Admin extends Zikula_AbstractController
{
    /**
     * Post initialise.
     *
     * Run after construction.
     *
     * @return void
     */
    
    protected function postInitialize()
    {
        // Disable caching by default.
        $this->view->setCaching(Zikula_View::CACHE_DISABLED);
    }
    
     
    public function main()
    {
        return $this->modifyconfig();
    }
    
    
    public function modifyconfig()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('modifyconfig.tpl', new Wakka_Handler_ModifyConfig());
    }
    
}
<?php
/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikka
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Access to (administrative) user-initiated actions.
 */
class Wikka_Controller_Admin extends Zikula_AbstractController
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
    
    /**
     * This function is a forward to the modifyconfig function. 
     *
     * @return redirect
     */  
    public function main()
    {
        return $this->modifyconfig();
    }
    
    /**
     * This functions returns the modifiy config hander.
     * 
     * @return string HTML string containing the rendered template.
     */  
    public function modifyconfig()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('modifyconfig.tpl', new Wikka_Handler_ModifyConfig());
    }
    
}
<?php

/**
 * Copyright Wikukla Team 2011
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Wikka
 * @link http://code.zikula.org/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */


class Wikka_Version extends Zikula_AbstractVersion
{
    public function getMetaData()
    {
        $meta = array();
        $meta['description']    = __('Wikka markup language editor');
        $meta['displayname']    = __('Wikka');
        //!url must be different to displayname
        $meta['url']            = __('wikka');
        $meta['version']        = '0.1.0';
        $meta['author']         = 'Fabian Wuertz';
        $meta['contact']        = 'fabian.wuertz.org';
        // recommended and required modules
        $meta['core_min'] = '1.3.0'; // requires minimum 1.3.0 or later
        $meta['dependencies'] = array();
        $meta['capabilities'] = array(HookUtil::PROVIDER_CAPABLE => array('enabled' => true));

        return $meta;
    }
    
    protected function setupHookBundles()
    {
        $bundle = new Zikula_HookManager_ProviderBundle($this->name, 'provider.wikka.ui_hooks.lml', 'ui_hooks', __('Wikka editor'));
        $bundle->addServiceHandler('display_view', 'Wikka_HookHandler_Lml', 'ui_view', 'wikka.lml');
        $this->registerHookProviderBundle($bundle);    

        
        $bundle = new Zikula_HookManager_ProviderBundle($this->name, 'provider.wikka.filter_hooks.lml', 'filter_hooks', __('Wikka transform'));
        $bundle->addStaticHandler('filter', 'Wikka_HookHandler_Lml', 'filter', 'wikka.lml');
        $this->registerHookProviderBundle($bundle);    
    }
}
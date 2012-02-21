<?php
/**
 * Copyright Wikukla Team 2011
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikka
 * @link http://code.zikula.org/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Provides metadata for this module to the Extensions module.
 */
class Wikka_Version extends Zikula_AbstractVersion
{
    
    /**
     * Assemble and return module metadata.
     *
     * @return array Module metadata.
     */
    public function getMetaData()
    {
        return array(
            'description'    => $this->__('Wikka markup language editor'),
            'displayname'    => $this->__('Wikka'),
            //!url must be different to displayname
            'url'            => 'wikka',
            'version'        => '1.0.0',
            'author'         => 'Fabian Wuertz',
            'contact'        => 'https://github.com/phaidon/Wikula',
            // recommended and required modules
            'core_min'       => '1.3.0', // requires minimum 1.3.0 or later
            'dependencies'   => array(),
            'capabilities'   => array(
                                    HookUtil::PROVIDER_CAPABLE => array('enabled' => true)
                                )
        );
    }
    
    
    /**
     * Define the hook bundles supported by this module.
     *
     * @return void
     */
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
<?php
/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 * @package Wikula
 * @license GNU/GPLv2 (or at your option, any later version).
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Provides metadata for this module to the Extensions module.
 * 
 * @package Wikula
 */
class Wikula_Version extends Zikula_AbstractVersion
{
    
    /**
     * Assemble and return module metadata.
     *
     * @return array Module metadata.
     */
    public function getMetaData()
    {
        $meta = array();
        $meta['name']           = 'Wikula';
        $meta['displayname']    = __('Wikula');
        $meta['oldnames']       = array('pnWikka', 'wikula');
        $meta['description']    = __('The Wikula module provides a wiki to your website.');
        $meta['url']            = __('wikula');
        $meta['version']        = '2.0.0';
        $meta['credits']        = 'docs/credits.txt';
        $meta['help']           = 'docs/install.txt';
        $meta['changelog']      = 'docs/changelog.txt';
        $meta['license']        = 'docs/license.txt';
        $meta['core_min']       = '1.3.0'; // requires minimum 1.3.0 or later

        $meta['official']       = false;

        $meta['author']         = 'Fabian Würtz, Frank Chestnut, Chris Hildebrandt, Florian Schießl, Mateo Tibaquirá, Gilles Pilloud,';
        $meta['contact']        = 'https://github.com/phaidon/Wikula';

        $meta['securityschema'] = array(
            'Wikula::' => '::',
            'Wikula::' => 'page::Page Tag'
        );
        $meta['capabilities']   = array(HookUtil::SUBSCRIBER_CAPABLE => array('enabled' => true));
        $meta['dependencies']   = array(
                                      array('modname'    => 'LuMicuLa', 
                                            'minversion' => '0.0.1', 
                                            'maxversion' => '', 
                                            'status'     => ModUtil::DEPENDENCY_RECOMMENDED
                                      ),
                                      array('modname'    => 'Wakka', 
                                            'minversion' => '0.0.1', 
                                            'maxversion' => '', 
                                            'status'     => ModUtil::DEPENDENCY_RECOMMENDED
                                      ),
                                  );

        return $meta;
    }
    
    /**
     * Define the hook bundles supported by this module.
     *
     * @return void
     */
    protected function setupHookBundles()
    {
        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.wikula.ui_hooks.bottom', 'ui_hooks', $this->__('Wikula bottom area'));
        $bundle->addEvent('display_view', 'wikula.ui_hooks.bottom.display_view');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.wikula.ui_hooks.editor', 'ui_hooks', $this->__('Wikula editor'));
        $bundle->addEvent('display_view', 'wikula.ui_hooks.editor.display_view');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.wikula.filter_hooks.body', 'filter_hooks', $this->__('Wikula Filters'));
        $bundle->addEvent('filter', 'wikula.filter_hooks.body.filter');
        $this->registerHookSubscriberBundle($bundle);
        

    }
}

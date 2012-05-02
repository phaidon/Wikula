<?php
/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Provides metadata for this module to the Extensions module.
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
        return array(
            'name'           => 'Wikula',
            'displayname'    => $this->__('Wikula'),
            'oldnames'       => array('pnWikka', 'wikula'),
            'description'    => $this->__('The Wikula module provides a wiki to your website.'),
            'url'            => $this->__('wikula'),
            'version'        => '2.0.0',
            'credits'        => 'docs/credits.txt',
            'help'           => 'docs/install.txt',
            'changelog'      => 'docs/changelog.txt',
            'license'        => 'docs/license.txt',
            'core_min'       => '1.3.0', // requires minimum 1.3.0 or later
            'official'       => false,
            'author'         => 'Fabian Würtz, Frank Chestnut, Chris Hildebrandt, Florian Schießl, Mateo Tibaquirá, Gilles Pilloud,',
            'contact'        => 'https://github.com/phaidon/Wikula',
            'securityschema' => array(
                                    'Wikula::' => '::',
                                    'Wikula::' => 'page::Page Tag'
                                ),
            'capabilities'   => array(
                                    HookUtil::SUBSCRIBER_CAPABLE => array('enabled' => true)
                                ),
            'dependencies'   => array(
                                    array(
                                        'modname'    => 'LuMicuLa', 
                                        'minversion' => '0.0.1', 
                                        'maxversion' => '', 
                                        'status'     => ModUtil::DEPENDENCY_RECOMMENDED
                                    ),
                                    array(
                                        'modname'    => 'Wikka', 
                                        'minversion' => '0.0.1', 
                                        'maxversion' => '', 
                                        'status'     => ModUtil::DEPENDENCY_RECOMMENDED
                                      ),
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
        $bundle = new Zikula_HookManager_SubscriberBundle(
            $this->name,
            'subscriber.wikula.ui_hooks.bottom',
            'ui_hooks', $this->__('Wikula bottom area')
            );
        $bundle->addEvent('display_view', 'wikula.ui_hooks.bottom.display_view');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new Zikula_HookManager_SubscriberBundle(
            $this->name,
            'subscriber.wikula.ui_hooks.editor',
            'ui_hooks', $this->__('Wikula editor')
        );
        $bundle->addEvent('display_view', 'wikula.ui_hooks.editor.display_view');
        $this->registerHookSubscriberBundle($bundle);

        $bundle = new Zikula_HookManager_SubscriberBundle(
            $this->name, 'subscriber.wikula.filter_hooks.body',
            'filter_hooks', $this->__('Wikula Filters')
        );
        $bundle->addEvent('filter', 'wikula.filter_hooks.body.filter');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new Zikula_HookManager_SubscriberBundle(
            $this->name,
            'subscriber.wikula.ui_hooks.discuss',
            'ui_hooks', $this->__('Wikula discussion area')
            );
        $bundle->addEvent('display_view', 'wikula.ui_hooks.discuss.display_view');
        $this->registerHookSubscriberBundle($bundle);
    }
}

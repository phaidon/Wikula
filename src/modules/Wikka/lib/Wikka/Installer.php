<?php
/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Wikka
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Provides module installation and upgrade services for the Wikula module.
 * 
 */
class Wikka_Installer extends Zikula_AbstractInstaller
{
    
    /**
     * Initialise the Wikula module.
     *
     * This function is only ever called once during the lifetime of a particular
     * module instance.
     * 
     * @return boolean True on success, false otherwise.
     */
    public function install()
    {        
        // create hook
        HookUtil::registerProviderBundles($this->version->getHookProviderBundles());

        // Initialisation successful
        return true;
    }
  

    /**
     * Upgrade the users module from an older version.
     *
     * This function must consider all the released versions of the module!
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param string $oldversion Version number string to upgrade from.
     *
     * @return mixed True on success, last valid version string or false if fails.
     */
    public function upgrade($oldversion)
    {
        // Update successful
        return true;
    }

    /**
     * Delete the users module.
     *
     * Since the users module should never be deleted we'all always return false here.
     *
     * @return bool false
     */
    public function uninstall()
    {
        HookUtil::unregisterProviderBundles($this->version->getHookProviderBundles());
        return true;

    }
}


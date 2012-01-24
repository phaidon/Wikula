<?php

/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Provides module installation and upgrade services for the Wikula module.
 * 
 * @package Wikula
 */
class Wikula_Installer extends Zikula_AbstractInstaller
{

    /**
     * This function loads the common file.
     */
    function __autoload($class_name) {
        unset($class_name);
        require_once 'modules/Wikula/lib/Wikula/Common.php';
    }

    /**
     * Initialise the Wikula module.
     *
     * @return bool True on success, false otherwise.
     */
    public function install()
    {        
        // create table
        try {
            DoctrineHelper::createSchema($this->entityManager, array(
                'Wikula_Entity_Pages',
                'Wikula_Entity_Links',
                'Wikula_Entity_Categories',
                'Wikula_Entity_Subscriptions'
            ));
        } catch (Exception $e) {
            LogUtil::registerStatus($e->getMessage());
            return false;
        }

        
        $this->defaultdata();

        HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());
        $hookManager = ServiceUtil::getService('zikula.hookmanager'); 
        if (ModUtil::available('Wikka')) {
            $hookManager->bindSubscriber(  'subscriber.wikula.filter_hooks.body', 'provider.wikka.filter_hooks.lml');
            $hookManager->bindSubscriber(  'subscriber.wikula.ui_hooks.editor', 'provider.wikka.ui_hooks.lml');
        } else if (ModUtil::available('LuMicuLa')) {
            $hookManager->bindSubscriber(   'subscriber.wikula.filter_hooks.body', 'provider.lumicula.filter_hooks.lml');
            $hookManager->bindSubscriber(   'subscriber.wikula.ui_hooks.editor', 'provider.lumicula.ui_hooks.lml');
        }


        return true;
    }

    /**
     * Upgrade the users module from an older version.
     *
     * This function must consider all the released versions of the module!
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param string $oldVersion Version number string to upgrade from.
     *
     * @return mixed True on success, last valid version string or false if fails.
     */
    public function upgrade($oldversion)
    {
        switch($oldversion) {
            // version pnWikka 1.0 for PostNuke .7x
            // to Wikula 1.1 for Zikula 1.x
            case '1.0':
            case '1.1':
                // rename the tables
                $tables = DBUtil::metaTables();
                if (in_array('pnwikka_pages', $tables) && !DBUtil::renameTable('pnwikka_pages', 'wikula_pages')) {
                    return false;
                }
                if (in_array('pnwikka_links', $tables) && !DBUtil::renameTable('pnwikka_links', 'wikula_links')) {
                    return false;
                }
                if (in_array('pnwikka_referrers', $tables) && !DBUtil::renameTable('pnwikka_referrers', 'wikula_referrers')) {
                    return false;
                }

                // drop table prefix
                $prefix = $this->serviceManager['prefix'];
                $connection = Doctrine_Manager::getInstance()->getConnection('default');
                $sqlStatements = array();
                $sqlStatements[] = 'RENAME TABLE ' . $prefix . '_wikula_pages' . " TO wikula_pages";
                $sqlStatements[] = 'RENAME TABLE ' . $prefix . '_wikula_links' . " TO wikula_links";
                $sqlStatements[] = 'RENAME TABLE ' . $prefix . '_wikula_referrers' . " TO wikula_referrers";
                $sqlStatements[] = "UPDATE `wikula_pages` SET `tag` = '".__('WikiHelp')."' WHERE `tag` = 'WikkaDocumentation'";
                $sqlStatements[] = "UPDATE `wikula_pages` SET `tag` = '".__('ReleaseNotes')."' WHERE `tag` = 'WikkaReleaseNotes'";

                foreach ($sqlStatements as $sql) {
                    $stmt = $connection->prepare($sql);
                    try {
                        $stmt->execute();
                    } catch (Exception $e) {
                    }
                }

                // migrate module vars
                $pnwikkavars = ModUtil::getVar('pnWikka');
                $wikulavars  = ModUtil::getVar('Wikula');
                foreach ($pnwikkavars as $name => $value) {
                    if (!isset($wikulavars[$name])) {
                        $wikulavars[$name] = $value;
                    }
                }

                // add the new ones
                if (!isset($wikulavars['hidehistory']) || (isset($wikulavars['hidehistory']) && !$wikulavars['hidehistory'])) {
                    $wikulavars['hidehistory'] = 20;
                }
                
                $wikulavars['langinstall'] = ZLanguage::getLanguageCode();
                $wikulavars['double_doublequote_html'] = 'safe';
                $wikulavars['geshi_tab_width'] = 4;
                $wikulavars['geshi_header'] = '';
                $wikulavars['geshi_line_numbers'] = '1';
                $wikulavars['grabcode_button'] = true;

                // save them and delete the old pnWikka variables
                $this->setVars($wikulavars);
                ModUtil::delVar('pnWikka');

            case '1.2':
                try {
                    DoctrineHelper::createSchema($this->entityManager, array(
                        'Wikula_Entity_Categories',
                        'Wikula_Entity_Subscriptions'
                    ));
                } catch (Exception $e) {
                    return false;
                }
                $this->delVar('hideeditbar');
                $this->delVar('excludefromhistory');
                $this->delVar('geshi_tab_width');
                $this->delVar('geshi_header');
                $this->delVar('geshi_line_numbers');
                $this->delVar('grabcode_button');
                $this->setVar('subscription', false);
                $this->setVar('mandatorycomment', false);
                $this->setVar('single_page_permissions', false);
                $root_page = $this->getVar('root_page', '');
                $root_page = str_replace(' ', '_', $root_page);
                $this->setVar('root_page', $root_page);
                
                $em = $this->getService('doctrine.entitymanager');
                $pages = $em->getRepository('Wikula_Entity_Pages')->findAll();
                foreach($pages as $page) {
                    $tag = $page->getTag();
                    $newtag = str_replace(' ', '_', $tag);
                    if($tag != $newtag) {                        
                        $page->setTag($newtag);
                        $em->persist($page);
                        $em->flush();
                    }
                    
                }
                
                DBUtil::dropTable('wikula_referrers');
                
                HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());
                $this->defaultdata();

                HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());
                $hookManager = ServiceUtil::getService('zikula.hookmanager'); 
                if (ModUtil::available('Wikka')) {
                    $hookManager->bindSubscriber(  'subscriber.wikula.filter_hooks.body', 'provider.wikka.filter_hooks.lml');
                    $hookManager->bindSubscriber(  'subscriber.wikula.ui_hooks.editor', 'provider.wikka.ui_hooks.lml');
                } else if (ModUtil::available('LuMicuLa')) {
                    $hookManager->bindSubscriber(   'subscriber.wikula.filter_hooks.body', 'provider.lumicula.filter_hooks.lml');
                    $hookManager->bindSubscriber(   'subscriber.wikula.ui_hooks.editor', 'provider.lumicula.ui_hooks.lml');
                }

        }

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
        // drop tables
        DoctrineHelper::dropSchema($this->entityManager, array(
            'Wikula_Entity_Pages',
            'Wikula_Entity_Links',
            'Wikula_Entity_Categories',
            'Wikula_Entity_Subscriptions'
        ));
        HookUtil::unregisterSubscriberBundles($this->version->getHookSubscriberBundles());
        
        // Delete the module vars
        $this->delVars();

        return true;
    }

    
    /**
     * Create the default data for the users module.
     *
     * This function is only ever called once during the lifetime of a particular
     * module instance.
     *
     * @return void
     */
    public function defaultdata()
    {
        /// default Settings
        $defaultsettings = Wikula_Util::getDefaultVars();
        $this->setVars($defaultsettings);
        
        
        $root_page = $this->__('HomePage');

        // Defines each record and save it in the DB
        $uname = DataUtil::formatForStore(UserUtil::getVar('uname'));
        // Insert the default pages
        $body = $this->__("Welcome to your Wiki!");
        $record = array(
            'tag'    => DataUtil::formatForStore($root_page),
            'body'   => $body,
            'owner'  => $uname,
            'user'   => $uname,
            'time'   => DateUtil::getDatetime(),
            'latest' => 'Y',
            'note'   => $this->__('Initial Insert')
        );

        $page = new Wikula_Entity_Pages();
        $page->merge($record);
        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return true;
    }

}

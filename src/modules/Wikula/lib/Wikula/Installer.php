<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       https://github.com/phaidon/Wikula/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */


class Wikula_Installer extends Zikula_AbstractInstaller
{

    function __autoload($class_name) {
        require_once 'modules/Wikula/lib/Wikula/Common.php';
    }

    /**
     * Wikula install function
     */
    public function install()
    {        
        // create table
        try {
            DoctrineHelper::createSchema($this->entityManager, array(
                'Wikula_Entity_Pages',
                'Wikula_Entity_Links',
                'Wikula_Entity_Subscriptions'
            ));
        } catch (Exception $e) {
            LogUtil::registerStatus($e->getMessage());
            return false;
        }

        
        $this->defaultdata();

        HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());


        return true;
    }

    /**
     * Wikula upgrade function
     */
    public function upgrade($oldversion)
    {
        switch($oldversion) {
            // version pnWikka 1.0 for PostNuke .7x
            // to Wikula 1.1 for Zikula 1.x
            case '1.0':
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

                // change the WikiHelp and ReleaseNotes public pages
                $tables = pnDBGetTables();
                $column = $tables['wikula_pages_column'];
                $sqls[] = "UPDATE $tables[wikula_pages] SET $column[tag] = '".__('WikiHelp')."' WHERE $column[tag] = 'WikkaDocumentation'";
                $sqls[] = "UPDATE $tables[wikula_pages] SET $column[tag] = '".__('ReleaseNotes')."' WHERE $column[tag] = 'WikkaReleaseNotes'";
                foreach ($sqls as $sql) {
                    if (!DBUtil::executeSQL($sql)) {
                        return LogUtil::registerError (__('Error! Table update failed.'));
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
                return wikula_upgrade('1.1');

            case '1.2':
                try {
                    DoctrineHelper::createSchema($this->entityManager, array(
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
                HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());
                
        }

        return true;
    }

    /**
     * Wikula uninstall function
     */
    public function uninstall()
    {
        // drop tables
        DoctrineHelper::dropSchema($this->entityManager, array(
            'Wikula_Entity_Pages',
            'Wikula_Entity_Links',
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
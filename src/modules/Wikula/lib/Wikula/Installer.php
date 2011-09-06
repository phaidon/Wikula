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
     * wikula install function
     */
    public function install()
    {        
        // create table
        try {
            DoctrineUtil::createTablesFromModels($this->name);
        } catch (Exception $e) {
            return false;
        }

        $this->defaultdata();

        HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());


        return true;
    }

    /**
     * wikula upgrade function
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
                $wikulavars  = ModUtil::getVar('wikula');
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

            case '1.1':
                try {
                    DoctrineUtil::createTablesFromModels($this->name);
                } catch (Exception $e) {
                    return false;
                }
        }

        return true;
    }

    /**
     * wikula uninstall function
     */
    public function uninstall()
    {
        DoctrineUtil::dropTable('wikula_pages');
        DoctrineUtil::dropTable('wikula_links');
        DoctrineUtil::dropTable('wikula_referrers');
        DoctrineUtil::dropTable('wikula_subscriptions');

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
        
        $dom = ZLanguage::getModuleDomain($this->name);
        
        $root_page = __('HomePage', $dom);

        // Defines each record and save it in the DB
        $uname = DataUtil::formatForStore(UserUtil::getVar('uname'));
        // Insert the default pages
        $renderer = Zikula_View::getInstance('Wikula', false);
        $body = self::HomePage();
        $record = array(
            'tag'    => DataUtil::formatForStore($root_page),
            'body'   => $body,
            'owner'  => $uname,
            'user'   => $uname,
            'time'   => DateUtil::getDatetime(),
            'latest' => 'Y',
            'note'   => __('Initial Insert', $dom)
        );

        $page = new Wikula_Model_Pages();
        $page->merge($record);
        $page->save();

        

        // Defines the tags to insert
        $tags = array(
            __('WikiCategory', $dom)       => self::WikiCategory(),
            __('CategoryWiki', $dom)       => self::CategoryWiki(),
            __('SandBox', $dom)            => self::SandBox()
        );

        // Following records are public and tag and body relies on language defines
        $record['owner'] = '(Public)';
        foreach ($tags as $name => $tag) {
            $record['tag']  = $name;            
            $nofooter[] =  __('CategoryWiki', $dom);
            if(!in_array($name, $nofooter)) {
                $tag .= "\n\n----\n[[CategoryWiki Wiki category]]";
            }
            $record['body'] = $tag;
            $page = new Wikula_Model_Pages();
            $page->merge($record);
            $page->save();
        }

        return true;
    }
    
    public static function HomePage()
    {
        $dom = ZLanguage::getModuleDomain('Wikula');
        $page = __("Welcome to your Wiki!", $dom);
        /*__("=====Welcome to your Wiki!=====
Thanks for install **[[https://github.com/phaidon/Wikula/ Wikula]]**! The Wiki module for Zikula based on [[http://wikkawiki.org WikkaWiki]].
This site is running on version {{wikkaversion}}.

>>==Contribute==
You can report bugs or file feature requests
on the [[https://github.com/phaidon/Wikula Wikula development website]]!
>>====Getting started====
If you are not sure how a wiki works, you can check out the [[WikiHelp Help page]] to get started or click or the &quot;edit page&quot; link at the bottom.

====Some useful pages====
~-[[FormattingRules Formatting guide]]
~-[[WikiHelp Help page]]
~-[[RecentChanges Recently modified pages]]

You will find more useful pages in the [[CategoryWiki Wiki category]] or in the [[PageIndex Page index]].

Enjoy!", $dom);*/
        return $page;

    }
    
    public static function SandBox()
    {
        $dom = ZLanguage::getModuleDomain('Wikula');
        $page = __("Test your formatting skills here.", $dom). "\n\n\n\n\n\n";
        return $page;
    }

    
    public static function CategoryWiki()
    {
        $dom = ZLanguage::getModuleDomain('Wikula');
        $page = __('===Wiki Related Category===
This Category will contain links to pages talking about Wikis and Wikis specific topics. When creating such pages, be sure to include CategoryWiki at the bottom of each page, so that page shows listed.
----
{{Category col="3" full="1" notitle="1"}}
----
[[CategoryCategory List of all categories]]', $dom);
        return $page;

    }
    

    
    public static function WikiCategory()
    {
        $dom = ZLanguage::getModuleDomain('Wikula');
        $page = __('===Wiki Related Category===
This Category will contain links to pages talking about Wikis and Wikis specific topics. When creating such pages, be sure to include CategoryWiki at the bottom of each page, so that page shows listed.
----
{{Category col="3" full="1" notitle="1"}}
----
[[CategoryCategory List of all categories]]', $dom);
        return $page;

    }

}
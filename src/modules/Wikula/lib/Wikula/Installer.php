<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: pninit.php 173 2010-05-06 08:31:17Z slam $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */


class Wikula_Installer extends Zikula_Installer
{

    function __autoload() {
        // Preload common stuff
        Loader::requireOnce('modules/Wikula/lib/wikula/Common.php');
    }


    /**
     * wikula install function
     */
    public function install()
    {
        // Main table
        if (!DBUtil::createTable('wikula_pages')) {
            return LogUtil::registerError(__('Error! Table creation failed.').' (wikula_pages)');
        }

        // Create the pages indexes
        //$dbtype      =  pnConfigGetVar('dbtype');
        //$dbtabletype =  pnConfigGetVar('dbtabletype');
        //if (strtolower($dbtype) == 'mysql' && strtolower($dbtabletype) == 'myisam') {
        $idxoptarray = array('FULLTEXT' => 'FULLTEXT');
        //}
        if (!DBUtil::createIndex('tag', 'wikula_pages', 'tag', $idxoptarray)) {
            return LogUtil::registerError(__('Error! Index creation failed.').' - tag (wikula_pages)');
        }
        //TODO - fix this error :: blob used in key specification without a key length
        //if (!DBUtil::createIndex('body', 'wikula_pages', 'body', $idxoptarray)) {
        //    return LogUtil::registerError(__('Error! Index creation failed.').' - body (wikula_pages)');
        //}


        // Links table
        if (!DBUtil::createTable('wikula_links')) {
            return LogUtil::registerError(__('Error! Table creation failed.').' (wikula_links)');
        }

        // Create the links unique index
        $idxoptarray = array('UNIQUE' => true);
        if (!DBUtil::createIndex('idx_from,idx_to', 'wikula_links', array('from_tag','to_tag'), $idxoptarray)) {
            return LogUtil::registerError(__('Error! Index creation failed.').' - idx_from,idx_to (wikula_links)');
        }


        // Referrers table
        if (!DBUtil::createTable('wikula_referrers')) {
            return LogUtil::registerError(__('Error! Table creation failed.').' (wikula_referrers)');
        }

        // Create the referrers unique index
        $idxoptarray = array('UNIQUE' => true);
        if (!DBUtil::createIndex('idx_page_tag,idx_time', 'wikula_referrers', array('page_tag','time'), $idxoptarray)) {
            return LogUtil::registerError(__('Error! Index creation failed.').' - idx_page_tag,idx_time (wikula_referrers)');
        }

        // End of table creation

        // Wikka import
        $wikulainit = SessionUtil::getVar('wikulainit');

        // Check if we import...
        if ($wikulainit['importwikka']) {
            // If we do import
            if (!$this->ImportData()) {
                return LogUtil::registerError(__('PROBLEM IMPORTING DATA'));
            }
        // Filling with default data...
        } elseif (!$this->DefaultData($wikulainit['root_page'])) {
            return LogUtil::registerError(__('PROBLEM INSERT DEFAULT DATA'));
        }

        // Module config vars
        $modvars = array(
            'root_page'               => $wikulainit['root_page'],
            'savewarning'             => (bool)$wikulainit['savewarning'],
            'logreferers'             => (bool)$wikulainit['logreferers'],
            'excludefromhistory'      => $wikulainit['root_page'],
            'modulestylesheet'        => 'style.css',
            'hideeditbar'             => false,
            'hidehistory'             => 20,
            'itemsperpage'            => 25,
            'langinstall'             => pnUserGetLang(),
            'double_doublequote_html' => 'safe',
            'geshi_tab_width'         => 4,
            'geshi_header'            => '',
            'geshi_line_numbers'      => '1',
            'grabcode_button'         => true
        );
        $this->setVars($modvars);


        // EZComments hook
        /*if (!empty($wikulainit['ezc'])) {
            pnQueryStringSetVar('hooks_EZComments', 'ON');
            pnModAPIFunc('Modules', 'admin', 'updatehooks',
                         array('id' => pnModGetIDFromName('wikula')));
        }*/

        // Delete the interactive install data
        SessionUtil::delVar('wikulainit');
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
                $pnwikkavars = pnModGetVar('pnWikka');
                $wikulavars  = pnModGetVar('wikula');
                foreach ($pnwikkavars as $name => $value) {
                    if (!isset($wikulavars[$name])) {
                        $wikulavars[$name] = $value;
                    }
                }

                // add the new ones
                if (!isset($wikulavars['hidehistory']) || (isset($wikulavars['hidehistory']) && !$wikulavars['hidehistory'])) {
                    $wikulavars['hidehistory'] = 20;
                }
                $wikulavars['langinstall'] = pnUserGetLang();
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
                //return wikula_upgrade('1.2');
        }

        return true;
    }

    /**
     * wikula uninstall function
     */
    public function uninstall()
    {
        // Delete the module tables
        if (!DBUtil::dropTable('wikula_pages')) {
            return LogUtil::registerError(__('Error! Table deletion failed.').' (wikula_pages)');
        }

        if (!DBUtil::dropTable('wikula_links')) {
            return LogUtil::registerError(__('Error! Table deletion failed.').' (wikula_links)');
        }

        if (!DBUtil::dropTable('wikula_referrers')) {
            return LogUtil::registerError(__('Error! Table deletion failed.').' (wikula_referrers)');
        }

        // Delete the module vars
        $this->delVars();

        // Delete the Admin module register
        pnModDBInfoLoad('Admin');
        DBUtil::deleteObjectByID('admin_module', (int)pnModGetIdFromName('wikula'), 'mid');

        return true;
    }

    /**
     * Interactive installation functions
     */
    public function init_interactiveinit()
    {
        // Only continue if this module is uninstalled
        $modinfo = pnModGetInfo(pnModGetIDFromName('wikula'));
        if ($modinfo['state'] != PNMODULE_STATE_UNINITIALISED) {
            return LogUtil::registerError(__('Sorry! This module cannot be accessed directly.'), 403);
        }

        if (!SecurityUtil::checkPermission('::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerError(__('Sorry! No authorization to access this module.'), 403);
        }

        // Only continue if this module is uninstalled
        $modinfo = pnModGetInfo(pnModGetIDFromName('wikula'));
        if ($modinfo['state'] != PNMODULE_STATE_UNINITIALISED) {
            return LogUtil::registerError(__('Sorry! This module cannot be accessed directly.'), 403);
        }

        $submit     = FormUtil::getPassedValue('submit');
        $wikulainit = FormUtil::getPassedValue('wikulainit', SessionUtil::getVar('wikulainit', array()));

        // Validate the passed data if submitted
        $validationerror = false;
        if ($submit && empty($wikulainit['root_page'])) {
            $validationerror = __('Must fill the root Page');
        }

        $csrftoken = FormUtil::getPassedValue('csrftoken');
        // defaults
        $wikulainit['root_page']    = (!empty($wikulainit['root_page'])) ? $wikulainit['root_page'] : __('HomePage');
        $wikulainit['itemsperpage'] = (!empty($wikulainit['itemsperpage'])) ? $wikulainit['itemsperpage'] : 25;
        $wikulainit['savewarning']  = (isset($wikulainit['savewarning']) && $wikulainit['savewarning']) ? true : false;
        $wikulainit['logreferers']  = (isset($wikulainit['logreferers']) && $wikulainit['logreferers']) ? true : false;
        $wikulainit['ezc']          = (isset($wikulainit['ezc']) && $wikulainit['ezc']) ? true : false;
        $wikulainit['wikkaprefix']  = (isset($wikulainit['wikkaprefix'])) ? $wikulainit['wikkaprefix'] : 'wikka';
        $wikulainit['importwikka']  = (!empty($wikulainit['importwikka'])) ? $wikulainit['importwikka'] : 0;
        $wikulainit['activate']     = (isset($wikulainit['activate']) && $wikulainit['activate']) ? true : false;   

        if (!$submit || $validationerror !== false) {
            // check if there was a validation error
            if ($validationerror !== false) {
                LogUtil::registerError($validationerror);
            }

            // submit is not set, show the form now
            $render = pnRender::getInstance('wikula', false);

            $wikulainit['ezcavailable'] = false;
            if (pnModAvailable('EZComments')) {
                $wikulainit['ezcavailable'] = true;
            } else {
                $wikulainit['ezc'] = false;
            }

            $wikulainit['wikkawiki']   = false;

            $result = DBUtil::executeSQL("SHOW TABLES LIKE '{$wikulainit['wikkaprefix']}%'");
            echo $result->rowCount(); 
            if ($result->rowCount() > 0) {
                $wikulainit['wikkawiki'] = true;
                $wikulainit['importwikka'] = 1;
            }

            $admincats  = array();
            $admincat   = (isset($wikulainit['admincategory'])) ? $wikulainit['admincategory'] : pnModGetVar('Admin', 'defaultcategory');
            $categories = pnModAPIFunc('Admin', 'admin', 'getall');
            foreach ($categories as $cat) {
                $admincats[$cat['cid']] = $cat['catname'];
            }

            $render->assign('admincats',   $admincats);
            $render->assign('admincat',    $admincat);
            $render->assign('csrftoken', $csrftoken);        
            $render->assign($wikulainit);

            return $render->fetch('init/interactive.tpl');

        } else {
            // submit is set, read the data and store them.
            if (!SecurityUtil::confirmAuthKey()) {
                return LogUtil::registerError(__("Invalid 'authkey':  this probably means that you pressed the 'Back' button, or that the page 'authkey' expired. Please refresh the page and try again."), null, pnModURL('Modules', 'admin', 'view'));
            }

            SessionUtil::setVar('wikulainit', $wikulainit);
        }

        // we are ready now and redirect to the function that is responsible for installing a module
        return System::redirect(ModUtil::url('extensions', 'admin', 'initialise', array('csrftoken' => $csrftoken) ));

    }

    public function init_interactivedelete()
    {
        // Check permissions
        if (!SecurityUtil::checkPermission('::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerError(__('Sorry! No authorization to access this module.'), 403);
        }

        $render = pnRender::getInstance('wikula', false);
        return $render->fetch('init/delete.tpl');
    }


    /**
     * Default Data
     */
    public function DefaultData($root_page=null)
    {
        if (is_null($root_page)) {
            $root_page = __('HomePage');
        }

        // Defines each record and save it in the DB
        $uname = DataUtil::formatForStore(pnUserGetVar('uname'));

        // Insert the default pages
        $record = array(
            'tag'    => DataUtil::formatForStore($root_page),
            'body'   => __("=====Welcome to your Wiki!=====
    Thanks for install **[[http://code.zikula.org/wikula/ Wikula]]**! The Wiki module for Zikula based on [[http://wikkawiki.org WikkaWiki]].
    This site is running on version {{wikkaversion}} (see the [[ReleaseNotes release notes]] to learn what's new in this release).

    >>==Contribute==
    You can report bugs or file feature requests
    on the [[http://code.zikula.org/wikula Wikula development website]]!
    >>====Getting started====
    If you are not sure how a wiki works, you can check out the [[WikiHelp Help page]] to get started or click or the &quot;edit page&quot; link at the bottom.

    ====Some useful pages====
    ~-[[FormattingRules Formatting guide]]
    ~-[[WikiHelp Help page]]
    ~-[[RecentChanges Recently modified pages]]

    You will find more useful pages in the [[CategoryWiki Wiki category]] or in the [[PageIndex Page index]].

    Enjoy!"),
            'owner'  => $uname,
            'user'   => $uname,
            'time'   => DateUtil::getDatetime(),
            'latest' => 'Y',
            'note'   => __('Initial Insert')
        );

        if (!DBUtil::insertObject($record, 'wikula_pages')) {
            return LogUtil::registerError(__('Failed filling Database: ').' homepage');
        }

        // Following records are public and tag and body relies on language defines
        $record['owner'] = '(Public)';

        // Defines the tags to insert
        $tags = array(__('RecentChanges')=>__('{{RecentChanges}}
    ----
    [[CategoryWiki Wiki category]]'),
                      __('PageIndex')=>__('{{PageIndex}}

    ----
    [[CategoryWiki Wiki category]]'),
                      __('ReleaseNotes')=>__('{{WikkaChanges}}

    ----
    [[CategoryWiki Wiki category]]'),
                     __('WikiHelp') => __("=====Useful pages=====
      * FormattingRules: learn how to format the contents in this wiki
      * SandBox: play with your formatting skills

      * PageIndex: index of the available pages on the wiki
      * WantedPages: check out the pages pending for creation
      * OrphanedPages: list of orphaned pages
      * TextSearch: search something of your interest in the wiki
      * TextSearchExpanded: fine grained search if you haven't found anything in the normal search

      * WikiCategory: learn how works the categorization system of this wiki
      * CategoryWiki: list of pages related to the Wiki
      * InterWiki: check the allowed interwiki links

      * RecentChanges: check which pages that were changed recently
      * HighScores: check who had contributed more to the wiki
      * OwnedPages: check out how many pages you own on the wiki
      * MyChanges: list of changes that you have done
      * MyPages: list of pages that you own on this wiki

    You will find more useful pages in the [[WikiCategory Wiki category]] or in the [[PageIndex Page index]].

    =====Wikka Documentation=====
    Comprehensive and up-to-date documentation can be found on the [[http://docs.wikkawiki.org/WikkaDocumentation Wikka Documentation server]].

    ----
    [[CategoryWiki Wiki category]]"),
                      __('WantedPages')=>__('{{wantedpages}}

    ----
    [[CategoryWiki Wiki category]]'),
                      __('OrphanedPages')=>__('{{orphanedpages}}

    ----
    [[CategoryWiki Wiki category]]'),
                      __('TextSearch')=>__('{{textsearch}}

    ----
    [[CategoryWiki Wiki category]]'),
                      __('TextSearchExpanded')=>__('{{textsearchexpanded}}

    ----
    [[CategoryWiki Wiki category]]'),
                      __('MyPages')=>__('{{mypages}}

    ----
    [[CategoryWiki Wiki category]]'),
                  __('MyChanges')=>__('{{mychanges}}

    ----
    [[CategoryWiki Wiki category]]'),
                     __('InterWiki')=>__('{{interwikilist}}

    ----
    [[CategoryWiki Wiki category]]'),
                  __('WikiCategory')=>__('===Categorization system===
    This wiki is using a very flexible but simple categorizing system to keep everything properly organized

    {{Category page="CategoryCategory" col="3" full="1"}}
    ==Here\'s how it works:==
    ~- The master list of the categories is **""CategoryCategory""** which will automatically list all known main categories, and should never be edited. This list is easily accessed from the Wiki\'s top navigation bar. (Categories).
    ~- Pages can belong to zero or more categories. Including a page in a category is done by simply linking to the ""CategoryName"" on the page (by convention at the very end of the page). To mention a category on a page when the page does not belong to it, write its name in double double quote in order to unwikify it. To link to a category on a page when the page does not belong to it, link it using full URL like ""[[http://yoursite.com/CategoryXy CategoryXy]]"".
    ~- The system allows to build hierarchies of categories by referring to the parent category in the subcategory page. The parent category page will then automatically include the subcategory page in its list.
    ~- A special kind of category is **""CategoryUsers""** to group the userpages, so your Wiki homepage should include it at the end to be included in the category-driven userlist.
    ~- New categories can be created (think very hard before doing this though, you don\'t need too much of them) by creating a ""CategoryName"" page, including ""{{Category}}"" in it and placing it in the **""CategoryCategory""** category (for a main category or another parent category in case you want to create a subcategory).

    {{sidenote type="warning" title="Please" text="help to keep this place organized by including the relevant categories in new and existing pages!" width="100%" side="none"}}
    **Notes:**
    ~- The above bold items were coded using double doublequote in order to unwikify them to prevent this page from showing up in the mentioned categories. This page only belongs in CategoryWiki (which can be safely mentioned) after all !
    ~- In order to avoid accidental miscategorization you should unwikify all camelcased non-related ""CategoryName"" on a page. This is a side-effect of how the categorizing system works: it\'s based on a backlinking and is not restricted to the footer convention.
    ~- Don\'t be put of by the name of this page (WikiCategory) which is a logical name (it\'s about the Wiki and explains Category) but doesn\'t have any special role in the Categorizing system.
    ~- To end with this is the **standard convention** to include the categories (both the wiki code and the result):
    %%==Categories==
    CategoryWiki

    or
    ==Categories==
    [[CategoryWiki Wiki category]]%%

    ----
    ==Categories==
    [[CategoryWiki Wiki category]]'),
                      __('CategoryWiki')=>__('===Wiki Related Category===
    This Category will contain links to pages talking about Wikis and Wikis specific topics. When creating such pages, be sure to include CategoryWiki at the bottom of each page, so that page shows listed.
    ----
    {{Category col="3" full="1" notitle="1"}}
    ----
    [[CategoryCategory List of all categories]]'),
                      __('CategoryCategory')=>__('===List of All Categories===
    Below is the list of all Categories existing on this Wiki, granted that users did things right when they created their pages or new Categories. See WikiCategory for how the system works.
    ----
    {{Category compact="1" notitle="1"}}'),
                      __('FormattingRules')=>__('======Wikka Formatting Guide======
    <<**Note:** Anything between 2 sets of double-quotes is not formatted.
    <<::c::Once you have read through this, test your formatting skills in the SandBox.
    ----
    ===1. Text Formatting===

    <<
    ~##""**I\'m bold**""##
    ~**I\'m bold **
    <<::c::
    <<
    ~##""//I\'m italic text!//""##
    ~//I\'m italic text!//
    <<::c::
    <<
    ~##""And I\'m __underlined__!""##
    ~And I\'m __underlined__!
    <<::c::
    <<
    ~##""##monospace text##""##
    ~##monospace text##
    <<::c::
    <<
    ~##""\'\'highlight text\'\'""## (using 2 single-quotes)
    ~\'\'highlight text\'\'
    <<::c::
    <<
    ~##""++Strike through text++""##
    ~++Strike through text++
    <<::c::
    <<
    ~##""&pound;&pound;Text insertion&pound;&pound;""##
    ~ &pound;&pound;Text insertion&pound;&pound;
    <<::c::
    <<
    ~##""&yen;&yen;Text deletion&yen;&yen;""##
    ~ &yen;&yen;Text deletion&yen;&yen;
    <<::c::
    <<
    ~##""Press #%ANY KEY#%""##
    ~Press #%ANY KEY#%
    <<::c::
    <<
    ~##""@@Center text@@""##
    ~@@Center text@@
    <<::c::
    <<
    ~##""/* Elided content (eliminates trailing ws */""##
    ~Elides (hides) content from displaying.  Eliminates trailing whitespace so there are no unsightly gaps in output. Useful for commenting Wikka markup.
    <<::c::
    <<
    ~##""`` Elided content (preserves trailing ws ``""##
    ~Elides (hides) content from displaying.  Preserves trailing whitespace.
    <<::c::

    ===2. Headers===

    Use between six ##=## (for the biggest header) and two ##=## (for the smallest header) on both sides of a text to render it as a header.

    <<
    ~##""====== Really big header ======""##
    ~====== Really big header ======
    <<::c::
    <<
    ~##""===== Rather big header =====""##
    ~===== Rather big header =====
    <<::c::
    <<
    ~##""==== Medium header ====""##
    ~==== Medium header ====
    <<::c::
    <<
    ~##""=== Not-so-big header ===""##
    ~=== Not-so-big header ===
    <<::c::
    <<
    ~##""== Smallish header ==""##
    ~== Smallish header ==
    <<::c::

    ===3. Horizontal separator===

    ~##""----""##
    ----

    ===4. Forced line break===

    ~##""---""##
    ---

    ===5. Lists and indents===

    You can indent text using a **~**, a **tab** or **2 spaces**.

    <<##""~This text is indented<br />~~This text is double-indented<br />&nbsp;&nbsp;This text is also indented""##
    <<::c::
    <<
    ~This text is indented
    ~~This text is double-indented
    ~This text is also indented
    <<::c::

    To create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a ##**~**##):

    **Bulleted lists**
    <<##""~- Line one""##
    ##""~- Line two""##
    <<::c::
    <<
    ~- Line one
    ~- Line two
    <<::c::

    **Numbered lists**
    <<##""~1) Line one""##
    ##""~1) Line two""##
    <<::c::
    <<
    ~1) Line one
    ~1) Line two
    <<::c::

    **Ordered lists using uppercase characters**
    <<##""~A) Line one""##
    ##""~A) Line two""##
    <<::c::
    <<
    ~A) Line one
    ~A) Line two
    <<::c::

    **Ordered lists using lowercase characters**
    <<##""~a) Line one""##
    ##""~a) Line two""##
    <<::c::
    <<
    ~a) Line one
    ~a) Line two
    <<::c::

    **Ordered lists using roman numerals**
    <<##""~I) Line one""##
    ##""~I) Line two""##
    <<::c::
    <<
    ~I) Line one
    ~I) Line two
    <<::c::

    **Ordered lists using lowercase roman numerals**
    <<##""~i) Line one""##
    ##""~i) Line two""##
    <<::c::
    <<
    ~i) Line one
    ~i) Line two
    <<::c::

    ===6. Inline comments===

    To format some text as an inline comment, use an indent ( **~**, a **tab** or **2 spaces**) followed by a **""&amp;""**.

    <<
    ##""~&amp; Comment""##
    ##""~~&amp; Subcomment""##
    ##""~~~&amp; Subsubcomment""##
    <<::c::
    <<
    ~& Comment
    ~~& Subcomment
    ~~~& Subsubcomment
    <<::c::

    ===7. Images===

    To place images on a Wiki page, you can use the ##image## action.

    <<
    ~##""{{image class="center" alt="DVD logo" title="An Image Link" url="images/dvdvideo.gif" link="RecentChanges"}}""##
    ~{{image class="center" alt="dvd logo" title="An Image Link" url="modules/Wikula/pnimages/dvdvideo.gif" link="RecentChanges"}}
    <<::c::

    Links can be external, or internal Wiki links. You don\'t need to enter a link at all, and in that case just an image will be inserted. You can use the optional classes ##left## and ##right## to float images left and right. You don\'t need to use all those attributes, only ##url## is required while ##alt## is recommended for accessibility.

    ===8. Links===

    To create a **link to a wiki page** you can use any of the following options:
    ---
    ~1) type a ##""WikiName""##: --- --- ##""FormattingRules""## --- FormattingRules --- ---
    ~1) add a forced link surrounding the page name by ##""[[""## and ##""]]""## (everything after the first space will be shown as description): --- --- ##""[[SandBox Test your formatting skills]]""## --- [[SandBox Test your formatting skills]] --- --- ##""[[SandBox &#27801;&#31665;]]""## --- [[SandBox &#27801;&#31665;]] --- ---
    ~1) add an image with a link (see instructions above).

    To **link to external pages**, you can do any of the following:
    ---
    ~1) type a URL inside the page: --- --- ##""http://www.example.com""## --- http://www.example.com --- --- 
    ~1) add a forced link surrounding the URL by ##""[[""## and ##""]]""## (everything after the first space will be shown as description): --- --- ##""[[http://example.com/jenna/ Jenna\'s Home Page]]""## --- [[http://example.com/jenna/ Jenna\'s Home Page]] --- --- ##""[[mail@example.com Write me!]]""## --- [[mail@example.com Write me!]] --- ---
    ~1) add an image with a link (see instructions above); --- ---
    ~1) add an interwiki link (browse the [[InterWiki list of available interwiki tags]]): --- --- ##""WikiPedia:WikkaWiki""## --- WikiPedia:WikkaWiki --- --- ##""Google:CSS""## --- Google:CSS --- --- ##""Thesaurus:Happy""## --- Thesaurus:Happy --- ---

    ===9. Tables===

    To create a table, you can use the ##table## action.

    <<
    ~##""{{table columns="3" cellpadding="1" cells="BIG;GREEN;FROGS;yes;yes;no;no;no;###"}}""##
    ~{{table columns="3" cellpadding="1" cells="BIG;GREEN;FROGS;yes;yes;no;no;no;###"}}
    <<::c::Note that ##""###""## must be used to indicate an empty cell.
    Complex tables can also be created by embedding HTML code in a wiki page (see instructions below).

    ===10. Colored Text===

    Colored text can be created using the ##color## action:

    <<
    ~##""{{color c="blue" text="This is a test."}}""##
    ~{{color c="blue" text="This is a test."}}
    <<::c::You can also use hex values:

    <<
    ~##""{{color hex="#DD0000" text="This is another test."}}""##
    ~{{color hex="#DD0000" text="This is another test."}}
    <<::c::Alternatively, you can specify a foreground and background color using the ##fg## and ##bg## parameters (they accept both named and hex values):

    <<
    ~##""{{color fg="#FF0000" bg="#000000" text="This is colored text on colored background"}}""##
    ~{{color fg="#FF0000" bg="#000000" text="This is colored text on colored background"}}
    <<::c::
    <<
    ~##""{{color fg="yellow" bg="black" text="This is colored text on colored background"}}""##
    ~{{color fg="yellow" bg="black" text="This is colored text on colored background"}}
    <<::c::

    ===11. Floats===

    To create a **left floated box**, use two ##<## characters before and after the block.

    **Example:**

    ~##""&lt;&lt;Some text in a left-floated box hanging around&lt;&lt; Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.""##

    <<Some text in a left-floated box hanging around<<Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.

    ::c::To create a **right floated box**, use two ##>## characters before and after the block.

    **Example:**

    ~##"">>Some text in a right-floated box hanging around>> Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.""##

       >>Some text in a right-floated box hanging around>>Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.

    ::c:: Use ##""::c::""##  to clear floated blocks.

    ===12. Code formatters===

    You can easily embed code blocks in a wiki page using a simple markup. Anything within a code block is displayed literally. 
    To create a **generic code block** you can use the following markup:

    ~##""%% This is a code block %%""##. 

    %% This is a code block %%

    To create a **code block with syntax highlighting**, you need to specify a //code formatter// (see below for a list of available code formatters). 

    ~##""%%(""{{color c="red" text="php"}}"")<br />&lt;?php<br />echo "Hello, World!";<br />?&gt;<br />%%""##

    %%(php)
    <?php
    echo "Hello, World!";
    ?>
    %%

    You can also specify an optional //starting line// number.

    ~##""%%(php;""{{color c="red" text="15"}}"")<br />&lt;?php<br />echo "Hello, World!";<br />?&gt;<br />%%""##

    %%(php;15)
    <?php
    echo "Hello, World!";
    ?>
    %%

    If you specify a //filename//, this will be used for downloading the code.

    ~##""%%(php;15;""{{color c="red" text="test.php"}}"")<br />&lt;?php<br />echo "Hello, World!";<br />?&gt;<br />%%""##

    %%(php;15;test.php)
    <?php
    echo "Hello, World!";
    ?>
    %%

    |?|List of available code formatters||
    ||
    |=|Language|=|Formatter|=|Language|=|Formatter|=|Language|=|Formatter||
    |#|
    |=|Actionscript||##actionscript##|=|ABAP||##abap##|=|ADA||##ada##||
    |=|Apache Log||##apache##|=|""AppleScript""||##applescript##|=|ASM||##asm##||
    |=|ASP||##asp##|=|""AutoIT""||##autoit##|=|Bash||##bash##||
    |=|""BlitzBasic""||##blitzbasic##|=|""Basic4GL""||##basic4gl##|=|bnf||##bnf##||
    |=|C||##c##|=|C for Macs||##c_mac##|=|C#||##csharp##||
    |=|C""++""||##cpp##|=|C""++"" (+QT)||##cpp-qt##|=|CAD DCL||##caddcl##||
    |=|""CadLisp""||##cadlisp##|=|CFDG||##cfdg##|=|""ColdFusion""||##cfm##||
    |=|CSS||##css##|=|D||##d##|=|Delphi||##delphi##||
    |=|Diff-Output||##diff##|=|DIV||##div##|=|DOS||##dos##||
    |=|Dot||##dot##|=|Eiffel||##eiffel##|=|Fortran||##fortran##||
    |=|""FreeBasic""||##freebasic##|=|FOURJ\'s Genero 4GL||##genero##|=|GML||##gml##||
    |=|Groovy||##groovy##|=|Haskell||##haskell##|=|HTML||##html4strict##||
    |=|INI||##ini##|=|Inno Script||##inno##|=|Io||##io##||
    |=|Java 5||##java5##|=|Java||##java##|=|Javascript||##javascript##||
    |=|""LaTeX""||##latex##|=|Lisp||##lisp##|=|Lua||##lua##||
    |=|Matlab||##matlab##|=|mIRC Scripting||##mirc##|=|Microchip Assembler||##mpasm##||
    |=|Microsoft Registry||##reg##|=|Motorola 68k Assembler||##m68k##|=|""MySQL""||##mysql##||
    |=|NSIS||##nsis##|=|Objective C||##objc##|=|""OpenOffice"" BASIC||##oobas##||
    |=|Objective Caml||##ocaml##|=|Objective Caml (brief)||##ocaml-brief##|=|Oracle 8||##oracle8##||
    |=|Pascal||##pascal##|=|Per (FOURJ\'s Genero 4GL)||##per##|=|Perl||##perl##||
    |=|PHP||##php##|=|PHP (brief)||##php-brief##|=|PL/SQL||##plsql##||
    |=|Python||##phyton##|=|Q(uick)BASIC||##qbasic##|=|robots.txt||##robots##||
    |=|Ruby on Rails||##rails##|=|Ruby||##ruby##|=|SAS||##sas##||
    |=|Scheme||##scheme##|=|sdlBasic||##sdlbasic##|=|Smarty||##smarty##||
    |=|SQL||##sql##|=|TCL/iTCL||##tcl##|=|T-SQL||##tsql##||
    |=|Text||##text##|=|thinBasic||##thinbasic##|=|Unoidl||##idl##||
    |=|VB.NET||##vbnet##|=|VHDL||##vhdl##|=|Visual BASIC||##vb##||
    |=|Visual Fox Pro||##visualfoxpro##|=|""WinBatch""||##winbatch##|=|XML||##xml##||
    |=|X""++""||##xpp##|=|""ZiLOG"" Z80 Assembler||##z80##|=| ||


    ===13. Mindmaps===

    Wikka has native support for [[Wikka:FreeMind mindmaps]]. There are two options for embedding a mindmap in a wiki page.

    **Option 1:** Upload a ""FreeMind"" file to a webserver, and then place a link to it on a wikka page:
      ##""http://yourdomain.com/freemind/freemind.mm""##
    No special formatting is necessary.

    **Option 2:** Paste the ""FreeMind"" data directly into a wikka page:
    ~- Open a ""FreeMind"" file with a text editor.
    ~- Select all, and copy the data.
    ~- Browse to your Wikka site and paste the Freemind data into a page. 

    ===14. Embedded HTML===

    You can easily paste HTML in a wiki page by wrapping it into two sets of doublequotes. 

    ~##&quot;&quot;[html code]&quot;&quot;##

    <<
    ~##&quot;&quot;y = x<sup>n+1</sup>&quot;&quot;##
    ~""y = x<sup>n+1</sup>""
    <<::c::
    <<
    ~##&quot;&quot;<acronym title="Cascade Style Sheet">CSS</acronym>&quot;&quot;##
    ~""<acronym title="Cascade Style Sheet">CSS</acronym>""
    <<::c::By default, some HTML tags are removed by the ""SafeHTML"" parser to protect against potentially dangerous code.  The list of tags that are stripped can be found on the Wikka:SafeHTML page.

    It is possible to allow //all// HTML tags to be used, see Wikka:UsingHTML for more information.

    ----
    [[CategoryWiki Wiki category]]'),
                  __('HighScores')=>__('{{highscores full="1"}}

    ----
    [[CategoryWiki Wiki category]]'),
                      __('OwnedPages')=>__('{{ownedpages}}

    These numbers merely reflect how many pages you have created, not how much content you have contributed or the quality of your contributions. To see how you rank with other members, you may be interested in checking out the HighScores.
    ----
    CategoryWiki'),
                  __('SandBox')=>__('Test your formatting skills here.







    ----
    [[CategoryWiki Wiki category]]'));

        foreach ($tags as $name => $tag) {
            $record['tag']  = $name;
            $record['body'] = $tag;

            if (!DBUtil::insertObject($record, 'wikula_pages')) {
                return LogUtil::registerError(__('Failed filling Database: ').' page '.$name+1);
            }
        }

        return true;
    }

    /**
     * Import Wakka Data
     */
    public function ImportData()
    {
    /*    $dbconn  =& pnDBGetConn(true);
        $pntable =& pnDBGetTables();

        $tbl = &$pntable['wikula_pages'];
        $col = &$pntable['wikula_pages_column'];

        $wikkatbl = 'wikka_pages';
        $wikkacol = array('id'      => $wikkatbl.'.id',
                          'tag'     => $wikkatbl.'.tag',
                          'time'    => $wikkatbl.'.time',
                          'body'    => $wikkatbl.'.body',
                          'owner'   => $wikkatbl.'.owner',
                          'user'    => $wikkatbl.'.user',
                          'latest'  => $wikkatbl.'.latest',
                          'note'    => $wikkatbl.'.note',
                          'handler' => $wikkatbl.'.handler');

        $source = 'SELECT '.$wikkacol['id'].', '
                           .$wikkacol['tag'].', '
                           .$wikkacol['time'].', '
                           .$wikkacol['body'].', '
                           .$wikkacol['owner'].', '
                           .$wikkacol['user'].', '
                           .$wikkacol['latest'].', '
                           .$wikkacol['note'].', '
                           .$wikkacol['handler']
              .' FROM '.$wikkatbl
              .' ORDER BY '.$wikkacol['id'];

        $result = $dbconn->Execute($source);
    */
        $result = DBUtil::selectObjectArray('wikka_pages');

       /* if (false == $result) {
            return LogUtil::registerError(__('Import data failed: '));
        }*/

        DBUtil::insertObjectArray($result,'wikula_pages');

    /*

        for(; !$result->EOF; $result->MoveNext()) {
            list($id, $tag, $time, $body, $owner, $user, $latest, $note, $handler) = $result->fields;

            list($id, $tag, $time, $body, $owner, $user, $latest, $note, $handler) = DataUtil::formatForStore($id, $tag, $time, $body, $owner, $user, $latest, $note, $handler);

            $target = 'INSERT INTO '.$tbl.' ('
                        .$col['id'].', '
                        .$col['tag'].', '
                        .$col['time'].', '
                        .$col['body'].', '
                        .$col['owner'].', '
                        .$col['user'].', '
                        .$col['latest'].', '
                        .$col['note'].', '
                        .$col['handler']
                      .') VALUES ("'.$id.'", '
                        .'"'.$tag.'", '
                        .'"'.$time.'", '
                        .'"'.$body.'", '
                        .'"'.$owner.'", '
                        .'"'.$user.'", '
                        .'"'.$latest.'", '
                        .'"'.$note.'", '
                        .'"'.$handler.'"
                      )';

            $dbconn->Execute($target);

            if ($dbconn->ErrorNo() != 0) {
                return LogUtil::registerError(__('Import data failed: ').' target - '.$dbconn->ErrorMsg());
            }
        }

        $result->Close();
    */
    /*
        $tbl = &$pntable['wikula_links'];
        $col = &$pntable['wikula_links_column'];

        $wikkatbl = 'wikka_links';
        $wikkacol = array('from_tag' => $wikkatbl.'.from_tag',
                          'to_tag'   => $wikkatbl.'.to_tag');

        $source = 'SELECT '.$wikkacol['from_tag'].', '.$wikkacol['to_tag'].' FROM '.$wikkatbl;

        $result = $dbconn->Execute($source);
    */

        $result = DBUtil::selectObjectArray('wikka_links');

      /*  if (false == $result) {
            return LogUtil::registerError(__('Import data failed: '));
        }*/

        DBUtil::insertObjectArray($result,'wikula_links');
    /*

        if ($dbconn->ErrorNo() != 0) {
            return LogUtil::registerError(__('PROBLEM IMPORTING DATA').' - '.$dbconn->ErrorMsg());
        }

        for(; !$result->EOF; $result->MoveNext()) {
            list($from_tag, $to_tag) = $result->fields;

            $target = 'INSERT INTO '.$tbl.' ('.$col['from_tag'].', '.$col['to_tag']
                                 .') VALUES ("'.DataUtil::formatForStore($from_tag).'", "'.DataUtil::formatForStore($to_tag).'")';

            $dbconn->Execute($target);

            if ($dbconn->ErrorNo() != 0) {
                return LogUtil::registerError(__('Import data failed: ').' target - '.$dbconn->ErrorMsg());
            }
        }

        $result->Close();
    */
        return true;
    }
}
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
            __('FormattingRules', $dom)    => self::FormattingRules(),
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
        $page = __("=====Welcome to your Wiki!=====
Thanks for install **[[http://code.zikula.org/wikula/ Wikula]]**! The Wiki module for Zikula based on [[http://wikkawiki.org WikkaWiki]].
This site is running on version {{wikkaversion}}.

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

Enjoy!", $dom);
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
    
    
    public static function FormattingRules() 
    {
        $dom = ZLanguage::getModuleDomain('Wikula');
        $page = __('======Wikka Formatting Guide======
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

It is possible to allow //all// HTML tags to be used, see Wikka:UsingHTML for more information.');
        return $page;
    }

}
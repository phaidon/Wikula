-- phpMyAdmin SQL Dump
-- version 3.4.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 06, 2012 at 03:14 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `z12`
--

-- --------------------------------------------------------

--
-- Table structure for table `wikula_links`
--

CREATE TABLE IF NOT EXISTS `wikula_links` (
  `from_tag` varchar(75) NOT NULL DEFAULT '',
  `to_tag` varchar(75) NOT NULL DEFAULT '',
  UNIQUE KEY `idx_from,idx_to` (`from_tag`,`to_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wikula_pages`
--

CREATE TABLE IF NOT EXISTS `wikula_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(75) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `body` longtext,
  `owner` varchar(75) NOT NULL DEFAULT '',
  `user` varchar(75) NOT NULL DEFAULT '',
  `latest` varchar(1) NOT NULL DEFAULT 'N',
  `note` varchar(100) NOT NULL DEFAULT '',
  `handler` varchar(30) NOT NULL DEFAULT 'page',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `tag` (`tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `wikula_pages`
--

INSERT INTO `wikula_pages` (`id`, `tag`, `time`, `body`, `owner`, `user`, `latest`, `note`, `handler`) VALUES
(1, 'HomePage', '2012-01-06 15:13:26', '=====Welcome to your Wiki!=====\nThanks for install **[[http://code.zikula.org/wikula/ Wikula]]**! The Wiki module for Zikula based on [[http://wikkawiki.org WikkaWiki]].\nThis site is running on version {{wikkaversion}} (see the [[ReleaseNotes release notes]] to learn what''s new in this release).\n\n>>==Contribute==\nYou can report bugs or file feature requests\non the [[http://code.zikula.org/wikula Wikula development website]]!\n>>====Getting started====\nIf you are not sure how a wiki works, you can check out the [[WikiHelp Help page]] to get started or click or the &quot;edit page&quot; link at the bottom.\n\n====Some useful pages====\n~-[[FormattingRules Formatting guide]]\n~-[[WikiHelp Help page]]\n~-[[RecentChanges Recently modified pages]]\n\nYou will find more useful pages in the [[CategoryWiki Wiki category]] or in the [[PageIndex Page index]].\n\nEnjoy!', 'admin', 'admin', 'Y', 'Initial Insert', 'page'),
(2, 'RecentChanges', '2012-01-06 15:13:26', '{{RecentChanges}}\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(3, 'PageIndex', '2012-01-06 15:13:26', '{{PageIndex}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(4, 'ReleaseNotes', '2012-01-06 15:13:26', '{{WikkaChanges}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(5, 'WikiHelp', '2012-01-06 15:13:26', '=====Useful pages=====\n  * FormattingRules: learn how to format the contents in this wiki\n  * SandBox: play with your formatting skills\n\n  * PageIndex: index of the available pages on the wiki\n  * WantedPages: check out the pages pending for creation\n  * OrphanedPages: list of orphaned pages\n  * TextSearch: search something of your interest in the wiki\n  * TextSearchExpanded: fine grained search if you haven''t found anything in the normal search\n\n  * WikiCategory: learn how works the categorization system of this wiki\n  * CategoryWiki: list of pages related to the Wiki\n  * InterWiki: check the allowed interwiki links\n\n  * RecentChanges: check which pages that were changed recently\n  * HighScores: check who had contributed more to the wiki\n  * OwnedPages: check out how many pages you own on the wiki\n  * MyChanges: list of changes that you have done\n  * MyPages: list of pages that you own on this wiki\n\nYou will find more useful pages in the [[WikiCategory Wiki category]] or in the [[PageIndex Page index]].\n\n=====Wikka Documentation=====\nComprehensive and up-to-date documentation can be found on the [[http://docs.wikkawiki.org/WikkaDocumentation Wikka Documentation server]].\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(6, 'WantedPages', '2012-01-06 15:13:26', '{{wantedpages}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(7, 'OrphanedPages', '2012-01-06 15:13:26', '{{orphanedpages}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(8, 'TextSearch', '2012-01-06 15:13:26', '{{textsearch}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(9, 'TextSearchExpanded', '2012-01-06 15:13:26', '{{textsearchexpanded}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(10, 'MyPages', '2012-01-06 15:13:26', '{{mypages}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(11, 'MyChanges', '2012-01-06 15:13:26', '{{mychanges}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(12, 'InterWiki', '2012-01-06 15:13:26', '{{interwikilist}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(13, 'WikiCategory', '2012-01-06 15:13:26', '===Categorization system===\nThis wiki is using a very flexible but simple categorizing system to keep everything properly organized\n\n{{Category page="CategoryCategory" col="3" full="1"}}\n==Here''s how it works:==\n~- The master list of the categories is **""CategoryCategory""** which will automatically list all known main categories, and should never be edited. This list is easily accessed from the Wiki''s top navigation bar. (Categories).\n~- Pages can belong to zero or more categories. Including a page in a category is done by simply linking to the ""CategoryName"" on the page (by convention at the very end of the page). To mention a category on a page when the page does not belong to it, write its name in double double quote in order to unwikify it. To link to a category on a page when the page does not belong to it, link it using full URL like ""[[http://yoursite.com/CategoryXy CategoryXy]]"".\n~- The system allows to build hierarchies of categories by referring to the parent category in the subcategory page. The parent category page will then automatically include the subcategory page in its list.\n~- A special kind of category is **""CategoryUsers""** to group the userpages, so your Wiki homepage should include it at the end to be included in the category-driven userlist.\n~- New categories can be created (think very hard before doing this though, you don''t need too much of them) by creating a ""CategoryName"" page, including ""{{Category}}"" in it and placing it in the **""CategoryCategory""** category (for a main category or another parent category in case you want to create a subcategory).\n\n{{sidenote type="warning" title="Please" text="help to keep this place organized by including the relevant categories in new and existing pages!" width="100%" side="none"}}\n**Notes:**\n~- The above bold items were coded using double doublequote in order to unwikify them to prevent this page from showing up in the mentioned categories. This page only belongs in CategoryWiki (which can be safely mentioned) after all !\n~- In order to avoid accidental miscategorization you should unwikify all camelcased non-related ""CategoryName"" on a page. This is a side-effect of how the categorizing system works: it''s based on a backlinking and is not restricted to the footer convention.\n~- Don''t be put of by the name of this page (WikiCategory) which is a logical name (it''s about the Wiki and explains Category) but doesn''t have any special role in the Categorizing system.\n~- To end with this is the **standard convention** to include the categories (both the wiki code and the result):\n%%==Categories==\nCategoryWiki\n\nor\n==Categories==\n[[CategoryWiki Wiki category]]%%\n\n----\n==Categories==\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(14, 'CategoryWiki', '2012-01-06 15:13:26', '===Wiki Related Category===\nThis Category will contain links to pages talking about Wikis and Wikis specific topics. When creating such pages, be sure to include CategoryWiki at the bottom of each page, so that page shows listed.\n----\n{{Category col="3" full="1" notitle="1"}}\n----\n[[CategoryCategory List of all categories]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(15, 'CategoryCategory', '2012-01-06 15:13:26', '===List of All Categories===\nBelow is the list of all Categories existing on this Wiki, granted that users did things right when they created their pages or new Categories. See WikiCategory for how the system works.\n----\n{{Category compact="1" notitle="1"}}', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(16, 'FormattingRules', '2012-01-06 15:13:26', '======Wikka Formatting Guide======\n<<**Note:** Anything between 2 sets of double-quotes is not formatted.\n<<::c::Once you have read through this, test your formatting skills in the SandBox.\n----\n===1. Text Formatting===\n\n<<\n~##""**I''m bold**""##\n~**I''m bold **\n<<::c::\n<<\n~##""//I''m italic text!//""##\n~//I''m italic text!//\n<<::c::\n<<\n~##""And I''m __underlined__!""##\n~And I''m __underlined__!\n<<::c::\n<<\n~##""##monospace text##""##\n~##monospace text##\n<<::c::\n<<\n~##""''''highlight text''''""## (using 2 single-quotes)\n~''''highlight text''''\n<<::c::\n<<\n~##""++Strike through text++""##\n~++Strike through text++\n<<::c::\n<<\n~##""&pound;&pound;Text insertion&pound;&pound;""##\n~ &pound;&pound;Text insertion&pound;&pound;\n<<::c::\n<<\n~##""&yen;&yen;Text deletion&yen;&yen;""##\n~ &yen;&yen;Text deletion&yen;&yen;\n<<::c::\n<<\n~##""Press #%ANY KEY#%""##\n~Press #%ANY KEY#%\n<<::c::\n<<\n~##""@@Center text@@""##\n~@@Center text@@\n<<::c::\n<<\n~##""/* Elided content (eliminates trailing ws */""##\n~Elides (hides) content from displaying.  Eliminates trailing whitespace so there are no unsightly gaps in output. Useful for commenting Wikka markup.\n<<::c::\n<<\n~##""`` Elided content (preserves trailing ws ``""##\n~Elides (hides) content from displaying.  Preserves trailing whitespace.\n<<::c::\n\n===2. Headers===\n\nUse between six ##=## (for the biggest header) and two ##=## (for the smallest header) on both sides of a text to render it as a header.\n\n<<\n~##""====== Really big header ======""##\n~====== Really big header ======\n<<::c::\n<<\n~##""===== Rather big header =====""##\n~===== Rather big header =====\n<<::c::\n<<\n~##""==== Medium header ====""##\n~==== Medium header ====\n<<::c::\n<<\n~##""=== Not-so-big header ===""##\n~=== Not-so-big header ===\n<<::c::\n<<\n~##""== Smallish header ==""##\n~== Smallish header ==\n<<::c::\n\n===3. Horizontal separator===\n\n~##""----""##\n----\n\n===4. Forced line break===\n\n~##""---""##\n---\n\n===5. Lists and indents===\n\nYou can indent text using a **~**, a **tab** or **2 spaces**.\n\n<<##""~This text is indented<br />~~This text is double-indented<br />&nbsp;&nbsp;This text is also indented""##\n<<::c::\n<<\n~This text is indented\n~~This text is double-indented\n~This text is also indented\n<<::c::\n\nTo create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a ##**~**##):\n\n**Bulleted lists**\n<<##""~- Line one""##\n##""~- Line two""##\n<<::c::\n<<\n~- Line one\n~- Line two\n<<::c::\n\n**Numbered lists**\n<<##""~1) Line one""##\n##""~1) Line two""##\n<<::c::\n<<\n~1) Line one\n~1) Line two\n<<::c::\n\n**Ordered lists using uppercase characters**\n<<##""~A) Line one""##\n##""~A) Line two""##\n<<::c::\n<<\n~A) Line one\n~A) Line two\n<<::c::\n\n**Ordered lists using lowercase characters**\n<<##""~a) Line one""##\n##""~a) Line two""##\n<<::c::\n<<\n~a) Line one\n~a) Line two\n<<::c::\n\n**Ordered lists using roman numerals**\n<<##""~I) Line one""##\n##""~I) Line two""##\n<<::c::\n<<\n~I) Line one\n~I) Line two\n<<::c::\n\n**Ordered lists using lowercase roman numerals**\n<<##""~i) Line one""##\n##""~i) Line two""##\n<<::c::\n<<\n~i) Line one\n~i) Line two\n<<::c::\n\n===6. Inline comments===\n\nTo format some text as an inline comment, use an indent ( **~**, a **tab** or **2 spaces**) followed by a **""&amp;""**.\n\n<<\n##""~&amp; Comment""##\n##""~~&amp; Subcomment""##\n##""~~~&amp; Subsubcomment""##\n<<::c::\n<<\n~& Comment\n~~& Subcomment\n~~~& Subsubcomment\n<<::c::\n\n===7. Images===\n\nTo place images on a Wiki page, you can use the ##image## action.\n\n<<\n~##""{{image class="center" alt="DVD logo" title="An Image Link" url="images/dvdvideo.gif" link="RecentChanges"}}""##\n~{{image class="center" alt="dvd logo" title="An Image Link" url="modules/wikula/pnimages/dvdvideo.gif" link="RecentChanges"}}\n<<::c::\n\nLinks can be external, or internal Wiki links. You don''t need to enter a link at all, and in that case just an image will be inserted. You can use the optional classes ##left## and ##right## to float images left and right. You don''t need to use all those attributes, only ##url## is required while ##alt## is recommended for accessibility.\n\n===8. Links===\n\nTo create a **link to a wiki page** you can use any of the following options:\n---\n~1) type a ##""WikiName""##: --- --- ##""FormattingRules""## --- FormattingRules --- ---\n~1) add a forced link surrounding the page name by ##""[[""## and ##""]]""## (everything after the first space will be shown as description): --- --- ##""[[SandBox Test your formatting skills]]""## --- [[SandBox Test your formatting skills]] --- --- ##""[[SandBox &#27801;&#31665;]]""## --- [[SandBox &#27801;&#31665;]] --- ---\n~1) add an image with a link (see instructions above).\n\nTo **link to external pages**, you can do any of the following:\n---\n~1) type a URL inside the page: --- --- ##""http://www.example.com""## --- http://www.example.com --- --- \n~1) add a forced link surrounding the URL by ##""[[""## and ##""]]""## (everything after the first space will be shown as description): --- --- ##""[[http://example.com/jenna/ Jenna''s Home Page]]""## --- [[http://example.com/jenna/ Jenna''s Home Page]] --- --- ##""[[mail@example.com Write me!]]""## --- [[mail@example.com Write me!]] --- ---\n~1) add an image with a link (see instructions above); --- ---\n~1) add an interwiki link (browse the [[InterWiki list of available interwiki tags]]): --- --- ##""WikiPedia:WikkaWiki""## --- WikiPedia:WikkaWiki --- --- ##""Google:CSS""## --- Google:CSS --- --- ##""Thesaurus:Happy""## --- Thesaurus:Happy --- ---\n\n===9. Tables===\n\nTo create a table, you can use the ##table## action.\n\n<<\n~##""{{table columns="3" cellpadding="1" cells="BIG;GREEN;FROGS;yes;yes;no;no;no;###"}}""##\n~{{table columns="3" cellpadding="1" cells="BIG;GREEN;FROGS;yes;yes;no;no;no;###"}}\n<<::c::Note that ##""###""## must be used to indicate an empty cell.\nComplex tables can also be created by embedding HTML code in a wiki page (see instructions below).\n\n===10. Colored Text===\n\nColored text can be created using the ##color## action:\n\n<<\n~##""{{color c="blue" text="This is a test."}}""##\n~{{color c="blue" text="This is a test."}}\n<<::c::You can also use hex values:\n\n<<\n~##""{{color hex="#DD0000" text="This is another test."}}""##\n~{{color hex="#DD0000" text="This is another test."}}\n<<::c::Alternatively, you can specify a foreground and background color using the ##fg## and ##bg## parameters (they accept both named and hex values):\n\n<<\n~##""{{color fg="#FF0000" bg="#000000" text="This is colored text on colored background"}}""##\n~{{color fg="#FF0000" bg="#000000" text="This is colored text on colored background"}}\n<<::c::\n<<\n~##""{{color fg="yellow" bg="black" text="This is colored text on colored background"}}""##\n~{{color fg="yellow" bg="black" text="This is colored text on colored background"}}\n<<::c::\n\n===11. Floats===\n\nTo create a **left floated box**, use two ##<## characters before and after the block.\n\n**Example:**\n\n~##""&lt;&lt;Some text in a left-floated box hanging around&lt;&lt; Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.""##\n\n<<Some text in a left-floated box hanging around<<Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n::c::To create a **right floated box**, use two ##>## characters before and after the block.\n\n**Example:**\n\n~##"">>Some text in a right-floated box hanging around>> Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.""##\n\n   >>Some text in a right-floated box hanging around>>Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n::c:: Use ##""::c::""##  to clear floated blocks.\n\n===12. Code formatters===\n\nYou can easily embed code blocks in a wiki page using a simple markup. Anything within a code block is displayed literally. \nTo create a **generic code block** you can use the following markup:\n\n~##""%% This is a code block %%""##. \n\n%% This is a code block %%\n\nTo create a **code block with syntax highlighting**, you need to specify a //code formatter// (see below for a list of available code formatters). \n\n~##""%%(""{{color c="red" text="php"}}"")<br />&lt;?php<br />echo "Hello, World!";<br />?&gt;<br />%%""##\n\n%%(php)\n<?php\necho "Hello, World!";\n?>\n%%\n\nYou can also specify an optional //starting line// number.\n\n~##""%%(php;""{{color c="red" text="15"}}"")<br />&lt;?php<br />echo "Hello, World!";<br />?&gt;<br />%%""##\n\n%%(php;15)\n<?php\necho "Hello, World!";\n?>\n%%\n\nIf you specify a //filename//, this will be used for downloading the code.\n\n~##""%%(php;15;""{{color c="red" text="test.php"}}"")<br />&lt;?php<br />echo "Hello, World!";<br />?&gt;<br />%%""##\n\n%%(php;15;test.php)\n<?php\necho "Hello, World!";\n?>\n%%\n\n|?|List of available code formatters||\n||\n|=|Language|=|Formatter|=|Language|=|Formatter|=|Language|=|Formatter||\n|#|\n|=|Actionscript||##actionscript##|=|ABAP||##abap##|=|ADA||##ada##||\n|=|Apache Log||##apache##|=|""AppleScript""||##applescript##|=|ASM||##asm##||\n|=|ASP||##asp##|=|""AutoIT""||##autoit##|=|Bash||##bash##||\n|=|""BlitzBasic""||##blitzbasic##|=|""Basic4GL""||##basic4gl##|=|bnf||##bnf##||\n|=|C||##c##|=|C for Macs||##c_mac##|=|C#||##csharp##||\n|=|C""++""||##cpp##|=|C""++"" (+QT)||##cpp-qt##|=|CAD DCL||##caddcl##||\n|=|""CadLisp""||##cadlisp##|=|CFDG||##cfdg##|=|""ColdFusion""||##cfm##||\n|=|CSS||##css##|=|D||##d##|=|Delphi||##delphi##||\n|=|Diff-Output||##diff##|=|DIV||##div##|=|DOS||##dos##||\n|=|Dot||##dot##|=|Eiffel||##eiffel##|=|Fortran||##fortran##||\n|=|""FreeBasic""||##freebasic##|=|FOURJ''s Genero 4GL||##genero##|=|GML||##gml##||\n|=|Groovy||##groovy##|=|Haskell||##haskell##|=|HTML||##html4strict##||\n|=|INI||##ini##|=|Inno Script||##inno##|=|Io||##io##||\n|=|Java 5||##java5##|=|Java||##java##|=|Javascript||##javascript##||\n|=|""LaTeX""||##latex##|=|Lisp||##lisp##|=|Lua||##lua##||\n|=|Matlab||##matlab##|=|mIRC Scripting||##mirc##|=|Microchip Assembler||##mpasm##||\n|=|Microsoft Registry||##reg##|=|Motorola 68k Assembler||##m68k##|=|""MySQL""||##mysql##||\n|=|NSIS||##nsis##|=|Objective C||##objc##|=|""OpenOffice"" BASIC||##oobas##||\n|=|Objective Caml||##ocaml##|=|Objective Caml (brief)||##ocaml-brief##|=|Oracle 8||##oracle8##||\n|=|Pascal||##pascal##|=|Per (FOURJ''s Genero 4GL)||##per##|=|Perl||##perl##||\n|=|PHP||##php##|=|PHP (brief)||##php-brief##|=|PL/SQL||##plsql##||\n|=|Python||##phyton##|=|Q(uick)BASIC||##qbasic##|=|robots.txt||##robots##||\n|=|Ruby on Rails||##rails##|=|Ruby||##ruby##|=|SAS||##sas##||\n|=|Scheme||##scheme##|=|sdlBasic||##sdlbasic##|=|Smarty||##smarty##||\n|=|SQL||##sql##|=|TCL/iTCL||##tcl##|=|T-SQL||##tsql##||\n|=|Text||##text##|=|thinBasic||##thinbasic##|=|Unoidl||##idl##||\n|=|VB.NET||##vbnet##|=|VHDL||##vhdl##|=|Visual BASIC||##vb##||\n|=|Visual Fox Pro||##visualfoxpro##|=|""WinBatch""||##winbatch##|=|XML||##xml##||\n|=|X""++""||##xpp##|=|""ZiLOG"" Z80 Assembler||##z80##|=| ||\n\n\n===13. Mindmaps===\n\nWikka has native support for [[Wikka:FreeMind mindmaps]]. There are two options for embedding a mindmap in a wiki page.\n\n**Option 1:** Upload a ""FreeMind"" file to a webserver, and then place a link to it on a wikka page:\n  ##""http://yourdomain.com/freemind/freemind.mm""##\nNo special formatting is necessary.\n\n**Option 2:** Paste the ""FreeMind"" data directly into a wikka page:\n~- Open a ""FreeMind"" file with a text editor.\n~- Select all, and copy the data.\n~- Browse to your Wikka site and paste the Freemind data into a page. \n\n===14. Embedded HTML===\n\nYou can easily paste HTML in a wiki page by wrapping it into two sets of doublequotes. \n\n~##&quot;&quot;[html code]&quot;&quot;##\n\n<<\n~##&quot;&quot;y = x<sup>n+1</sup>&quot;&quot;##\n~""y = x<sup>n+1</sup>""\n<<::c::\n<<\n~##&quot;&quot;<acronym title="Cascade Style Sheet">CSS</acronym>&quot;&quot;##\n~""<acronym title="Cascade Style Sheet">CSS</acronym>""\n<<::c::By default, some HTML tags are removed by the ""SafeHTML"" parser to protect against potentially dangerous code.  The list of tags that are stripped can be found on the Wikka:SafeHTML page.\n\nIt is possible to allow //all// HTML tags to be used, see Wikka:UsingHTML for more information.\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(17, 'HighScores', '2012-01-06 15:13:26', '{{highscores full="1"}}\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(18, 'OwnedPages', '2012-01-06 15:13:26', '{{ownedpages}}\n\nThese numbers merely reflect how many pages you have created, not how much content you have contributed or the quality of your contributions. To see how you rank with other members, you may be interested in checking out the HighScores.\n----\nCategoryWiki', '(Public)', 'admin', 'Y', 'Initial Insert', 'page'),
(19, 'SandBox', '2012-01-06 15:13:26', 'Test your formatting skills here.\n\n\n\n\n\n\n\n----\n[[CategoryWiki Wiki category]]', '(Public)', 'admin', 'Y', 'Initial Insert', 'page');

-- --------------------------------------------------------

--
-- Table structure for table `wikula_referrers`
--

CREATE TABLE IF NOT EXISTS `wikula_referrers` (
  `page_tag` varchar(75) NOT NULL DEFAULT '',
  `referrer` varchar(150) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `idx_page_tag,idx_time` (`page_tag`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


DELETE FROM `modules` WHERE `modules`.`name` = 'Wikula';


INSERT INTO `modules` (`name`, `type`, `displayname`, `url`, `description`, `directory`, `version`, `state`, `securityschema`) VALUES
('wikula', 2, 'Wikula', 'wikula', 'The Wikula module provides a wiki to your website.', 'wikula', '1.2', 0, 'a:1:{s:8:"wikula::";s:14:"page::Page Tag";}');


INSERT INTO `module_vars` (`modname`, `name`, `value`) VALUES
('wikula', 'double_doublequote_html', 's:4:"safe";'),
('wikula', 'excludefromhistory', 's:8:"HomePage";'),
('wikula', 'geshi_header', 's:0:"";'),
('wikula', 'geshi_line_numbers', 's:1:"1";'),
('wikula', 'geshi_tab_width', 'i:4;'),
('wikula', 'grabcode_button', 'b:1;'),
('wikula', 'hideeditbar', 'b:0;'),
('wikula', 'hidehistory', 'i:20;'),
('wikula', 'itemsperpage', 'i:25;'),
('wikula', 'langinstall', 's:3:"eng";'),
('wikula', 'logreferers', 'b:0;'),
('wikula', 'modulestylesheet', 's:9:"style.css";'),
('wikula', 'root_page', 's:8:"HomePage";'),
('wikula', 'savewarning', 'b:0;');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


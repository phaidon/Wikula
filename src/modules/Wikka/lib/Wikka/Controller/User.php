<?php
/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikka
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Access to (administrative) user-initiated actions.
 */
class Wikka_Controller_User extends Zikula_AbstractController
{
    
    /**
     * This function shows a wikka test.
     *
     *@return string
     */  
    public function main()
    {

        $wikkaCode = '======Wikka Formatting Guide======
<<**Note:** Anything between 2 sets of double-quotes is not formatted.
<<::c::
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
~##""{{image class="center" alt="DVD logo" title="An Image Link" url="images/logo.gif" link="RecentChanges"}}""##
~{{image class="center" alt="dvd logo" title="An Image Link" url="images/logo.gif" link="RecentChanges"}}
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

===13. Embedded HTML===

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
[[CategoryWiki Wiki category]]';

        $wikkaCode = array(
            'text'   => $wikkaCode,
            'modname' => 'Wikula'
        );
        return ModUtil::apiFunc('Wikka', 'transform', 'transform', $wikkaCode);
    }

    
}
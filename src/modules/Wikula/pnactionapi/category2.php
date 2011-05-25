<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: category2.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

function wikula_actionapi_category2($args)
{
    return ModUtil::apiFunc('wikula', 'user', 'Action',
                        array('action' => 'category',
                              'args'   => $args));
}

/**
 * Generates a list of pages belonging to the specified or top-level category.
 *
 * If no category page is specified, the current page is assumed to be a category (or the 'base' page
 * for the 'related pages' feature).
 *
 * Features:
 * - specify whether to show subcategories, pages, or both; separate or all together as "members"
 * - specify whether to format the output as columns or as a "flat" list
 * - list 'related pages': pages other than the current one that are in the same category or categories
 *   (output will always be in the form of one or more lists)
 * - specify a class name as 'hook' for styling
 * - lists are generated as type 'menu' so they can be given "menu" styling
 * - specify a (main) heading to override the built-in defaults
 *
 * There are two possible "contexts" for the action:
 * - A category (either the current category page or specified with the cat parameter): used to list
 *   members of that category. The action can be <b>used</b> on any page; if not a category page, the cat
 *   parameter must be specified in order to list category members (otherwise the action will revert to
 *   list the content of the top-level category).
 * - a content page for the 'related pages' feature. To list related pages, specify type='related' and
 *   (optionally) the category to list (other) member pages of. If no category is specified, all
 *   categories of the current page are considered; if a category is specified, it will be considered
 *   only if it is actually one of the categories the current page belongs to.
 *
 * Several features are dependent on a naming convention where each category ("category page") starts
 * with 'Category'. While it's (technically) possible to have categories without this naming convention,
 * this makes features like separating subcategories from content pages in a listing impossible.
 * The default is to assume the naming convention is used; if not, override this behavior with a
 * catnames="0" parameter: the action will then ignore any contradictory parameters and revert to just
 * listing 'members' of the current or requested category.
 *
 * Template pages (created specifically to clone from) are normally not "real" content pages; the default
 * is to filter these pages (so only actual content pages are listed), which will only work if the
 * naming convention for them is followed by ending the page name with 'Template'. This behavior can
 * be overridden by specifying an inctpl="1" parameter. One exception: page names that start with
 * 'Category' and end with 'Template' are considered a proper category, intended to contain templates:
 * all members of such a 'template category' are considered even when the 'inctpl' parameter is set to 0.
 *
 * Note: the list view (type='list' or type='related') is nice for a sidebar while the columnar view
 * (type='cols') is more suited as content for a category page.
 *
 * Syntax:
 *  {{category [cat="categoryname"] [show="all|categories|pages|members"] [type="cols|list|related"] [cols="n"] [catnames="0|1"] [inctpl="0|1"] [class="class"] [head="main heading"]}}
 *
 * Old (version 1) parameters are supported for compatibility but new ones take precedence.
 *
 * @todo        - possible? use a single list also for columns, using CSS to visually split up into columns - JW 2005-01-19
 *              - possible to clean this to a Zikula compatible way? :-P
 *
 * @package     Actions
 * @subpackage  SystemContent
 * @name        Category
 *
 * @author      {@link http://wikka.jsnx.com/JsnX JsnX}
 * @author      {@link http://wikka.jsnx.com/NilsLindenberg NilsLindenberg} (separation into subcategories and pages)
 * @author      {@link http://wikka.jsnx.com/JavaWoman JavaWoman} (complete rewrite, filtering, table-less columns and 'related pages' functionality)
 * @copyright   Copyright � 2005, Marjolein Katsma
 * @license     http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @since       Wikka 1.0.0
 * @version     2.5beta
 *
 * @input       string  $cat        optional: category to list members of; default: current page or CategoryCategory
 *                                  You can specify '/' as a shortcut for CategoryCategory
 * @input       string  $show       optional: all|categories|pages|members; default: all; or members if $catnames
 *                                  is 0.
 * @input       string  $type       optional: cols|list|related; default: cols.
 *                                  - cols: multi-column display, list everything. (Useful for category pages)
 *                                  - list: single list, strips 'Category' from category (page) names.
 *                                  (Useful for sidebar)
 *                                  - related:  "related pages": list(s) with pages in the same categories as
 *                                  the current page, or in requested category only; excluding the current page.
 *                                  (useful for sidebar)
 *
 * @input       integer $catnames   optional: consider only category names (pages) that start with 'Category' as a
 *                                  category (the name test is case-insensitive); default: 1
 *                                  set to 0 if there is no naming convention for categories!
 * @input       integer $inctpl     optional: include "template" pages (1) or not (0); default: 0.
 * @input       integer $cols       optional: number of columns to use; default: 1 (only relevant for type"cols")
 * @input       integer $class      optional: class(es) to determine styling of the output list or columns
 * @input       string  $head       optional: override of built-in main heading; you can specify a '%s' placeholder
 *                                  for the requested category in a category members listing (type="cols|list")
 *
 * @input       string  $page       DEPRECATED (optional): superseded by $cat (synonym)
 * @input       integer $compact    DEPRECATED (optional): use columns (0) or list (1); superseded by $type
 * @input       integer $col        DEPRECATED (optional): superseded by $cols
 *
 * @output      string  list of pages belonging to the specified or top-level category, formatted as a list
 *                      or columns of items
 *
 * @uses        CheckMySQLVersion()
 * @uses        getCatMembers()
 * @uses        Link()
 * @uses        makeId()
 * @uses        makeMemberList()
 * @uses        makeMemberCols()
 */

// ----------------- utility functions ------------------
/*
if (!function_exists('buildMemberList'))
{
    function buildMemberList($member,&$list,$cat,$bIncTpl)
    {
        global $wakka;
        if (
            (FALSE === $bIncTpl && preg_match('/(.+)Template$/',$member)) ||# template filter
            ('CategoryCategory' == $member) ||                              # do not list top-level category as member
            ($member == $cat)                                               # do not list requested category as member
           )
        {
#echo '(Members) Filtered: '.$member.'<br/>';
            // do nothing
        }
        else
        {
            $list[] = $wakka->Link($member);
        }
    }
}
if (!function_exists('buildCatList'))
{
    function buildCatList($member,&$list,$cat,$type)
    {
        global $wakka;
        if (
            (!preg_match('/^Category/',$member)) ||                         # only category pages count
            ('CategoryCategory' == $member) ||                              # do not list top-level category as member
            ($member == $cat)                                               # do not list requested category as member
           )
        {
#echo '(Cats) Filtered: '.$member.'<br/>';
            // do nothing
        }
        else
        {
            if ('cols' == $type)
            {
                $list[] = $wakka->Link($member);
            }
            elseif ('list' == $type)
            {
                $list[] = $wakka->Link($member,'',preg_replace('/^Category/','',$member));
            }
        }
    }
}
if (!function_exists('buildPageList'))
{
    function buildPageList($member,&$list,$cat,$bIncTpl,$type,$page)
    {
        global $wakka;
        if (
            (preg_match('/^Category/',$member)) ||                          # categories filter (only pages count)
            (FALSE === $bIncTpl && preg_match('/Template$/',$member)) ||    # template filter
            ('related' == $type && $member == $page)                        # current page filter
           )
        {
#echo '(Pages) Filtered: '.$member.'<br/>';
            // do nothing
        }
        else
        {
            if ('related' == $type)
            {
#echo "adding member $member for cat $cat<br/>";
                $list[$cat][] = $wakka->Link($member);
            }
            else
            {
#echo 'adding '.$member.'<br/>';
                $list[] = $wakka->Link($member);
            }
        }
    }
}

// ----------------- constants and variables ------------------

// constants
$aShow      = array('all','categories','pages','members');
$aType      = array('cols','list','related');
if (!defined('PAT_PAGENAME_CHARS'))     define('PAT_PAGENAME_CHARS','A-Za-z0-9�������');    # NOT within [] so we can use if for a negative class as well

// set defaults
$lCat       = NULL;         # no predefined default
$lShow      = 'all';        # both categories and pages
$lType      = 'cols';       # default display type
$bCatNames  = TRUE;         # only pages starting with 'Category' are considered category pages
$bIncTpl    = FALSE;        # do not show template pages or treat a template as a category
$lCols      = 1;            # one column for columnar layout
$lClass     = '';           # no class
$lHead      = NULL;         # specified heading (may contain place holder for category)

// initializations
$bCatDefined = FALSE;
$bCategoryPage = preg_match('/^Category/',$this->tag);

// User-interface strings
if (!defined('HD_COL'))             define('HD_COL','Members of the %s Category');
if (!defined('HD_LST'))             define('HD_LST','%s');
if (!defined('HD_REL'))             define('HD_REL','Related pages');

if (!defined('COL_MEMBERS_FOUND'))  define('COL_MEMBERS_FOUND','Category %2$s consists of the following %1$d members:');
if (!defined('COL_CATS_FOUND'))     define('COL_CATS_FOUND','The following %1$d categories belong to %2$s:');
if (!defined('COL_PAGES_FOUND'))    define('COL_PAGES_FOUND','The following %1$d pages belong to %2$s:');
if (!defined('HD_LST_MEMBERS'))     define('HD_LST_MEMBERS','Members');
if (!defined('HD_LST_CATS'))        define('HD_LST_CATS','Categories');
if (!defined('HD_LST_PAGES'))       define('HD_LST_PAGES','Pages');
if (!defined('LST_MEMBERS_FOUND'))  define('LST_MEMBERS_FOUND','%d Members:');
if (!defined('LST_CATS_FOUND'))     define('LST_CATS_FOUND','%d Categories:');
if (!defined('LST_PAGES_FOUND'))    define('LST_PAGES_FOUND','%d Pages:');
if (!defined('REL_PAGES_FOUND'))    define('REL_PAGES_FOUND','in %2$s (%1$d):');    # note (required) parameter order!

if (!defined('COL_NONE_FOUND'))     define('COL_NONE_FOUND','No items found for %s.');
if (!defined('COL_NO_CATS_FOUND'))  define('COL_NO_CATS_FOUND','No subcategories found in %s.');
if (!defined('COL_NO_PAGES_FOUND')) define('COL_NO_PAGES_FOUND','No pages found in %s.');
if (!defined('LST_NONE_FOUND'))     define('LST_NONE_FOUND','No items found.');
if (!defined('LST_NO_CATS_FOUND'))  define('LST_NO_CATS_FOUND','No subcategories found.');
if (!defined('LST_NO_PAGES_FOUND')) define('LST_NO_PAGES_FOUND','No pages found.');
if (!defined('REL_NONE_FOUND'))     define('REL_NONE_FOUND','None found.');

// --------------------------- processsing --------------------------



// --------------- get parameters ----------------

if (is_array($vars))
{
    foreach ($vars as $param => $value)
    {
        switch ($param)
        {
            // 1 - context
            case 'cat':
                if ($this->existsPage($value)) $tCat = $value;
                break;
            case 'page':
                if ($this->existsPage($value)) $tPage = $value;
                break;
            // 2 - what
            case 'show':
                if (in_array($value,$aShow)) $tShow = $value;
                break;
            // 3 - display type
            case 'type':
                if (in_array($value,$aType)) $tType = $value;
#
#if (isset($tType)) echo '(valid) input type: '.$tType.'<br/>';
#
                break;
            case 'compact':
                if ($value === (string)(int)$value) $tCompact = (int)$value;
                break;
            // 4 - filters
            case 'catnames':
                if ($value == 0) $tCatNames = FALSE;
#
#if (isset($tCatNames)) echo 'CatNames FALSE<br/>';
#
                break;
            case 'inctpl':
                if ($value == 1) $tIncTpl = TRUE;
#
#if ($value == 1) echo 'include template pages<br/>';
#
                break;
            // 5 - presentation
            case 'cols':
                if ($value === (string)(int)$value) $tCols = abs((int)$value);
                break;
            case 'col':
                if ($value === (string)(int)$value) $tCol = abs((int)$value);
                break;
            case 'class':
                $tClass = trim(strip_tags($value));
                break;
            case 'head':
                $tHead = trim(strip_tags($value));
        }
    }
}

// ------------- process parameters --------------

// filters
if (isset($tCatNames))  $bCatNames = $tCatNames;
#
#if ($bCatNames) echo 'catnames on<br/>'; else echo 'catnames not on: cannot distinguish pages from categories!!<br/>';
#

// determine which category/categories to look at
if (isset($tCat))
{
    $bCatDefined = TRUE;
    $tempCat = ('/' == $tCat) ? 'CategoryCategory' : $tCat;     # '/' = shortcut for top-level category
}
elseif (isset($tPage))                                          # DEPRECATED
{
    $bCatDefined = TRUE;
    $tempCat = ('/' == $tPage) ? 'CategoryCategory' : $tPage;   # '/' = shortcut for top-level category
}
else
{
    $tempCat = $this->tag;                                      # fallback current page for no specified category
}

// derive display type
if (isset($tType))
{
    if ('related' == $tType && (!$bCatNames))
    {
        $lType = 'list';                                        # fallback if category not defined or we have no naming convention
    }
    else
    {
        $lType = $tType;
    }
}
elseif (isset($tCompact))                                       # DEPRECATED
{
    if (0 == $tCompact)
    {
        $lType = 'cols';
    }
    elseif (1 == $tCompact)
    {
        $lType = 'list';
    }
}
//else default 'cols'

// final category (or array of categories) to consider
// verify whether we have a valid category (page)
if ($bCatNames && 'related' != $lType && !preg_match('/^Category/i',$tempCat))
{
    $lCat = 'CategoryCategory';                         # fallback category
}
// find categories to consider for 'related'
elseif ($bCatNames && 'related' == $lType)
{
    $aCats = array();
    if (!$bCatDefined)
    {
#
#$relstart = getmicrotime();
#
        // replace every character not allowed in a page name by a space and build word array from the page
        $aWords = preg_split('/\s/',preg_replace('/[^'.PAT_PAGENAME_CHARS.']/',' ',$this->page['body']));
        // collect category names
        foreach ($aWords as $word)
        {
            // a word is only a category name if 'Category' is followed by something (case-insensitive check)
            if (preg_match('/^Category(.+)$/i',$word)) $aCats[] = $word;
        }
        $aCats = array_unique($aCats);
#

//printf('Page categories found in %.6f seconds<br/>',(getmicrotime() - $relstart));
//echo 'categories for the current page:<pre>';
//print_r($aCats);
//echo '</pre>';

#
    }
    else
    {
        // one category defined: use (only) that IF the specified category occurs on the page!
        if (preg_match('/\b'.$tempCat.'\b/',$this->page['body'])) $aCats[] = $tempCat;
    }
}
else
{
    $lCat = $tempCat;
}

// include templates? may depend on Category selected
if (isset($tIncTpl))
{
    $bIncTpl = $tIncTpl;
}
elseif (preg_match('/^Category(.*)Template$/',$lCat))       # 'template category'
{
    $bIncTpl = TRUE;                                        # do show templates in a 'template category'
}
// else default FALSE (don't show templates)

// derive what to show
if ($bCatNames)                                             # assume naming convention
{
    if ('related' == $lType)                                # overrides 'show' parameter for type 'related'
    {
        $lShow = 'pages';
    }
    elseif (isset($tShow))
    {
        $lShow = $tShow;
    }
    // else default 'all'
}
else                                                        # cannot distinguish between pages and categories!
{
    $lShow = 'members';
}
$bShowMixed = ('members' == $lShow);
$bShowCats  = ('all' == $lShow || 'categories' == $lShow) ? TRUE : FALSE;
$bShowPages = ('all' == $lShow || 'pages' == $lShow) ? TRUE : FALSE;

// --- presentation parameters

// columns
if ('cols' == $lType)
{
    if (isset($tCols))                                      # overrides 'col' parameter
    {
        $lCols = $tCols;
    }
    elseif (isset($tCol))
    {
        $lCols = $tCol;
    }
}
// class
if (isset($tClass) && '' != $tClass) $lClass = $tClass;
// main heading override
if (isset($tHead) && '' != $tHead)
{
    $lHead = $tHead;
}
else
{
    switch ($lType)
    {
        case 'cols':
            $lHead = HD_COL;
            break;
        case 'list':
            $lHead = HD_LST;
            break;
        case 'related':
            $lHead = HD_REL;
            break;
    }
}


// ---------------- gather data ------------------

// get the category content
if ('related' == $lType)
{
    $pagelist = array();
    foreach ($aCats as $cat)
    {
#
#echo '- Looking at category: '.$cat.'<br/>';
#
        $results = $this->getCatMembers($cat);
        if ($results)
        {
            foreach ($results as $cpage)
            {
                $member = $cpage['tag'];
#
#echo 'considering member: '.$member.'<br/>';
#
                buildPageList($member,$pagelist,$cat,$bIncTpl,$lType,$this->tag);
            }
        }
    }
    ksort($pagelist);
}
else
{
#
#echo '- Looking at category: '.$lCat.'<br/>';
#
    $results = $this->getCatMembers($lCat);

    // gather what we show AS content of the requested category
    $memberlist = array();
    $catlist = array();
    $pagelist = array();
    if ($results)
    {
        foreach ($results as $cpage)
        {
            $member = $cpage['tag'];
#
#echo 'considering member: '.$member.'<br/>';
#
            if ($bShowMixed)
            {
                buildMemberList($member,$memberlist,$lCat,$bIncTpl);
            }
            if ($bShowCats)
            {
                buildCatList($member,$catlist,$lCat,$lType);
            }
            if ($bShowPages)
            {
                buildPageList($member,$pagelist,$lCat,$bIncTpl,$lType,$this->tag);
            }
        }
        sort($memberlist);
        sort($catlist);
        sort($pagelist);
    }
}

// ------------------ output ---------------------

// show resulting list(s) of items belonging to selected category/categories
$str ='';
switch ($lType)
{
    case 'cols':
        $attrClass = ('' != $lClass) ? ' class="categorycols '.$lClass.'"' : ' class="categorycols"';
        $head = ($bCatNames) ? sprintf($lHead,preg_replace('/^Category/','',$lCat)) : sprintf($lHead,$lCat);
        // start wrapper
        $str .= '<div'.$attrClass.'>'."\n";
        $str .= '   <h5 id="'.$this->makeId('hn','category members').'">'.$head.'</h5>'."\n";
        // members (undifferentiated)
        if ($bShowMixed)
        {
            $hd   = sprintf(COL_MEMBERS_FOUND,count($memberlist),$lCat);
            $str .= $this->makeMemberCols($memberlist,$lCols,$hd,'category members',sprintf(COL_NONE_FOUND,$lCat));
        }
        // categories
        if ($bShowCats)
        {
            $hd   = sprintf(COL_CATS_FOUND,count($catlist),$lCat);
            $str .= $this->makeMemberCols($catlist,$lCols,$hd,'category members',sprintf(COL_NO_CATS_FOUND,$lCat));
        }
        // pages
        if ($bShowPages)
        {
            $hd   = sprintf(COL_PAGES_FOUND,count($pagelist),$lCat);
            $str .= $this->makeMemberCols($pagelist,$lCols,$hd,'category members',sprintf(COL_NO_PAGES_FOUND,$lCat));
        }
        // end wrapper
        $str .= "</div>\n";
        break;
    case 'list':
        $attrClass = ('' != $lClass) ? ' class="categorylist '.$lClass.'"' : ' class="categorylist"';
        $head = sprintf($lHead,$lCat);
        // start wrapper
        $str .= '<div'.$attrClass.'>'."\n";
        $str .= '   <h5 id="'.$this->makeId('hn','category members').'">'.$head.'</h5>'."\n";
        // members (undifferentiated)
        if ($bShowMixed)
        {
            $hd   = LST_MEMBERS_FOUND;
            $str .= $this->makeMemberList($memberlist,$hd,'category members','memberlist',LST_NONE_FOUND,'menu');
        }
        // categories
        if ($bShowCats)
        {
            $hd   = LST_CATS_FOUND;
            $str .= $this->makeMemberList($catlist,$hd,'category members','catlist',LST_NO_CATS_FOUND,'menu');
        }
        // pages
        if ($bShowPages)
        {
            $hd   = LST_PAGES_FOUND;
            $str .= $this->makeMemberList($pagelist,$hd,'category members','pagelist',LST_NO_PAGES_FOUND,'menu');
        }
        // end wrapper
        $str .= "</div>\n";
        break;
    case 'related':
        $attrClass = ('' != $lClass) ? ' class="categoryrel '.$lClass.'"' : ' class="categoryrel"';
        $head = $lHead;
        // start wrapper
        $str .= '<div'.$attrClass.'>'."\n";
        $str .= '   <h5 id="'.$this->makeId('hn','related pages').'">'.$head.'</h5>'."\n";
        // data lists
        if (count($pagelist) > 0)
        {
            foreach ($pagelist as $cat => $memberlist)
            {
                sort($memberlist);
                $hd = sprintf(REL_PAGES_FOUND,count($memberlist),$cat);
                $str .= $this->makeMemberList($memberlist,$hd,'related pages','rellist',REL_NONE_FOUND,'menu');
            }
        }
        else
        {
            $str .= '   <p>'.REL_NONE_FOUND.'</p>'."\n";
        }
        // end wrapper
        $str .= "</div>\n";
        break;
}

echo $str;
*/

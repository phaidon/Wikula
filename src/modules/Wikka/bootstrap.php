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


// load the system tags defines if exists
//$syslang  = System::getVar('language');
//$wikulang = ModUtil::getVar('Wikula', 'langinstall', $syslang);
//Loader::loadFile('pagetags.php', 'modules/wikula/pnlang/'.$wikulang, false);

// patterns definitions
if (!defined('VALID_PAGENAME_PATTERN')) define ('VALID_PAGENAME_PATTERN', '/^[A-Za-z�������]+[A-Za-z0-9�������]*$/s');

/**#@+
 * Code block pattern.
 */
if (!defined('PATTERN_OPEN_BRACKET')) define('PATTERN_OPEN_BRACKET', '\(');
if (!defined('PATTERN_FORMATTER')) define('PATTERN_FORMATTER', '([^;\)]+)');
if (!defined('PATTERN_LINE_NUMBER')) define('PATTERN_LINE_NUMBER', '(;(\d*?))?');
if (!defined('PATTERN_FILENAME')) define('PATTERN_FILENAME', '(;([^\)\x01-\x1f\*\?\"<>\|]*)([^\)]*))?');
if (!defined('PATTERN_CLOSE_BRACKET')) define('PATTERN_CLOSE_BRACKET', '\)');
if (!defined('PATTERN_CODE')) define('PATTERN_CODE', '(.*)');

/**#@-*/
/**
 * Match heading tags.
 *
 * - $result[0] : the entire node representation, including the closing tag
 * - $result[1] : the nodename (h1, h2, .. , h6)
 * - $result[2] : the heading attribute, ie all the strings after the tagname and before the first ">" character
 * - $result[3] : the content of the heading tag, just like the innerHTML method in DOM.
 * This pattern will match only if the text it is applied to is valid XHTML: it should use lowercase in the tagName,
 * it should not contain the character ">" inside attributes.
 */
if (!defined('PATTERN_MATCH_HEADINGS')) define('PATTERN_MATCH_HEADINGS', '#^<(h[1-6])(.*?)>(.*?)</\\1>$#s');
/**
 * Match id in attributes.
 *
 * - $result[0] : a string like <code>id="h1_id"</code>, starting with the letters id=, and followed by a string
 *   enclosed in either single or double quote. It doesn't match if the term id is not preceded by any whitespace.
 * - $result[1] : The single character used to enclose the string, either a single or a double quote.
 * - $result[2] : The content of the string, ie the value of the id attribute.
 * The RE uses a backref to match both single and double enclosing quotes.
 */
if (!defined('PATTERN_MATCH_ID_ATTRIBUTES')) define('PATTERN_MATCH_ID_ATTRIBUTES', '/(?<=\\s)id=("|\')(.*?)\\1/');

/**
 * To be used in replacing img tags having an alt attribute with the value of the alt attribute, trimmed.
 * - $result[0] : the entire img tag
 * - $result[1] : If the alt attribute exists, this holds the single character used to delimit the alt string.
 * - $result[2] : The content of the alt attribute, after it has been trimmed, if the attribute exists.
 */
if (!defined('PATTERN_REPLACE_IMG_WITH_ALTTEXT')) define('PATTERN_REPLACE_IMG_WITH_ALTTEXT', '/<img[^>]*(?<=\\s)alt=("|\')\s*(.*?)\s*\\1.*?>/');

/**
 * Defines characters that are not valid for an ID.
 * Defined as the negation of a character class comprising the characters that
 * <i>are</i> valid in an ID. All but valid characters will be stripped when deriving
 * an ID froma provided string.
 */
if (!defined('PATTERN_INVALID_ID_CHARS')) define ('PATTERN_INVALID_ID_CHARS', '/[^A-Za-z0-9_:.-\s]/');

/**
 * Match "<a " when it isn't preceded by "</a>"
 */
if (!defined('PATTERN_OPEN_A_ALONE')) define('PATTERN_OPEN_A_ALONE', '(?<!</a>|^)<a ');
/**
 * Match the end of a string when the string doesn't end with </a>
 */
if (!defined('PATTERN_END_OF_STRING_ALONE')) define('PATTERN_END_OF_STRING_ALONE', '(?<!</a>)$');
/**
 * Match "</a>" when it is not followed by an opening link markup (<a )
 */
if (!defined('PATTERN_CLOSE_A_ALONE')) define('PATTERN_CLOSE_A_ALONE', '</a>(?!<a |$)');
/**
 * Match the start of a string when the string doesn't start with "<a "
 */
if (!defined('PATTERN_START_OF_STRING_ALONE')) define('PATTERN_START_OF_STRING_ALONE', '^(?!<a )');

if (!defined('ID_LENGTH')) define('ID_LENGTH', 10); // @@@ maybe make length configurable
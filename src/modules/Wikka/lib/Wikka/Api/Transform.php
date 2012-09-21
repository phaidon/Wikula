<?php
/**
 * Copyright Wikula Team 2011/ Wikka Development Team
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikka
 * @link http://code.zikula.org/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Transform api class.
 */
class Wikka_Api_Transform extends Zikula_AbstractApi 
{

    /**
     * Categories collector
     *
     * @var array
     */
    private $categories = array();
    
    /**
     * Headings collector
     *
     * @var array
     */
    private $headings = array();
    
    /**
     * Codeblock collector
     *
     * @var array
     */
    private $codeblocks = array();
    
    
    /**
     * Forward to Wikka formater
     * 
     * @param array $args Arguments.
     * 
     * @return Wikka formater output
     */
    public function transform($args)
    {   
        PageUtil::addVar('stylesheet', 'modules/'.$this->name.'/style/transform.css');
        return $this->wikka($args);
    }


    /**
     * Wikka formater
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * string $args['text'] Text to wiki-format.
     * string $args['method'] (Optional) Legacy Wikka state.
     * 
     * @param array $args Arguments. 
     * 
     * @return wiki-formatted text
     */
    private function wikka($args)
    {
        global $mapcounter;
        $mapcounter = 1;
        

        $args['text'] = str_replace("\r\n", "\n", $args['text']);

        // We'll see about that later
        $args['method'] = isset($args['method']) ? $args['method'] : FormUtil::getPassedValue('method');
        if (empty($args['method']) || $args['method'] == 'show') {
            $mindmap_pattern = '<map.*?<\/map>|';
        } else {
            $mindmap_pattern = '';
        }
        
        
        $args['text'] = preg_replace_callback(
            "#\n\[\[Category(.*?)\]\]#si",
            array($this, 'categoryCallback'),
            $args['text']
        );
        $args['text'] = preg_replace_callback(
            "#\nCategory([a-zA-Z0-9]*+)#si",
            array($this, 'categoryCallback'),
            $args['text']
        );

        // remove hr
        $length = strlen($args['text']);
        if (substr($args['text'], $length-4) == '----' ) {
            $args['text'] = substr($args['text'], 0, $length-4);
        }
        
        $args['text'] = preg_replace_callback(
            '/'.
            '%%.*?%%|'.                                                   // code
            "\/\*.*?\*\/[\s]*|".                                          // elided content (eliminates trailing ws)
            "``.*?``|".                                                   // elided content (preserves trailing ws)
            '"".*?""|'.                                                   // literal
            $mindmap_pattern.
            '\[\[[^\[]*?\]\]|\(\([^\(]*?\)\)|'.                           // forced link
            '-{4,}|-{3,}|'.                                               // forced linebreak and separator (hr)
            '\b[a-z]+:\/\/\S+|'.                                          // URL
            "\*\*|\'\'|\#\#|\#\%|@@|::c::|\>\>|\<\<|&pound;&pound;|&yen;&yen;|\+\+|__|<|>|\/\/|". // Wiki markup
            '======|=====|====|===|==|'.                                  // headings
            "(^|\n)([\t~]+|[ ]{2,})+(-(?!-)|&|\*(?!\*)|([0-9]+|[a-zA-Z]+)\))?|". // indents and lists
            "\|(?:[^\|])?\|(?:\(.*?\))?(?:\{[^\{\}]*?\})?(?:\n)?|".       // Simple Tables
            "\{\{.*?\}\}|".                                               // action
                    "\b[A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+[:](?![=_])\S*\b|".											// InterWiki link
                    "\b([A-ZÄÖÜ]+[a-zßäöü]+[A-Z0-9ÄÖÜ][A-Za-z0-9ÄÖÜßäöü]*)\b|".								// CamelWords
            '\\&([#a-zA-Z0-9]+;)?|'.                                      // ampersands! Track single ampersands or any htmlentity-like (&...;)
            "\n".                                                         // new line
            '/ms',
             array($this, 'wikka2callback'),
            $args['text']
        );

        // close open tags
        $args['text'] .= $this->wikka2callback('closetags');

        $args['text'] = preg_replace_callback(
            '#('.
            '<h[1-6].*?>.*?</h[1-6]>'.
            // other elements to be treated go here
            ')#ms',

            array($this, 'wikka3callback'),
            $args['text']
        );

        // we're cutting the lasts <br />
        $args['text'] = preg_replace('/<br \/>$/', '', $args['text']);

        if (count($this->categories) > 0) {
            if (count($this->categories) == 1 ) {
                $categories = $this->__('Category');
            } else {
                $categories = $this->__('Categories');
            }
            $categories = '<div class="wikula_categories">'.$categories.': '.implode(', ', $this->categories).'</div>';
        } else {
            $categories = '';
        }
        
        
        
        $indexBox = self::indexBox($args['text']);

        
        return '<div id="wakka">'.$indexBox.$args['text'].$categories.'</div>';
    }
    
    
    /**
     * Add a index box to wiki page
     *
     * @param string $text Wiki text. 
     * 
     * @return wiki-formatted text with index box.
     */
    private function indexBox($text)
    {
        if (!$this->getVar('showIndex', false) ) {
            return '';
        }
        
        $headings = array();
        preg_match_all('/\<h(.*?)\<\/a\>/si', $text, $headings);
        
        
        if (count($headings[1]) == 0 ) {
            return '';
        }
            
        PageUtil::addVar('javascript', 'prototype');
        $indexBox = '<div class="z-center">Contents '.
                    '<a href="#" onclick="$(\'innerIndexBox\').toggle()">['.
                    $this->__('hide').
                    ']</a></div><div id="innerIndexBox"><br />';
        $h = array();
        $h[1] = 1;
        $h[2] = 1;
        $h[3] = 1;
        $h[4] = 1;
        $h[5] = 1;
        $prelevel = 0;

        foreach ($headings[1] as $value) {

            $tmparray1 = explode(' ', $value);
            $tmparray2 = explode('>', $value);
            $level = $tmparray1[0];
            $title = $tmparray2[2];

            if ($level <= $prelevel ) {
                $h[$level]++;

                if ($level < 5 ) {
                    $h[5] = 1;
                }
                if ($level < 4 ) {
                    $h[4] = 1;
                }
                if ($level < 3 ) {
                    $h[3] = 1;
                }
                if ($level < 2 ) {
                    $h[2] = 1;
                }

            }

            if ($level != $prelevel) {
                $number = '';
                $spaces = '';
                for ($i = 1; $i < $level; $i++) {
                    $number .= $h[$i].'.';
                    $spaces .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                }
            }

            $titleNS = str_replace(' ', '_', $title);
            $indexBox .= $spaces.'<a href="#hn_'.$titleNS.'">'.$number.$h[$level].' '.$title.'</a><br />';

            $prelevel = $level;
        }

        
        return '<div class="indexBox">'.$indexBox.'</div></div>'.
               '<div class="z-clearer"></div>';
        
    }
    

    /**
     * Callback transform Wikka function
     *
     * @param string $things Match with the patterns defined.
     * 
     * @return HTML transformation
     */
    private function wikka2callback($things)
    {
        $cr     = "\n";
        $thing  = $things[0];
        $result = '';
        $valid_filename = '';

        static $oldIndentLevel = 0;
        static $indentClosers = array();
        static $curIndentType;
        static $newIndentSpace= array();
        static $br = true;
        static $trigger_bold = 0;
        static $trigger_center = 0;
        static $trigger_colgroup = 0;
        static $trigger_deleted = 0;
        static $trigger_floatl = 0;
        static $trigger_floatr = 0;
        static $trigger_inserted = 0;
        static $trigger_italic = 0;
        static $trigger_keys = 0;
        static $trigger_l = array(-1, 0, 0, 0, 0, 0);
        static $trigger_monospace = 0;
        static $trigger_notes = 0;
        static $trigger_rowgroup = 0;
        static $trigger_strike = 0;
        static $trigger_table = 0;
        static $trigger_underline = 0;
        static $li = 0;
        static $output = '';
        static $invalid = '';

        if (!is_array($things) && $things == 'closetags') {
            $return = '';
            // close inline elements
            if ($trigger_keys % 2) {
                $return .= '</kbd>';
            }
            if ($trigger_italic % 2) {
                $return .= '</em>';
            }
            if ($trigger_monospace % 2) {
                $return .= '</tt>';
            }
            if ($trigger_bold % 2) {
                $return .= '</strong>';
            }
            if ($trigger_strike % 2) {
                $return .= '</span>';
            }
            if ($trigger_notes % 2) {
                $return .= '</span>';
            }
            if ($trigger_inserted % 2) {
                $return .= '</span>';
            }
            if ($trigger_deleted % 2) {
                $return .= '</span>';
            }
            if ($trigger_underline % 2) {
                $return .= '</span>';
            }

            // close headings
            for ($i = 1; $i<=5; $i ++) {
                if ($trigger_l[$i] % 2) {
                    $return .= "</h$i>";
                }
            }

            // close indents
            $c = count($indentClosers);
            for ($i = 0; $i < $c; $i++) {
                $return .= array_pop($indentClosers);
            }

            // close tables
            // TODO check colgroup?
            if (3 < $trigger_table) {
                $return .=  '</caption>';
            } elseif (2 < $trigger_table) {
                $return .=  '</th></tr>';
            } elseif (1 < $trigger_table) {
                $return .=  '</td></tr>';
            }
            if (2 < $trigger_rowgroup) {
                $return .=  '</tbody>';
            } elseif (1 < $trigger_rowgroup) {
                $return .=  '</tfoot>';
            } elseif (0 < $trigger_rowgroup) {
                $return .=  '</thead>';
            }
            if (0 < $trigger_table) {
                $return .=  '</table>';
            }

            // close block elements
            if ($trigger_floatl % 2) {
                $return .= '</div>';
            }
            if ($trigger_floatr % 2) {
                $return .= '</div>';
            }
            if ($trigger_center % 2) {
                $return .= '</div>';
            }

            // reset the static vars
            $oldIndentLevel  = 0;
            $oldIndentLength = 0;
            $indentClosers = $newIndentSpace  = array();
            $trigger_bold = $trigger_center = $trigger_floatl = $trigger_floatr = $trigger_inserted = $trigger_deleted = $trigger_italic = $trigger_keys = $trigger_table = $trigger_rowgroup = $trigger_rowgroup = 0;
            $trigger_monospace = $trigger_notes = $trigger_strike = $trigger_underline = 0;
            $trigger_l = array(-1, 0, 0, 0, 0, 0);

            return $return;

        
        } elseif (preg_match("/^\|\|\n$/", $thing, $matches) && $trigger_table == 1) {
            // Ignore the closing delimiter if there is nothing to close.
            return '';

        
        } elseif (preg_match("/^\|([^\|])?\|(\(.*?\))?(\{.*?\})?(\n)?$/", $thing, $matches)) {
            // Simple tables
            // $matches[1] is element, $matches[2] is attributes, $matches[3] is styles and $matches[4] is linebreak
            for ($i = 1; $i < 5; $i++) {
                if (!isset($matches[$i])) $matches[$i] = '';
            }
            // Set up the variables that will aggregate the html markup
            $close_part = '';
            $open_part  = '';
            $linebreak_after_open = '';
            $selfclose = '';

            // $trigger_table == 0 means no table, 1 means in table but no cell, 2 is in datacell, 3 is in headercell, 4 is in caption.

            // If we have parsed the caption, close it, set trigger = 1 and return.
            if ($trigger_table == 4) {
                $trigger_table = 1;
                return '</caption>'.$cr;
            }

            // If we have parsed a cell - close it, go on to open new.
            if ($trigger_table == 3) {
                $close_part = '</th>';
            } elseif ($trigger_table == 2) {
                $close_part = '</td>';
            } elseif ($trigger_table == 1 || $matches[1] == '!') {
                // If no cell, or we want to open a table; then there is nothing to close
                $close_part = '';
            } else {
                //This is actually opening the table (i.e. nothing at all to close). Go on to open a cell.
                $trigger_table = 1;
                $close_part = '<table class="data">'.$cr;
            }

            // If we are in a cell and there is a linebreak - then it is end of row.
            if ( $trigger_table > 1 && $matches[4] == $cr) {
                $trigger_table = 1;
                return $close_part .= '</tr>'.$cr; //Can return here, it is closed-
            }

            // If we were in a colgroup and there is a linebreak, then it is the end.
            if ($trigger_colgroup == 1 && $matches[4] == $cr) {
                $trigger_colgroup = 0;
                return $close_part .= '</colgroup>'.$cr; //Can return here, it is closed-
            }

            // We want to start a new table, and most likely have attributes to parse.
            // TODO: Need to find out if class="data" should be auto added, and if so - put it in the attribute list to add up.
            if ($matches[1] == '!') {
                $trigger_table = 1;
                $open_part = '<table class="data"';
                $linebreak_after_open = $cr;
            
            } elseif ($matches[1] == '?') {
                // Open a caption.
                $trigger_table = 4;
                $open_part = '<caption';
            
            } elseif ($matches[1] == '#' || $matches[1] == '[' || $matches[1] == ']') {
                //Start a rowgroup.
                //If we're here, we want to close any open rowgroup.
                if (2 < $trigger_rowgroup) {
                    $close_part .= '</tbody>'.$cr;
                } elseif (1 < $trigger_rowgroup) {
                    $close_part .= '</tfoot>'.$cr;
                } elseif (0 < $trigger_rowgroup) {
                    $close_part .= '</thead>'.$cr;
                }

                // Then open the appropriate rowgroup.
                if ($matches[1] == '[' ) {
                    $open_part .= '<thead';
                    $trigger_rowgroup = 1;
                } elseif ($matches[1] == ']' ) {
                    $open_part .= '<tfoot';
                    $trigger_rowgroup = 2;
                } else {
                    $open_part .= '<tbody';
                    $trigger_rowgroup = 3;
                }
                $linebreak_after_open = $cr;

            
            } elseif ($matches[1] == '_') {
                // Here we want to add colgroup.
                // close any open colgroup
                if ($trigger_colgroup == 1) {
                    $close_part .= '</colgroup>'.$cr;
                }

                $trigger_colgroup = 1;
                $open_part .= '<colgroup';

            
            } elseif ($matches[1] == '-') {
                // And col elements
                $open_part .= '<col';
                $selfclose = ' /';
                if ($matches[4]) {
                    $linebreak_after_open = $cr;
                }

            
            } else {
                //Ok, then it is cells.
                $open_part = '';
                // Need a tbody if no other rowgroup open.
                if ($trigger_rowgroup == 0) {
                    $open_part .= '<tbody>'.$cr;
                    $trigger_rowgroup = 3;
                }

                // If no row, open a new one.
                if ($trigger_table == 1) {
                    $open_part .= '<tr>';
                }

                
                if ($matches[1] == '=') {
                    // Header cell.
                    $trigger_table = 3;
                    $open_part .= '<th';
                } else {
                    ////Datacell
                    $trigger_table = 2;
                    $open_part .= '<td';
                }
            }

            // If attributes...
            if (preg_match("/\((.*)\)/", $matches[2], $attribs)) {
                // $hints = array('core' => 'core', 'i18n' => 'i18n');
                $hints = array();
                // allow / disallow different attribute keys. (ie. data/header cell only.
                if ($trigger_table == 2 || $trigger_table == 3) {
                    $hints['cell'] = 'cell';
                } else {
                    $hints['other_table'] = 'other_table';
                }
                $open_part .= parse_attributes($attribs[1], $hints);
            }

            // If styles, just make attribute of it and parse again.
            if (preg_match("/\{(.*)\}/", $matches[3], $attribs)) {
                $attribs = 's:'.$attribs[1];
                $open_part .= parse_attributes($attribs, array());
            }

            // the variable $selfclose is "/" if this is a <col/> element.
            $open_part .= $selfclose.'>';
            return $close_part . $open_part . $linebreak_after_open;

        
        } else if ($trigger_table == 1) {
            // are in table, no cell - but not asked to open new: please close and parse again. ;)
            $close_part = '';
            if (2 < $trigger_rowgroup) {
                $close_part .= '</tbody>'.$cr;
            } elseif (1 < $trigger_rowgroup) {
                $close_part .= '</tfoot>'.$cr;
            } elseif (0 < $trigger_rowgroup) {
                $close_part .= '</thead>'.$cr;
            }

            $close_part .= '</table>'.$cr;

            $trigger_table = $trigger_rowgroup = 0;

            // and remember to parse what we got.
            return $close_part.$this->wikka2callback($things);
        }

        
        if ($thing == '<') {
            // convert HTML thingies
            return '&lt;';

        } else if ($thing == '>') {
            return '&gt;';

        
        } else if ($thing == '<<') {
            // float box left
            return (++$trigger_floatl % 2 ? '<div class="floatl">'.$cr : $cr.'</div>'.$cr);

        
        } else if ($thing == '>>') {
            // float box right
            return (++$trigger_floatr % 2 ? '<div class="floatr">'.$cr : $cr.'</div>'.$cr);

        
        } else if ($thing == '::c::') {
            // clear floated box
            return ('<div class="clear">&nbsp;</div>'.$cr);

        
        } else if ($thing == '#%') {
            // keyboard
            return (++$trigger_keys % 2 ? '<kbd class="keys">' : '</kbd>');

        
        } else if ($thing == '**') {
            // bold
            return (++$trigger_bold % 2 ? '<strong>' : '</strong>');

        
        } else if ($thing == '//') {
            // italic
            return (++$trigger_italic % 2 ? '<em>' : '</em>');

        
        } else if ($thing == '__') {
            // underline
            return (++$trigger_underline % 2 ? '<span class="underline">' : '</span>');

        } else if ($thing == '##') {
            // monospace
            return (++$trigger_monospace % 2 ? '<tt>' : '</tt>');

        
        } else if ($thing == "''") {
            // notes
            return (++$trigger_notes % 2 ? '<span class="notes">' : '</span>');

        
        } else if ($thing == '++') {
            // strikethrough
            return (++$trigger_strike % 2 ? '<span class="strikethrough">' : '</span>');

        
        } else if ($thing == '&pound;&pound;') {
            // additions
            return (++$trigger_inserted % 2 ? '<span class="additions">' : '</span>');

        
        } else if ($thing == '&yen;&yen;') {
            // deletions
            return (++$trigger_deleted % 2 ? '<span class="deletions">' : '</span>');

        
        } else if ($thing == '@@') {
            // center
            return (++$trigger_center % 2 ? '<div class="center">'.$cr : $cr.'</div>'.$cr);

        
        } else if (preg_match('/^([a-z]+:\/\/\S+?)([^[:alnum:]^\/])?$/', $thing, $matches)) {
            // urls
            $url = $matches[1];
            if (preg_match('/^(.*)\.(gif|jpg|jpeg|png)/si', $url)) {
                return DataUtil::formatForDisplayHTML('<img src="'.$url.'" alt="image" />'.$matches[2]);
            } else {
                // Mind Mapping Mod
                if (preg_match('/^(.*)\.(mm)/si', $url)) {
                    return ModUtil::apiFunc($this->name, 'user', 'Action',
                                        array('action' => 'mindmap',
                                            'url'    => $url));
                } else {
                    $matches[2] = (isset($matches[2]) ? $matches[2] : '');
                    $link = $this->Link(
                        array(
                            'tag'    => $url,
                            'method' => '',
                            'text'   => $matches[2]
                        )
                    );
                    return $link.$matches[2];
                }
            }

        
        } else if ($thing == '==') {
            // header level 5
            $br = false;
            return (++$trigger_l[5] % 2 ? '<h5>' : '</h5>'.$cr);

        
        } else if ($thing == '===') {
            // header level 4
            $br = false;
            return (++$trigger_l[4] % 2 ? '<h4>' : '</h4>'.$cr);

        } else if ($thing == '====') {
            // header level 3
            $br = false;
            return (++$trigger_l[3] % 2 ? '<h3>' : '</h3>'.$cr);

        } else if ($thing == '=====') {
            // header level 2
            $br = false;
            return (++$trigger_l[2] % 2 ? '<h2>' : '</h2>'.$cr);

        
        } else if ($thing == '======') {
            // header level 1
            $br = false;
            return (++$trigger_l[1] % 2 ? '<h1>' : '</h1>'.$cr);

        
        } else if ($thing == '---') {
            // forced line breaks
            return '<br />';

        
        } else if (preg_match('/^""(.*)""$/s', $thing, $matches)) {
            // escaped text
            $ddquotes_policy = $this->getVar('double_doublequote_html', 'safe');
            $embedded = $matches[1];

            if ($ddquotes_policy == 'safe' || $ddquotes_policy == 'raw') {
                // get tags with id attributes
                // use backref to match both single and double quotes
                $patTagWithId = '((<[a-z][^>]*)((?<=\\s)id=("|\')(.*?)\\4)(.*?>))';
                // with PREG_SET_ORDER we get an array for each match: easy to use with list()!
                // we do the match case-insensitive so we catch uppercase HTML as well;
                // SafeHTML will treat this but 'raw' may end up with invalid code!
                $tags2 = preg_match_all('/'.$patTagWithId.'/i', $embedded, $matches2, PREG_SET_ORDER);
                // step through code, replacing tags with ids with tags with new ('repaired') ids
                $tmpembedded = $embedded;
                $newembedded = '';
                for ($i=0; $i < $tags2; $i++) {
                    // $attrid not needed, just for clarity
                    list( , $tag, $tagstart, $attrid, $quote, $id, $tagend) = $matches2[$i];
                    // split in two at matched tag
                    $parts = explode($tag, $tmpembedded, 2);
                    // replace if we got a new value
                    if ($id != ($newid = $this->makeId('embed', $id))) {
                        $tag = $tagstart.'id='.$quote.$newid.$quote.$tagend;
                    }
                    // append (replacement) tag to first part
                    $newembedded .= $parts[0].$tag;
                    // after tag: next bit to handle
                    $tmpembedded  = $parts[1];
                }
                // add last part
                $newembedded .= $tmpembedded;
            }

            switch ($ddquotes_policy)
            {
                case 'safe':
                    return DataUtil::formatForDisplayHTML($newembedded);
                case 'raw':
                    return $newembedded; // may still be invalid code - 'raw' will not be corrected!
                default:
                    return $this->htmlspecialchars_ent(array('text' => $embedded)); // display only
            }


        
        } elseif (preg_match("/^\/\*(.*?)\*\/[\s]*$/s", $thing, $matches)) {
            // Elided content (eliminates trailing ws)
            return null;

        
        } elseif (preg_match("/``(.*?)``/s", $thing, $matches)) {
            // Elided content (preserves trailing ws)
            return null;

        } else if (preg_match('/^%%(.*?)%%$/s', $thing, $matches)) {
            // code text
            
            /*
                Note: This routine is rewritten such that (new) language formatters
                will automatically be found, whether they are GeSHi language config files
                or "internal" Wikka formatters.
                Path to GeSHi language files and Wikka formatters MUST be defined in config.
                For line numbering (GeSHi only) a starting line can be specified after the language
                code, separated by a ; e.g., %%(php;27)....%%.
                Specifying >= 1 turns on line numbering if this is enabled in the configuration.
            */
            
            $code = $matches[1];
            // if configuration path isn't set, make sure we'll get an invalid path so we
            // don't match anything in the home directory
            // check if a language (and starting line) has been specified
            $language = '';
            if (preg_match('/^'.PATTERN_OPEN_BRACKET.PATTERN_FORMATTER.PATTERN_LINE_NUMBER.PATTERN_FILENAME.PATTERN_CLOSE_BRACKET.PATTERN_CODE.'$/s', $code, $matches)) {
                $language = isset($matches[1]) ? $matches[1] : null;
                $start    = isset($matches[3]) ? $matches[3] : null;
                $filename = isset($matches[5]) ? $matches[5] : null;
                $invalid  = isset($matches[6]) ? $matches[6] : null;
                $code     = isset($matches[7]) ? $matches[7] : null;
            }
            // get rid of newlines at start and end (and preceding/following whitespace)
            // Note: unlike trim(), this preserves any tabs at the start of the first "real" line
            $code = preg_replace('/^\s*\n+|\n+\s*$/', '', $code);


            // check if specified filename is valid and generate code block header
            if (isset($filename) && strlen($filename) > 0 && strlen($invalid) == 0) {
                // TODO: use central regex library for filename validation
                $valid_filename = $filename;
                // create code block header
                $output .= '<div class="code_header">';
                // display filename and start line, if specified
                $output .= $filename;
                if (strlen($start) > 0) {
                    $output .= ' (line '.$start.')';
                }
                $output .= '</div>'.$cr;
            }
            // use GeSHi for hilighting
            $output = $this->highlight(array(
                        'sourcecode' => $code,
                        'language'   => $language,
                      ));

            return $output;

       
        } elseif (preg_match('/^\[\[(\S*)(\s+(.+))?\]\]$/s', $thing, $matches) || preg_match('/^\(\((\S*)(\s+(.+))?\)\)$/s', $thing, $matches)) {
            // recognize forced links across lines
            // @@@ regex accepts NO non-whitespace before whitespace, surely not correct? [[  something]]
            // forced links
            // \S : any character that is not a whitespace character
            // \s : any whitespace character
            // $matches[1] = url, $matches[3] = text
            // TODO: debug if needed
            if (isset($matches[1]) && !empty($matches[1])) {
                $result = '';
                $url = $matches[1];
                $text = isset($matches[3]) ? $matches[3] : $url;
                $link = $this->Link(
                    array(
                        'tag'  => $url,
                        'text' => $text
                    )
                );
                return $result.$link;
            } else {
                return '';
            }

        } elseif (preg_match("/(^|\n)([\t~]+|[ ]{2,})+(-(?!-)|&|\*(?!\*)|([0-9]+|[a-zA-Z]+)\))?(\n|$)/s", $thing, $matches)) {
            // indented text
            // find out which indent type we want
            $newIndentType  = $matches[3];
            $newIndentLevel = (strpos($matches[2], ' ') === false) ? strlen($matches[2]) : strlen($matches[2])/2;

            // close indent or list element
            if ($li == 2 && $curIndentType != '.') $result .= '</li>';
            if ($li == 2) $result .= ($br ? '<br />'.$cr : $cr);
            $li = 0;

            // we definitely want no line break in this one.
            $br = false;

            if (empty($newIndentType)) {
                $newIndentType = '.';
                $li = 1;
                $br = true;
            } else {
                if (preg_match('`[0-9]`', $newIndentType[0])) { 
                    $newIndentType = '1';
                } elseif (preg_match('`[IVX]`', $newIndentType[0])) {
                    $newIndentType = 'I';
                } elseif (preg_match('`[ivx]`', $newIndentType[0])) {
                    $newIndentType = 'i';
                } elseif (preg_match('`[A-Z]`', $newIndentType[0])) {
                    $newIndentType = 'A';
                } elseif (preg_match('`[a-z]`', $newIndentType[0])) {
                    $newIndentType = 'a';
                }

                $li = 1;
            }

            if ($newIndentLevel < $oldIndentLevel) {
                for (; $newIndentLevel < $oldIndentLevel; $oldIndentLevel--) {
                    $curIndentType = array_pop($indentClosers);
                    if ($oldIndentLevel > 1) {
                        $result .= str_repeat("\t", $oldIndentLevel -1);
                    }
                    if ($curIndentType == '.') {
                        $result .= '</div>';
                    } elseif ($curIndentType == '-' || $curIndentType == '&' || $curIndentType == '*') {
                        $result .= '</ul>';
                    } else {
                        $result .= '</ol>';
                    }
                    $result .= $cr;
                }
            }

            if ($oldIndentLevel == $newIndentLevel) {
                $curIndentType = array_pop($indentClosers);
                if ($newIndentType != $curIndentType) {
                    if ($oldIndentLevel > 1) {
                        $result .= str_repeat("\t", $oldIndentLevel -1);
                    }
                    if ($curIndentType == '.') {
                        $result .= '</div>';
                    } elseif ($curIndentType == '-' || $curIndentType == '&' || $curIndentType == '*') {
                        $result .= '</ul>';
                    } else {
                        $result .= '</ol>';
                    }
                    $oldIndentLevel = $newIndentLevel - 1;
                    $result .= $cr;

                } else {
                    array_push($indentClosers, $curIndentType);
                }
            }

            if ($newIndentLevel > $oldIndentLevel) {
                for (; $newIndentLevel > $oldIndentLevel; $oldIndentLevel++) {
                    $result .= str_repeat("\t", $oldIndentLevel);
                    if ($newIndentType == '.') {
                        $result .= '<div class="indent">';
                    } else if ($newIndentType == '-' || $newIndentType == '&' || $newIndentType == '*') {
                        $result .= '<ul';
                        if ($newIndentType == '&') {
                            $result .= ' class="thread"';
                        }
                        $result .= '>';
                    } else {
                        $result .= '<ol type="'.$newIndentType.'">';
                    }
                    $result .= $cr;
                    array_push($indentClosers, $newIndentType);
                }
            }

            $oldIndentLevel = $newIndentLevel;

            $result .= str_repeat("\t", $oldIndentLevel);
            if ($li == 1) {
                if ($newIndentType != '.') {
                    $result .= '<li>';
                }
                $li = 2;
            }

            $curIndentType = $newIndentType;
            return $result;

        
        } elseif ($thing == $cr) {
            // new lines
            // close lines in indents and list elements
            if ($li == 2) {
                if ($curIndentType != '.') {
                    $result .= '</li>';
                } else {
                    $result .= '<br/>';
                }
                $result .= $cr;
                $li = 0;
            }
            // if we got here, there was no tab in the next line; this means that we can close all open indents and lists.
            for (; 0 < $oldIndentLevel; $oldIndentLevel--) {
                $curIndentType = array_pop($indentClosers);
                if ($oldIndentLevel > 1) {
                    $result .= str_repeat("\t", $oldIndentLevel-1);
                }
                if ($curIndentType == '.') {
                    $result .= '</div>';
                } elseif ($curIndentType == '-' || $curIndentType == '&' || $curIndentType == '*') {
                    $result .= '</ul>';
                } else {
                    $result .= '</ol>';
                }
                $result .= $cr;
                $br = false;
            }
            $oldIndentLevel = 0;

            $result .= ($br ? '<br />'.$cr : $cr);
            $br = true;

            return $result;

        
        } else if (preg_match('/^\{\{(.*?)\}\}$/s', $thing, $matches)) {
            // Actions
            if (isset($matches[1]) && !empty($matches[1])) {

                return $this->Action(
                    array('action' => $matches[1])
                );
            } else {
                return '{{}}';
            }

        
        } else if (preg_match("/^[A-Z���][A-Za-z�������]+[:]\S*$/s", $thing)) {
            // InterWiki links!
            
            return $this->Link(
                array('tag' => $thing)
            );

        
        } else if (preg_match("/^\!?[A-Z0-9���]+[a-z����]+[A-Z0-9���][A-Za-z0-9�������]*$/s", $thing)) {
            // CamelWords unparsed
            if ($thing[0] == '!') {
                return DataUtil::formatForDisplay(substr($thing, 1));
            } else {
                return $this->Link(
                    array('tag' => $thing)
                );
            }

        
        } elseif (preg_match('/^[A-Z���]+[a-z����]+[A-Z0-9���][A-Za-z0-9�������]*$/s', $thing)) {
            // wiki links!
            return $this->Link(
                array('tag'  => $thing)
            );

        
        } else if (preg_match('/-{4,}/', $thing, $matches)) {
            // separators
            $br = false;

            return '<hr />'.$cr;

        
        } else if (preg_match('/^<map.*<\/map>$/s', $thing)) {
            // Removing this until it's been worked out
            // mind map xml
            $maptemp = $mapcounter;
            $mapcounter++;
            //return System::getBaseUrl();
            //$mapurl = ModUtil::apiFunc('wikula', 'user', 'mindmap');
            //return ModUtil::apiFunc('wikula', 'user', 'Action', array('action' => 'mindmap', 'url' => 'index.php?module=wikula&ampfunc=mindmap&amp;tag='.$tag.'&amp;mapcounter='.$maptemp));
            //return ModUtil::apiFunc('wikula', 'user', 'Action', array('action' => 'mindmap', 'url' => $mapurl));
            //SessionUtil::setVar('wikula_map', base64_encode($thing));
            //return 'tada !';
            //return ModUtil::apiFunc('wikula', 'user', 'Action', array('action' => 'mindmap', 'url' => ModUtil::url('wikula', 'user', 'mindmap')));
            return 'Instant Map coming soon!';

        } elseif ($thing[0] == '&') {
            return $this->htmlspecialchars_ent(array('text' => $thing));
        }

        // if we reach this point, it must have been an accident.
        return $thing;
    }


    /**
     * htmlspecialchars to entities utility function
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * string $args['text'] text to process..
     * 
     * @param array $args Arguments. 
     * 
     * @return formatted text
     */
    private function htmlspecialchars_ent($args)
    {
        if (!isset($args['text']) || empty($args['text'])) {
            return '';
        }

        // Fixing for now the other args
        $quote_style = ENT_COMPAT;
        $charset     = 'UTF-8';

        // define patterns
        $alpha      = '[a-z]+';               // character entity reference
        $numdec     = '#[0-9]+';              // numeric character reference (decimal)
        $numhex     = '#x[0-9a-f]+';          // numeric character reference (hexadecimal)
        $terminator = ';|(?=($|[\n<]|&lt;))'; // semicolon; or end-of-string, newline or tag

        $entitystring   = $alpha.'|'.$numdec.'|'.$numhex;
        $escaped_entity = '&amp;('.$entitystring.')('.$terminator.')';

        // execute PHP built-in function, passing on optional parameters
        $output = htmlspecialchars($args['text'], $quote_style, $charset);

        // "repair" escaped entities
        // modifiers: s = across lines, i = case-insensitive
        $output = preg_replace('/'.$escaped_entity.'/si', '&$1;', $output);

        // return output
        return $output;
    }
    
    
    
    /**
     * "Afterburner" formatting: extra handling of already-generated XHTML code.
     *
     * Ensure every heading has an id, either specified or generated. (May be
     * extended to generate section TOC data.)
     * If an id is already specified, that is used without any modification.
     * If no id is specified, it is generated on the basis of the heading context:
     * - any image tag is replaced by its alt text (if specified)
     * - all tags are stripped
     * - all characters that are not valid in an ID are stripped (except whitespace)
     * - the resulting string is then used by makedId() to generate an id out of it
     *
     * @param array $things Required: matches of the regex in the preg_replace_callback.
     * 
     * @return string heading with an id attribute
     */
    private function wikka3callback($things)
    {
        $thing = $things[1];

        // heading
        $matches = array();
        if (preg_match(PATTERN_MATCH_HEADINGS, $thing, $matches)) {
            list($h_element, $h_tagname, $h_attribs, $h_heading) = $matches;

            if (preg_match(PATTERN_MATCH_ID_ATTRIBUTES, $h_attribs)) {
                // existing id attribute: nothing to do (assume already treated as embedded code)
                // @@@ we *may* want to gather ids and heading text for a TOC here ...
                // heading text should then get partly the same treatment as when we're creating ids:
                // at least replace images and strip tags - we can leave entities etc. alone - so we end up with
                // plain text-only
                // do this if we have a condition set to generate a TOC
                return $h_element;

            } else {
                // no id: we'll have to create one
                $headingtext = $this->CleanTextNode($h_heading);
                // now create id based on resulting heading text
                $h_id = $this->makeId(array('group' => 'hn', 'id' => $headingtext));

                // The text of a heading is now becoming a link to this heading, allowing an easy way to copy link to clipboard.
                // For this, we take the textNode child of a heading, and if it is not enclosed in <a...></a>, we enclose it in
                // $opening_anchor and $closing_anchor.
                $opening_anchor = '<a class="heading" href="#'.$h_id.'">';
                $closing_anchor = '</a>';
                $h_heading = preg_replace('@('.PATTERN_OPEN_A_ALONE. '|'.PATTERN_END_OF_STRING_ALONE.  ')@', $closing_anchor.'\\0', $h_heading);
                $h_heading = preg_replace('@('.PATTERN_CLOSE_A_ALONE.'|'.PATTERN_START_OF_STRING_ALONE.')@', '\\0'.$opening_anchor, $h_heading);

                // rebuild element, adding id
                return '<'.$h_tagname.$h_attribs.' id="'.$h_id.'">'.$h_heading.'</'.$h_tagname.'>';
            }
        }
        // other elements to be treated go here (tables, images, code sections...)
    }
    
    /**
     * Category callback
     *
     * @param string $things Things.
     * 
     * @return void
     */
    private function categoryCallback($things)
    {
        $things = explode(' ', $things[1]);
        $category = $things[0];
        $title  = str_replace('_', ' ', $category);
        
        $url = ModUtil::url(
            'Wikula',
            'user',
            'category',
            array('category' => $category)
        );
        $this->categories[] = '<a href="'.$url.'">'.$title.'</a>';
    }
    
    
    /**
     * Clean text node
     *
     * @param string  $textvalue                Text value.
     * @param string  $pattern_prohibited_chars Pattern prohibited chars.
     * @param boolean $decode_html_entities     Decode html entities.
     * 
     * @return string
     */
    private function CleanTextNode($textvalue, $pattern_prohibited_chars = PATTERN_INVALID_ID_CHARS, $decode_html_entities = true)
    {
        $textvalue = trim($textvalue);
        // First find and replace any image having an alt attribute with its (trimmed) alt text
        // Image tags missing an alt attribute are not replaced.
        $textvalue = preg_replace(PATTERN_REPLACE_IMG_WITH_ALTTEXT, '\\2', $textvalue);
        // @@@ JW/2005-05-27 now first replace linebreaks <br/> and other whitespace with single spaces!!
        // Remove all other tags, including img tags that missed an alt attribute
        $textvalue = strip_tags($textvalue);
        // @@@ this all-text result is usable for a TOC!!!
        // Use this if we have a condition set to generate a TOC
        // END -- nodeToTextOnly

        if ($decode_html_entities) {
            if (function_exists('html_entity_decode')) {
                // replace entities that can be interpreted
                // use default charset ISO-8859-1 because other chars won't be valid for an ID anyway
                $textvalue = html_entity_decode($textvalue, ENT_NOQUOTES);
            }
            // remove any remaining entities (so we don't end up with strange words and numbers in the ID text)
            $textvalue = preg_replace('/&[#]?.+?;/','',$textvalue);
        }

        // finally remove non-ID characters (except whitespace which is handled by makeId())
        if ($pattern_prohibited_chars) {
            $textvalue = preg_replace($pattern_prohibited_chars, '', $textvalue);
        }

        return $textvalue;
    }

    
    /**
     * Build an element ID
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * string $args['group'] Group of the id to build.
     * 
     * @param array $args Arguments.
     * 
     * @return final id
     */
    private function makeId($args)
    {
        if (!isset($args['group'])) {
            return LogUtil::registerArgsError();
        }

        $group = $args['group'];
        $id    = (isset($args['id'])) ? $args['id'] : '';
        unset($args);

        // initializations
        static $aSeq = array(); // group sequences
        static $aIds = array(); // used ids

        // preparation for group
        // make sure group starts with a letter
        if (!preg_match('/^[A-Z-a-z]/',$group)) {
            $group = 'g'.$group;
        }

        if (!isset($aSeq[$group])) {
            $aSeq[$group] = 0;
        }

        if (!isset($aIds[$group])) {
            $aIds[$group] = array();
        }

        if ('embed' != $group) {
            // replace any whitespace sequence in $id with a single underscore
            $id = preg_replace('/\s+/','_',trim($id));
        }

        // validation (full for 'embed', characters only for other groups since we'll add a prefix)
        if ('embed' == $group) {
            // ref: http://www.w3.org/TR/html4/types.html#type-id
            $validId = preg_match('/^[A-Za-z][A-Za-z0-9_:.-]*$/',$id);
        } else {
            $validId = preg_match('/^[A-Za-z0-9_:.-]*$/',$id);
        }

        // build or generate id
        // ignore specified id if it is invalid or exists already
        if ('' == $id || !$validId || in_array($id, $aIds)) {
            // use group and id as basis for generated id
            $id = substr(md5($group.$id), 0, ID_LENGTH);
        }

        // add group prefix (unless embedded HTML)
        $idOut = ('embed' == $group) ? $id : $group.'_'.$id;

        if (in_array($id, $aIds[$group])) {
            // add suffiX to make ID unique
            $idOut .= '_'.++$aSeq[$group];
        }

        // result
        // keep track of both specified and generated ids (without suffix)
        $aIds[$group][] = $id;

        return $idOut;
    }


    /**
     * Build a wiki link code
     * 
     * @param array $args Arguments.
     * 
     * @todo needs rework
     * @todo can we index all the Links and check if exists in the DB once?
     * 
     * @return string
     */
    private function Link($args)
    {
        if (!isset($args['tag'])) {
            return false;
        }

        if (!isset($args['text']) || empty($args['text'])) {
            // No text, we fill the page with at least its tag
            $args['text'] = $args['tag'];
        }
        if (!isset($args['title'])) {
            // No text, we fill the page with at least its tag
            $args['title'] = $args['tag'];
        }

        $matches = array();
        // is this an interwiki link?
        if (preg_match('/^([A-Z][A-Z,a-z]+)[:]([A-Z,a-z,0-9]*)$/s', $args['tag'], $matches)) {

            $link = $this->GetInterWikiUrl(
                array(
                    'name' => $matches[1],
                    'tag'  => isset($matches[2]) ? $matches[2] : ''
                )
            );

            $textlink = (isset($matches[2]) && !empty($matches[2])) ? $matches[2] : $matches[1];

            return '<a class="ext" href="'.$link.'" title="'.$matches[1].' - '.$matches[2].'">'.$textlink.'</a><span class="exttail">&#8734;</span>';

        } else if (preg_match('/[^[:alnum:]]/', $args['tag'])) {

            // is this a full link? i.e., does it contain non alpha-numeric characters?
            // Note : [:alnum:] is equivalent [0-9A-Za-z]
            //        [^[:alnum:]] means : some caracters other than [0-9A-Za-z]
            // For example : "www.address.com", "mailto:address@domain.com", "http://www.address.com"

            // check for email addresses
            if (preg_match('/^.+\@.+$/', $args['tag'])) {
                // Building spam safe email link and text
                if ($args['text'] == $args['tag']) {
                    $args['text'] = htmlspecialchars(str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $args['text']));
                }
                $mailto = '&#109;&#97;&#105;&#108;&#116;&#111;&#58;';
                $address = htmlspecialchars($args['tag']);
                $address_encode = '';
                for ($x=0; $x < strlen($address); $x++) {
                    if (preg_match('!\w!',$address[$x])) {
                        $address_encode .= '%' . bin2hex($address[$x]);
                    } else {
                        $address_encode .= $address[$x];
                    }
                }
                $args['tag'] = $mailto . $address_encode;

            } else if (!preg_match('/:\/\//', $args['tag'])) {
                // check for protocol-less URLs
                $args['tag'] = 'http://'.$args['tag'];  // Very important for xss (avoid javascript:() hacking)
            }

            if ($args['text'] != $args['tag'] && preg_match('/.(gif|jpeg|png|jpg)$/i', $args['tag'])) {
                return '<img src="'.DataUtil::formatForDisplay($args['tag']).'" alt="'.DataUtil::formatForDisplay($args['text']).'" />';
            }

        } else {
            // it's a Wiki link!
            $pageid = ModUtil::apiFunc('Wikula', 'user', 'PageExists', $args['tag']);

            $linktable = SessionUtil::getVar('linktable');
            if (is_array(unserialize($linktable))) {
                $linktable = unserialize($linktable);
            }
            $linktable[] = $args['tag']; //$args['page']['tag'];
            SessionUtil::setVar('linktable', serialize($linktable));

            if (!empty($pageid)) {
                $text = DataUtil::formatForDisplay($args['text']);
                $url  = ModUtil::url(
                    'Wikula',
                    'user',
                    'main',
                    array( 'tag' => DataUtil::formatForDisplay(urlencode($args['tag'])))
                );
                return '<a href="'.$url.'" title="'.$text.'">'.$text.'</a>';
            } else {
                $text = DataUtil::formatForDisplay($args['text']);
                $url  = ModUtil::url(
                    'Wikula',
                    'user',
                    'edit',
                    array('tag' => urlencode($args['tag']))
                );
                return '<span class="missingpage">'.$text.'</span><a href="'.$url.'" title="'.DataUtil::formatForDisplay($args['tag']).'">?</a>';
            }
        }

        // Non Wiki external link ?
        $external_link_tail = '<span class="exttail">&#8734;</span>';
        return !empty($args['tag']) ? '<a title="'.$args['text'].'" href="'.$args['tag'].'">'.$args['text'].'</a>'.$external_link_tail : $args['text']; //// ?????
    }
    
    
    /**
     * Get inter wiki url
     * 
     * @param array $args Arguments.
     * 
     * @return string
     */
    private function GetInterWikiUrl($args)
    {
        extract($args);
        unset($args);

        if (!isset($name) || !isset($tag)) {
            return LogUtil::registerArgsError();
        }

        $interwiki = ModUtil::apiFunc('wikula', 'user', 'ReadInterWikiConfig');

        if (!$interwiki || !is_array($interwiki)) {
            return 'http://'.$tag;
        }

        if (isset($interwiki[strtoupper($name)])) {
            return $interwiki[strtoupper($name)].$tag;
        }

        return 'http://'.$tag; //avoid xss by putting http:// in front of JavaScript:()

    }
    
    /**
     * Show a action
     * 
     * @param array $args Arguments.
     * 
     * @return string
     */
    private function Action($args)
    {
        if (!isset($args['action'])) {
            return LogUtil::registerError($this->__('Action argument missing!'));
        }

        $action = trim($args['action']);
        unset($args['action']);

        $vars   = array();
        // only search for parameters if there is a space
        if (strpos($action, ' ') !== false) {
            // treat everything after the first whitespace as parameter
            preg_match('/^([A-Za-z0-9]*)\s+(.*)$/', $action, $matches);

            // extract $action and $vars_temp ("raw" attributes)
            $action    = isset($matches[1]) ? $matches[1] : '';
            $vars_temp = isset($matches[2]) ? $matches[2] : '';

            if (!empty($action)) {
                // match all attributes (key and value)
                preg_match_all('/([A-Za-z0-9]*)="(.*)"/U', $vars_temp, $matches);

                // prepare an array for extract() to work with (in $this->IncludeBuffered())
                if (is_array($matches)) {
                    for ($a = 0; $a < count($matches[0]); $a++) {
                        $vars[$matches[1][$a]] = $matches[2][$a];
                    }
                }
                //$vars['wikka_vars'] = trim($vars_temp); // <<< add the buffered parameter-string to the array
            } else {
                return '<span class="error"><em>'.__f('Unknown action %s; the action name must not contain special characters', DataUtil::formatForDisplay($action)).'.</em></span>'; // <<< the pattern ([A-Za-z0-9])\s+ didn't match!
            }
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $action)) {
            return '<span class="error"><em>'.__f('Unknown action %s; the action name must not contain special characters', DataUtil::formatForDisplay($action)).'.</em></span>';
        }

        $vars = array_merge($args, $vars);

        // image
        if ($action == 'image') {
            if (empty($vars['url'])) {
                return '';
            }
            $params = ' ';
            if (isset($vars['title'])) {
                $params .= 'title="'.$vars['title'].'" ';
            }
            if (isset($vars['alt'])) {
                $params .= 'alt="'.$vars['alt'].'" ';
            }

            return '<img src="'.$vars['url'].'"'.$params.'>';
        }


        // color
        if ($action == 'color') {
            if (empty($vars['text'])) {
                return '';
            }
            $style = '';
            if (!empty($vars['c'])) {
                $style .= 'color:'.$vars['c'].';';
            } else if (!empty($vars['hex'])) {
                $style .= 'color:'.$vars['hex'].';';
            } else {
                if (!empty($vars['fg'])) {
                    $style .= 'color:'.$vars['fg'].';';
                }
                if (!empty($vars['bg'])) {
                    $style .= 'background-color:'.$vars['bg'].';';
                }
            }

            return '<span style="'.$style.'">'.$vars['text'].'</span>';
        }


        // table
        if ($action == 'table') {
            if (empty($vars['columns']) || empty($vars['cells'])) {
                return '';
            }
            if (empty($vars['cellpadding'])) {
                $vars['cellpadding'] = 3;
            }
            if (empty($vars['cellspacing'])) {
                $vars['cellspacing'] = 3;
            }

            $cells = explode(';',$vars['cells']);
            $rows = count($cells)/$vars['columns'];

            $table  = '<table cellpadding='.$vars['cellpadding'].' cellspacing='. $vars['cellspacing'].'>';
            $i=0;
            for ($j = 0; $j < $rows; $j++) {
                $table .= '<tr>';
                for ($k = 0; $k < $vars['columns']; $k++) {
                    $table .= '<td>'.str_replace('###', '', $cells[$i]).'</td>';
                    $i++;
                }
                $table .= '</tr>';
            }
            $table .= '</table>';
            return $table;


        }


        // return the Action result
        return ModUtil::apiFunc('Wikula', 'SpecialPage', strtolower($action), $vars);
    }
    
    /**
     * Higlight code syntax
     * 
     * @param array $args Arguments.
     * 
     * @return string
     */
    public function highlight($args)
    {
        if (empty($args['language'])) {
            $args['language'] = 'php';
        }
        if (empty($args['sourcecode'])) {
            return '';
        }
        $highlighter = $this->getVar('syntaxHighlighter');
           
        switch ($highlighter) {
            case 'geshi':
                include_once('modules/Wikka/lib/vendor/geshi/geshi.php');                        
                $geshi = new GeSHi($args['sourcecode'], $args['language']);
                $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
                $geshi->set_header_type(GESHI_HEADER_PRE);
                $output = $geshi->parse_code(); 
                break;
            case 'prettify':
                $path = 'modules/Wikka/lib/vendor/prettify/';
                PageUtil::addVar('javascript', $path.'prettify.js');
                PageUtil::addVar('stylesheet', $path.'prettify.css');
                PageUtil::addVar('header', '<script type="text/javascript">Event.observe(window, \'load\', prettyPrint);</script>');
                $output = str_replace("\n", '<br />', $args['sourcecode']);
                $output = '<code class="prettyprint linenums:1">'.$output.'</code>';
                break;
            case 'syntaxhighlighter':
                $path = 'modules/Wikka/lib/vendor/syntaxhighlighter/';
                PageUtil::addVar('javascript', $path.'scripts/shCore.js');
                PageUtil::addVar('javascript', $path.'scripts/shBrushJScript.js');
                PageUtil::addVar('stylesheet', $path.'styles/shCoreDefault.css');
                PageUtil::addVar('header', '<script type="text/javascript">SyntaxHighlighter.all()</script>');                
                $output = '<pre class="brush: js">'.$args['sourcecode'].'</pre>';
                break;
            default:
                // no language defined or no formatter found: make default code block;
                $cr = "\n";
                $output = '<div class="code">'.$cr;
                $output .= '<pre>'.htmlspecialchars($args['sourcecode'], ENT_QUOTES).'</pre>'.$cr;
                $output .= '</div>'.$cr;
                break;
        }
        
        return $output;
    } 

    
}
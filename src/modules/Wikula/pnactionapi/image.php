<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: image.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Shows an image
 *
 * @author Mateo Tibaquirá
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['url'] URL of image to be embedded
 * @param string $args['link'] (optional) target link for image. Supports URL, WikiName links, InterWiki links etc.
 * @param string $args['title'] (optional) title text displayed when mouse hovers above image
 * @param string $args['alt'] (optional) an alt text. If the title is not defined is that one too
 * @param string $args['class'] (optional) a CSS class for the image
 * @return unknown
 */
function wikula_actionapi_image($args)
{
    // reset the output
    $output = '';

    // reset the working vars
    $url   = '';
    $title = '';
    $class = '';
    $alt   = '';
    $style = '';

    // parse the input arguments
    if (!empty($args) && is_array($args)) {
        foreach ($args as $param => $value) {
            switch ($param) {
                case 'src':
                    if (!empty($value)) {
                        $url =  $value;
                    }
                    break;
                case 'url':
                    if (!empty($value)) {
                        $url =  $value;
                    }
                    // check if it's absolute, if not, attach the baseurl
                    if (!preg_match('/^http/i', $url)) {
                        $url = pnGetBaseURL().$url;
                    }
                    break;
                case 'title':
                    $title = ' title="'.pnModAPIFunc('wikula', 'user', 'htmlspecialchars_ent', array('text' => $value)).'"';
                    break;
                case 'class':
                    $class = ' class="'.pnModAPIFunc('wikula', 'user', 'htmlspecialchars_ent', array('text' => $value)).'"';
                    break;
                case 'alt':
                    $alt = pnModAPIFunc('wikula', 'user', 'htmlspecialchars_ent', array('text' => $value));
                    if (empty($title)) {
                        $title = $alt;
                    }
                case 'style':
                    $style = ' style="'.pnModAPIFunc('wikula', 'user', 'htmlspecialchars_ent', array('text' => $value)).'"';
                    break;
            }
        }
        $output = '<img'.$class.$style.$title.' src="'.$url.'" alt="'.$alt.'" />';
    }

    // of there's an output image and a link
    if (isset($args['link']) && !empty($output)) {
        // Is it only a WikiName?
        if (preg_match('/^[A-ZÄÖÜ]+[a-zßäöü]+[A-Z0-9ÄÖÜ][A-Za-z0-9ÄÖÜßäöü]*$/s', $args['link'])) {
            $args['link'] = pnModUrl('wikula', 'user', 'main',
                                     array('tag' => DataUtil::formatForDisplay($args['link'])));
        }
        $output = '<a href="'.$args['link'].'" title="'.$title.'">'.$output.'</a>';
    }

    return $output;
}

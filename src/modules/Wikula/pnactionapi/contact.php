<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: contact.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print a spam-safe mailto: link to the administrator's email address.
 * plain mailto links are a common source of spam.
 *
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_contact($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    $show = (isset($args['show']) && !empty($args['show'])) ? true : false;

    $email  = htmlspecialchars(pnConfigGetVar('adminmail'));
    $mailto = '&#109;&#97;&#105;&#108;&#116;&#111;&#58;';

    $email_encode = '';
    for ($x = 0; $x < strlen($email); $x++) {
        if (preg_match('!\w!',$email[$x])) {
            $email_encode .= '%' . bin2hex($email[$x]);
        } else {
            $email_encode .= $email[$x];
        }
    }

    $noscript = htmlspecialchars(str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $email));

    $email = '<a href="'.$mailto.$email_encode.'" title="'.__('Send us your feedback', $dom).'">'.__('Contact', $dom).''.(($show <> false) ? ': '.DataUtil::formatForDisplay($noscript) : '').'</a>';

    return $email;
}

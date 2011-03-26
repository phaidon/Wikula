<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: function.wikiedit.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

function smarty_function_wikiedit($params, &$smarty)
{
    $output  = '<script type="text/javascript" src="modules/Wikula/pnincludes/wikiedit/protoedit.js"></script>
                <script type="text/javascript" src="modules/Wikula/pnincludes/wikiedit/wikiedit2.js"></script>
                <script type="text/javascript">
                  maAvailable = '.(int)pnModAvailable('MediaAttach').';
                  wE = new WikiEdit();
                  wE.init("wikula_body", "WikiEdit", "editornamecss", "modules/Wikula/pnincludes/wikiedit/images/");
                </script>';

    if (isset($assign)) {
        $smarty->assign($assign, $output);
    } else {
        return $output;
    }
}

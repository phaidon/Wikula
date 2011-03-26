<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: function.wikkaedit.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

function smarty_function_wikkaedit($params, &$smarty)
{
   $output  = '<script type="text/javascript">
                  /* <![CDATA[ */
                  Wikkaedit_baseURL = "'.pnGetBaseURL().'";
                  /* ]]> */
                </script>
                <script type="text/javascript" src="modules/Wikula/pnincludes/wikkaedit/wikkaedit_data.js"></script>
                <script type="text/javascript" src="modules/Wikula/pnincludes/wikkaedit/wikkaedit_search.js"></script>
                <script type="text/javascript" src="modules/Wikula/pnincludes/wikkaedit/wikkaedit_actions.js"></script>
                <script type="text/javascript" src="modules/Wikula/pnincludes/wikkaedit/wikkaedit.js"></script>';

    if (isset($assign)) {
        $smarty->assign($assign, $output);
    } else {
        return $output;
    }
}

<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: function.wakka.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

function smarty_function_wakka($params, &$smarty)
{
    return ModUtil::apiFunc('Wikula', 'user', 'wakka', array(
        'text'   => $params['text'],
        'method' => isset($params['method']) ? $params['method'] : null)
    );
}

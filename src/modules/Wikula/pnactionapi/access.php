<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: access.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Set a level access check in the session
 * 
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_access($args)
{
    if (!isset($args['level']) || empty($args['level'])) {
        return;
    }

    $access = array(
        'ACCESS_COMMENT'  =>  300,
        'ACCESS_MODERATE' =>  400,
        'ACCESS_EDIT'     =>  500,
        'ACCESS_ADD'      =>  600,
        'ACCESS_DELETE'   =>  700,
        'ACCESS_ADMIN'    =>  800
    );

    if (!isset($access[$args['level']])) {
        return;
    }

    SessionUtil::setVar('wikula_access', $args['level']);

    return;
}

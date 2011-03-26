<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: googleform.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Shows a small google form
 * 
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['q'] query string
 * @todo check if works and any possible features
 */
function wikula_actionapi_googleform($args)
{
    $q = '';
    if (isset($args['q']) && !empty($args['q'])) {
        $q = DataUtil::formatForDisplay($args['q']);
    }

    $render = pnRender::getInstance('wikula');
    $render->assign('q', $q);
    return $render->fetch('action/googleform.tpl');
}

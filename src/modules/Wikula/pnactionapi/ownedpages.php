<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: ownedpages.php 140 2010-03-02 15:10:37Z gilles $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Shows stats of owned pages of current user
 *
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Chris Tessmer
 */
function wikula_actionapi_ownedpages($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!pnUserLoggedIn()) {
        return;
    }

    $result = pnModAPIFunc('wikula', 'user', 'LoadAllPagesOwnedByUser',
                           array('uname'     => pnUserGetVar('uname'),
                                 'justcount' => true));

    if (!$result) {
        return __('Error during element fetching !', $dom);
    }

    $render = pnRender::getInstance('wikula'/*, false*/);

    $render->assign($result);
    $render->assign('percent', round(($result['count']/$result['total'])*100, 2));

    return $render->fetch('action/ownedpages.tpl');
}

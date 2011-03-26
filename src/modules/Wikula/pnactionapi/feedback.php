<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: feedback.php 130 2009-09-22 08:42:40Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Displays a form to send feedback to the site administrator,
 * or to the owner of the page. The Zikula version of this action
 * needs the Mailer in order to work.
 * 
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @todo check for improvements
 */
function wikula_actionapi_feedback($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!pnModAvailable('Mailer')) {
        return __('The Mailer module is unavailable!', $dom);   
    }

    $tag  = FormUtil::getPassedValue('tag', pnModGetVar('wikula', 'root_page'));
    $mail = FormUtil::getPassedValue('mail');

    $render = pnRender::getInstance('wikula'/*, false*/);
    $render->force_compile = true;
    $render->clear_cache('action/feedback.tpl');

    $render->assign('tag', $tag);

    $error = 0;

    if ($mail == 'result') {
        $name     = FormUtil::getPassedValue('name');
        $email    = FormUtil::getPassedValue('email');
        $comments = FormUtil::getPassedValue('comments');

        if (empty($name)) {
            $error = 1;
        } elseif (!pnVarValidate($email, 'email')) {
            $error = 2;
        } elseif (empty($comments)) {
            $error = 3;
        }

        if ($error != 0) {
            $render->assign('error',    $error);
            $render->assign('name',     $name);
            $render->assign('email',    $email);
            $render->assign('comments', $comments);
            $render->assign('success',  false);

            return $render->fetch('action/feedback.tpl');
        }

        $toaddress = pnConfigGetVar('adminmail');
        $toname    = pnConfigGetVar('sitename');

        $result = pnModAPIFunc('Mailer', 'user', 'sendmessage',
                               array('toname'      => $toname,
                                     'toaddress'   => $toaddress,
                                     'fromname'    => $name,
                                     'fromaddress' => $email,
                                     'subject'     => 'FeedBack',
                                     'body'        => $comments,
                                     'html'        => false));

        $render->assign('success', $result);
        return $render->fetch('action/feedback.tpl');

    } else {
        return $render->fetch('action/feedback.tpl');
    }
}

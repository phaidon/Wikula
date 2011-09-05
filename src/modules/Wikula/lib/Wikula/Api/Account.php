<?php

/**
 * @copyright (c) 2011, Fabian Wuertz
 * @author Fabian Wuertz
 * @link http://fabian.wuertz.org
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package ISHAmembers
 */

class Wikula_Api_Account extends Zikula_AbstractApi 
{

	/**
	* Return an array of items to show in the your account panel
	*
	* @return   array   array of items, or false on failure
	*/
	public function getall($args)
	{
	    $items = array();
	    if (
                UserUtil::isLoggedIn() and
                SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ) and 
                $this->getVar('subscription', false)
            ) {
		// Create an array of links to return
		$items[] = array('url'     => ModUtil::url('Wikula', 'user', 'settings'),
				'module'  => $this->name,
				'set'     => '',
				'title'   => 'Wiki settings',
				'icon'    => 'admin.png');
	    }
	    // Return the items
	    return $items;
	}

}

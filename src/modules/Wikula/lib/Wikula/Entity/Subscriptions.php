<?php
/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

use Doctrine\ORM\Mapping as ORM;

/**
 * Wikula links entity class.
 *
 * Annotations define the entity mappings to database.
 *
 * @ORM\Entity
 * @ORM\Table(name="wikula_subscriptions")
 */
class Wikula_Entity_Subscriptions extends Zikula_EntityAccess
{
    
    /**
     * The following are annotations which define the id field.
     *
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer", length=16, unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $uid;

    /**
     * Return the uid field
     *
     * @return integer uid 
     */
    public function getuid()
    {
        return $this->uid;
    }
    

    /**
     * Set the uid field
     * 
     * @param integer $uid User id.
     * 
     * @return boolean
     */
    public function setuid($uid)
    {
        $this->uid = $uid;
        return true;
    }

}
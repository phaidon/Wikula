<?php

/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
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
 * @package Wikula
 * @ORM\Entity
 * @ORM\Table(name="wikula_links")
 */
class Wikula_Entity_Links2 extends Zikula_EntityAccess
{
    
      
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=75)
     */
    private $from_tag;
    
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=75)
     */
    private $to_tag;
    
    /**
     * Get from_tag
     * 
     * @return string
     */
    public function getfrom_tag()
    {
        return $this->from_tag;
    }
    
    /**
     * Set from_tag
     * 
     * @param string
     */
    public function setfrom_tag($from_tag)
    {
        $this->from_tag = $from_tag;
    }
    
    /**
     * Get to_tag
     * 
     * @return string
     */
    public function getto_tag()
    {
        return $this->to_tag;
    }
    
    /**
     * Set to_tag
     * 
     * @param string
     */
    public function setto_tag($to_tag)
    {
        $this->to_tag = $to_tag;
    }

}
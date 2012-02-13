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
 * @ORM\Table(name="wikula_links")
 */
class Wikula_Entity_Links extends Zikula_EntityAccess
{
    
      
    /**
     * From tag
     * 
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=75)
     */
    private $from_tag;
    
    /**
     * To tag
     * 
     * @var Wikula_Entity_Pages
     * @ORM\ManyToOne(targetEntity="Wikula_Entity_Pages", inversedBy="links")
     * @ORM\JoinColumn(name="to_tag", referencedColumnName="tag")
     * @ORM\Id
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
     * @param string $from_tag From tag.
     * 
     * @return boolean
     */
    public function setfrom_tag($from_tag)
    {
        $this->from_tag = $from_tag;
        return true;
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
     * @param string $to_tag To tag.
     * 
     * @return boolean
     */
    public function setto_tag($to_tag)
    {
        $this->to_tag = $to_tag;
        return true;
    }

}
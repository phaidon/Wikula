<?php

/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Piwik
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
 * @ORM\Table(name="wikula_pages")
 */
class Wikula_Entity_Pages extends Zikula_EntityAccess
{
    
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=16, unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="string", length=75)
     */
    private $tag;
    
   
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="datetime")
     */
    private $time;
    

    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="text")
     */
    private $body;


    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="string", length=75)
     */
    private $owner;   
    
    
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="string", length=75)
     */
    private $user; 
    
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="string", length=1)
     */
    private $latest = 'N'; 
       
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="string", length=100)
     */
    private $note; 
    
       /**
     * The following are annotations which define the id field.
     *
     * @ORM\Column(type="string", length=30)
     */
    private $handler = 'page';
    
    
    
    public function setNote($note)
    {
        $this->note = $note;
    }
    
    public function setTag($tag)
    {
        $this->tag = $tag;
    }
    
    public function setBody($body)
    {
        $this->body = $body;
    }
    
    
    public function setTime($time)
    {
        $this->time = new \DateTime($time);
    }
    
    public function setUser($user)
    {
        $this->user = $user;
    }
    
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }
    
        public function setLatest($latest)
    {
        $this->latest = $latest;
    }

    
    
    
}


/*  
    public function setUp() {
        $this->hasMany('Wikula_Model_Links', array(
            'local' => 'tag',
            'foreign' => 'to_tag',
            'onDelete' => 'CASCADE')
        ); 
*/

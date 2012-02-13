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
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * The following are annotations which define the id field.
     *
     * @var string
     * @ORM\Column(type="string", length=75)
     */
    private $tag;
    
   
    /**
     * links
     *
     * @var Wikula_Entity_Links
     * @ORM\OneToMany(targetEntity="Wikula_Entity_Links", 
     *                mappedBy="to_tag", cascade={"all"}, 
     *                orphanRemoval=true)
     */
    private $links;

    
    
    
    /**
     * The following are annotations which define the time field.
     *
     * @var datetime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $time;
    

    /**
     * The following are annotations which define the id field.
     *
     * @var text
     * @ORM\Column(type="text")
     */
    private $body;


    /**
     * The following are annotations which define the id field.
     *
     * @var string
     * @ORM\Column(type="string", length=75)
     */
    private $owner;   
    
    
    /**
     * The following are annotations which define the id field.
     *
     * @var string
     * @ORM\Column(type="string", length=75)
     */
    private $user; 
    
    /**
     * The following are annotations which define the id field.
     *
     * @var string
     * @ORM\Column(type="string", length=1)
     */
    private $latest = 'N'; 
       
    /**
     * The following are annotations which define the id field.
     *
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $note; 
    
    /**
     * The following are annotations which define the id field.
     *
     * @var string
     * @ORM\Column(type="string", length=30)
     */
    private $handler = 'page';

    /**
     * Construction function
     */
    public function __construct()
    {
        $this->links = new Doctrine\Common\Collections\ArrayCollection();
        return true;
    }

    /**
     * Returns the links of the wiki page
     * 
     * @return array 
     */
    public function getLinks()
    {
        $links = $this->links;
        $to = array();
        foreach ($links as $link) {
            $to[] = $link->getto_tag();
        }
        return $to;
    }
    
    /**
     * Get all
     * 
     * @return string 
     */
    public function toArray()
    {
        return array(
            'id'     => $this->id,
            'tag'    => $this->tag,
            'latest' => $this->latest,
            'body'   => $this->body,
            'user'   => $this->user,
            'time'   => $this->time,
            'owner'  => $this->owner,
            'note'   => $this->note,
        );
            
    }
    

    /**
     * Set note
     * 
     * @param string $note Note.
     * 
     * @return boolean
     */
    public function setNote($note)
    {
        $this->note = $note;
        return true;
    }
    
    /**
     * Set tag
     * 
     * @param string $tag Tag.
     * 
     * @return boolean
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return true;
    }
    
    /**
     * Set body
     * 
     * @param string $body Body.
     * 
     * @return boolean
     */
    public function setBody($body)
    {
        $this->body = $body;
        return true;
    }
    
    /**
     * Set time
     * 
     * @param datetime $time Time.
     * 
     * @return boolean 
     */
    public function setTime($time)
    {
        $this->time = new \DateTime($time);
        return true;
    }
    
    /**
     * Set user
     * 
     * @param string $user User.
     * 
     * @return boolean 
     */
    public function setUser($user)
    {
        $this->user = $user;
        return true;
    }
    
    /**
     * Set owner
     * 
     * @param string $owner Owner.
     * 
     * @return boolean 
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return true;
    }
    
    /**
     * Set latest
     * 
     * @param string $latest Latest.
     * 
     * @return boolean 
     */
    public function setLatest($latest)
    {
        $this->latest = $latest;
        return true;
    }

    
    
}
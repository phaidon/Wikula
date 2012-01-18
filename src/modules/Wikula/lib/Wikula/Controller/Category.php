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

/**
 * Access to (non-administrative) category-initiated actions for the Wikula module.
 * 
 * @package Wikula
 */
class Wikula_Controller_Category extends Zikula_AbstractController
{
    
    /**
     * main
     * 
     * This function is a forward to the show function. 
     *
     */    
    public function main($args)
    {
        return $this->show($args);
    }
    
    /**
     * show
     * 
     * Displays a category page
     *
     * @param string $args['category'] Category of the wiki page to show
     * @return smarty output
     */
    public function show($args)
    {   
        
        
        // Get input parameters
        $category  = isset($args['category']) ? $args['category'] : FormUtil::getPassedValue('category');

        if( empty( $category ) ) {
            return LogUtil::registerError( $this->__('No category specified!') );
        }
        
        // get pages of a category
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('c.tag')
           ->from('Wikula_Entity_Categories', 'c')
           ->where('c.category = :category')
           ->setParameter('category', $category)
           ->orderBy('c.tag');
        $pages = $qb->getQuery()->getArrayResult();

        
        return $this->view->assign('category', $category)
                          ->assign('pages', $pages)
                          ->fetch('category/show.tpl');
    }
    
    
    /**
     * show
     * 
     * Displays a list of all categories
     *
     * @param string $args['category'] Category of the wiki page to show
     * @return smarty output
     */
    public function showAll()
    {   

        // get a list of all categoriess
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('c.category')
           ->from('Wikula_Entity_Categories', 'c')
           ->groupBy('c.category')
           ->orderBy('c.category');
        $categories = $qb->getQuery()->getArrayResult();
 
        return $this->view->assign('categories', $categories)
                          ->fetch('category/showAll.tpl');
    }
    
  
  
    
}

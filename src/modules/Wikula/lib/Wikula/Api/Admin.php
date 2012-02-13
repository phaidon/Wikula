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

/**
 * Admin api class.
 */
class Wikula_Api_Admin extends Zikula_AbstractApi
{
    /**
     * get available admin panel links
     *
     * @return array array of admin links
     */
    public function getlinks()
    {
        $links = array();
        if (SecurityUtil::checkPermission('Wikula::', '::', ACCESS_ADMIN)) {
            $links[] = array(
                'url' => ModUtil::url('Wikula', 'admin', 'pages'),
                'text' =>  __('Wiki pages'),
                'class' => 'z-icon-es-view'
            );
            $links[] = array(
                'url' => ModUtil::url('Wikula', 'admin', 'modifyconfig'),
                'text' => __('Settings'),
                'class' => 'z-icon-es-config'
            );
        }
        return $links;
    }

    /**
     * get all owners
     *
     * @return array array of owners
     */
    public function GetOwners()
    {
        
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadPages' );
        $owners = array();
        foreach ($pages as $value) {
            $owners[$value['owner']] = $value['owner'];
        }

        if ($owners === false) {
            return LogUtil::registerError(__('Error! Get owners failed.'));
        }

        $items = array();
        $items['owners']      = $owners;
        $items['ownerscount'] = sizeof($owners);

        return $items;
    }

    /**
     * PageIndex
     * 
     * @return unknown
     */
    public function PageIndex()
    {
        
        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadAllPages');

        $requested_letter = FormUtil::getPassedValue('letter');
        $currentpage      = FormUtil::getPassedValue('tag');

        if (UserUtil::isLoggedIn()) {
            $cached_username = pnUserGetVar('uname');
        } else {
            $cached_username = '';
        }

        $current_character = '';
        $character_changed = false;
        $user_owns_pages   = false;
        $pagelist          = array();
        $headerletters     = array();

        if ($pages) {
            foreach ($pages as $page) {

                $page_owner = $page['owner'];

                $firstChar = strtoupper(substr($page['tag'],0,1)); //echo $firstChar;
                if (!preg_match('/[A-Za-z]/', $firstChar)) {
                    $firstChar = '#';
                }

                if ($firstChar != $current_character) {
                    $headerletters[] = $firstChar;
                    $current_character = $firstChar;
                    $character_changed = true;
                }
                if ($requested_letter == '' || $firstChar == $requested_letter) {
                    if ($character_changed) {
                        $character_changed = false;
                    }

                    $pagelist[$firstChar][] = $page;

                    if ($cached_username == $page_owner) {
                        $user_owns_pages = true;
                    }
                }

            }
        }

        $render = pnRender::getInstance('Wikula', false);

        $render->assign('currentpage',   $currentpage);
        $render->assign('headerletters', $headerletters);
        $render->assign('pagelist',      $pagelist);
        $render->assign('username',      $cached_username);
        $render->assign('userownspages', $user_owns_pages);

        return $render->fetch('wikula_admin_pages.tpl', $tag.$letter);
    }

    /**
     * This function returns all pages. 
     * 
     * @param array $args Arguments.
     * 
     * @return array wiki pages
     */
    public function getall($args)
    {

        if ($args['orderBy'] == 'revisions' ||
            $args['orderBy'] == 'comments'  ||
            $args['orderBy'] == 'backlinks') {
            $orderBy = $args['orderBy'];
            unset($args['orderBy']);
        }
        
        
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadPages', $args);
        $count = $pages['count'];
        $pages = $pages['pages'];

        foreach ($pages as $pageID => $pageTab) {
            $pages[$pageID]['revisions'] = ModUtil::apiFunc($this->name, 'admin', 'CountRevisions', array('tag' => $pageTab['tag']));
            $pages[$pageID]['backlinks'] = ModUtil::apiFunc($this->name, 'user',  'CountBackLinks', $pageTab['tag']);
            if (ModUtil::available('EZComments')) {
                $commentsCount = ModUtil::apiFunc('EZComments', 'user', 'countitems', array(
                    'mod' => $this->name,
                    'objectid' => $pageTab['tag']
                ));
            } else {
                $commentsCount = 0;
            }
            $pages[$pageID]['comments'] = $commentsCount;
        }


        if (isset($orderBy)) {
            $sortAarr = array();
            foreach ($pages as $res) {
                $sortAarr[] = $res[$orderBy];
            }
            array_multisort($sortAarr, (($args['orderDirection'] == 'ASC') ? SORT_ASC : SORT_DESC), SORT_NUMERIC, $pages);
        }

        return array(
            'pages' => $pages,
            'count' => $count
        );
    }

    /**
     * This function counts the revisions of a wiki page. 
     * 
     * @param array $args Arguments.
     * 
     * @return int number of revisions
     */
    public function CountRevisions($args = array())
    {
        if (!isset($args['tag']) || empty($args['tag'])) {
            return false;
        }
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('count(p.tag)')
           ->from('Wikula_Entity_Pages', 'p')
           ->where('p.tag = :to_tag')
           ->setParameter('to_tag', $args['tag']);
        $query = $qb->getQuery();
        $pagecount = $query->getSingleScalarResult();
        if ($pagecount === false) {
            return LogUtil::registerError(__('Error! Count the revisions for this page failed.'));
        }
        return $pagecount;
    }

    /**
     * This function deletes a revision by id. 
     * 
     * @param array $args Arguments.
     * 
     * @return boolean
     */
    public function deletepageid($args)
    {
        $id  = $args['id'];

        if (empty($id) || !is_numeric($id)) {
            return false;
        }
                
        $page = $this->entityManager->find('Wikula_Entity_Pages', $id);
        $this->entityManager->remove($page);
        $this->entityManager->flush();        
        
        return true;
    }

    /**
     * This function set the newest revision active. 
     * 
     * @param string $tag Tag of a wiki page.
     * 
     * @return boolean
     */
    public function setlatest($tag)
    {
        $em = $this->getService('doctrine.entitymanager');
        
        
        // check if there is still a latest revision
        $qb = $em->createQueryBuilder();
        $qb->select('p.id')
           ->from('Wikula_Entity_Pages', 'p')
           ->where("p.tag = :tag and p.latest = 'Y'")
           ->setParameter('tag', $tag)
           ->setMaxResults(1);
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if (count($result) > 0 ) {
            return false;
        }        
        
        
        // get newest revision
        $qb = $em->createQueryBuilder();
        $qb->select('p.id, p.body')
           ->from('Wikula_Entity_Pages', 'p')
           ->where("p.tag = :tag")
           ->setParameter('tag', $tag)
           ->orderBy('p.time', 'desc')
           ->setMaxResults(1);
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if (count($result) == 0 ) {
            return;
        }
        
        
        
        // set newest revision active 
        $id   = $result[0]['id'];
        $text = $result[0]['body'];
        $qb = $em->createQueryBuilder();
        $qb->update('Wikula_Entity_Pages', 'p')
           ->where("p.id = :id")
           ->setParameter('id', $id)
           ->set('p.latest', "'Y'");
        $query = $qb->getQuery();
        $query->getArrayResult();

        ModUtil::apiFunc(
            $this->name,
            'user',
            'updateLinksAndCategories',
            array(
                'tag'  => $tag,
                'text' => $text
            )
        );
                
        return;
    }
    
    /**
     * This function deletes a wiki pages with all revisions. 
     * 
     * @param string $tag Tag of a wiki page.
     * 
     * @return boolean
     */
    public function deletepage($tag)
    {

        if (empty($tag)) {
            return LogUtil::registerArgsError();
        }
                
        // remove page (all revisions)
        $page = $this->entityManager->getRepository('Wikula_Entity_Pages')
                        ->findBy(array('tag' => $tag));
        foreach ($page as $revision) {
            $this->entityManager->remove($revision);
            $this->entityManager->flush();
        }
        
        
        // remove links 
        $page = $this->entityManager->getRepository('Wikula_Entity_Links2')
                        ->findBy(array('from_tag' => $tag));
        foreach ($page as $revision) {
            $this->entityManager->remove($revision);
            $this->entityManager->flush();
        }
        
        
        // remove categories 
        $page = $this->entityManager->getRepository('Wikula_Entity_Categories')
                        ->findBy(array('tag' => $tag));
        foreach ($page as $revision) {
            $this->entityManager->remove($revision);
            $this->entityManager->flush();
        }
        
        return true;
    }

    
}

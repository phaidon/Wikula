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

use Doctrine\ORM\Query;

require_once 'modules/Wikula/lib/Wikula/Common.php';

/**
 * User api class.
 */
class Wikula_Api_User extends Zikula_AbstractApi
{   
    
    /**
     * Validate a PageName
     * 
     * @param array $args Arguments.
     * 
     * @return true
     */
    public function isValidPagename($args)
    {
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        return preg_match(VALID_PAGENAME_PATTERN, $args['tag']);
    }

    /**
     * Check access to edit a page
     * 
     * @param array $args Arguments.
     * 
     * @return boolean
     */
    public function isAllowedToEdit($args)
    {
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }
        
        return ModUtil::apiFunc($this->name, 'Permission', 'canEdit', $args['tag']); 
    }

    /**
     * Load a wiki page with the given tag
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * string $args['tag'] Tag of the wiki page to get
     * string $args['time'] (Optional) Update time, latest page if not defined
     * 
     * @param array $args Page arguments.
     * 
     * @return array wiki page data
     */
    public function LoadPage($args)
    {
                
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError($this->__('No tag given!') );
        }
        
        if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$args['tag'], ACCESS_READ)) {
            return LogUtil::registerError(__('You do not have the authorization to read this page!'), 403);
        }
        
        
        $em = $this->getService('doctrine.entitymanager');


        if (isset($args['rev']) && !empty($args['rev'])) {
            return $em->getRepository('Wikula_Entity_Pages')
                      ->findOneBy(array('tag' => $args['tag'], 'id' => $args['rev']))
                      ->toArray();
        } else {
            $result = $em->getRepository('Wikula_Entity_Pages')
                      ->findOneBy(array('tag' => $args['tag'], 'latest' => 'Y'));
            if ($result) {
                $result = $result->toArray();
            }
            return $result;
        }
        

    }
    
    /**
     * Load a all revisions of a wiki page
     *
     * @param string $tag Tag of the wiki page.
     * 
     * @return array list of all revisions of a wiki page.
     */
    public function LoadRevisions0($tag) {
                        
        if (!isset($tag)) {
            return LogUtil::registerArgsError($this->__('No tag given!') );
        }
        
        if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_READ)) {
            return LogUtil::registerError(__('You do not have the authorization to read this page!'), 403);
        }
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->where('p.tag = :tag')
           ->setParameter('tag', $tag)
           ->orderBy('p.time', 'DESC');


        // return the page or false if failed
        return $qb->getQuery()->getArrayResult();

    }
    
    /**
     * This functions diffs two revisions of a Wiki page
     * 
     * @param array $args Arguments.
     * 
     * @return mixed
     */
    public function DiffRevisions($args) {
        
        if (!isset($args['rev1']) || !isset($args['rev2']) || !isset($args['revisions'])) {
            return null;
        }
        
        if ($args['rev1'] == $args['rev2'] ) {
            return null;
        }
        
        foreach ($args['revisions'] as $revision) {        
            if ($revision['id'] == $args['rev1']) {
                $lines1 = explode("\n", $revision['body']);
            } else if ($revision['id'] == $args['rev2']) {
                $lines2 = explode("\n", $revision['body']);
            } else {
                continue;
            }
            if (isset($lines1) && isset($lines2)) {
                break;
            }
        }
              
        require_once 'modules/Wikula/lib/vendor/Text_Diff/Diff.php';
        require_once 'modules/Wikula/lib/vendor/Text_Diff/Diff/Renderer.php';
        require_once 'modules/Wikula/lib/vendor/Text_Diff/Diff/Renderer/unified.php';

        $diff     = new Text_Diff('auto', array($lines1, $lines2));
        $renderer = new Text_Diff_Renderer_Unified(
        );
        $text = $renderer->render($diff);
        $diff = explode("\n", $text);
        array_shift($diff);
        end($diff);
        if (current($diff) == '') {
           array_pop($diff);
        }
        return $diff;
    }


    /**
     * Check if a page exist
     * 
     * @param string $tag Tag of the wiki page.
     * 
     * @return id of the page, false if doesn't exists
     */
    public function PageExists($tag)
    {
        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }
        
        $specialPages = ModUtil::apiFunc($this->name, 'SpecialPage', 'listpages');
        if (array_key_exists($tag, $specialPages)) {
            return true;
        }
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->where('p.tag = :tag')
           ->setParameter('tag', $tag)
           ->andWhere('p.latest = :latest')
           ->setParameter('latest', 'Y')
           ->setMaxResults(1);

        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if (!$result) {
            return false;
        }
        return $result[0]['id'];

    }

    /**
     * Load a wiki page revision by id
     *
     * @param integer $id If of a wiki page revision.
     * 
     * @return array wiki page revision
     */
    public function LoadPagebyId($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            return LogUtil::registerArgsError();
        }
        

        $page = $this->entityManager->find('Wikula_Entity_Pages', $id);

        if ($page === false) {
            return LogUtil::registerError(__('Error! Getting the this page by id failed.'));
        }
        
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $page['tag']); 


        return $page;

    }

    /**
     * Get the Revisions of a Wiki Page
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * string $args['tag'].
     * string $args['startnum'] (optional) start offset.
     * string $args['numitems'] (optional) number of items to fetch.
     * string $args['orderby'] (optional) sort by fieldname.
     * string $args['orderdir'] (optional) sort direction (ASC or DESC).
     * string $args['loadbody'] (optional) flag to include the body in the results.
     * string $args['getoldest'] (optional) flag to fetch the oldes revision only.
     * 
     * @param array $args Page arguments.
     * 
     * @return array of revisions
     */
    public function LoadRevisions($args)
    {
        // Security check will be done by LoadRevisions()
        
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        $args['latest'] = false;


        // build the order by
        if (!isset($args['orderby'])) {
            $args['orderBy'] = 'time';
        } else {
            $args['orderBy'] = $orderby;
        }
        if (!isset($args['orderdir']) || !in_array(strtoupper($args['orderdir']), array('ASC', 'DESC'))) {
            $args['orderdir'] = 'DESC';
        }
        
        // check if we want to get the latest only
        if (isset($getoldest) && $getoldest) {
            $args['orderBy'] = $args['time'].' DESC';
            $numitems = 1;
        }

        
        $revisions = $this->LoadPages($args);
        
        // return the results
        if (isset($revisions[0]) && isset($getoldest) && $getoldest) {
            $revisions = $revisions[0];
        }

        
        $objects  = array();
        $previous = array();
        foreach ($revisions as $page) {

            if (empty($previous)) {
                // We filter the first one as we don't want to check it
                $previous = $page;
                continue;
            }

            $bodylast = explode("\n", $previous['body']);

            $bodynext = explode("\n", $page['body']);

            $added   = array_diff($bodylast, $bodynext);
            $deleted = array_diff($bodynext, $bodylast);

            if ($added) {
                $newcontent = implode("\n", $added); //."\n"
            } else {
                $newcontent = '';
                $added = false;
            }

            if ($deleted) {
                $oldcontent = implode("\n", $deleted);//."\n"
            } else {
                $oldcontent = '';
                $deleted = false;
            }

            
            
            $objects[] = array(
                //TODO zikula dateformat
                'pageAtime'    => $previous['time'],
                'pageBtime'    => $page['time'],
                'pageAtimeurl' => urlencode(DateUtil::formatDatetime($previous['time'])),
                'pageBtimeurl' => urlencode(DateUtil::formatDatetime($page['time'])),
                'EditedByUser' => $previous['user'],
                'note'         => $previous['note'],
                'newcontent'   => $newcontent,
                'oldcontent'   => $oldcontent,
                'added'        => $added,
                'deleted'      => $deleted
            );

            $previous = $page;
        }

        
        return array(
            'objects' => $objects,
            'oldest'  => $page
        );

    }

    /**
     * Load wiki pages that link to a given wiki page
     *
     * @param string $tag Tag of a wiki page.
     * 
     * @return array wiki pages
     */
    public function LoadPagesLinkingTo($tag)
    {
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $tag);
        
        
        $em = $this->getService('doctrine.entitymanager');
        $query = $em->createQueryBuilder();
        $query->select('l')
           ->from('Wikula_Entity_Links', 'l')
           ->where('l.to_tag = :to_tag')
           ->setParameter('to_tag', $tag);
        $links = $query->getQuery()->execute();        
        
        if ($links === false) {
            return LogUtil::registerError(__('Error! Getting the links for this page failed.'));
        }

        return $links;
    }
    

    /**
     * Count back links of a wiki page
     *
     * @param string $tag Tag of a wiki page.
     * 
     * @return integer Number of backlinks.
     */
    public function CountBackLinks($tag)
    {
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $tag);
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('count(l.from_tag)')
           ->from('Wikula_Entity_Links', 'l')
           ->where('l.to_tag = :to_tag')
           ->setParameter('to_tag', $tag);
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();

    }

    /**
     * Load the recently changed pages
     *
     * @param array $args Wiki page arguments.
     * 
     * @return array Revisions.
     */
    public function LoadRecentlyChanged($args)
    {
        
        extract($args);
        unset($args);


        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->where("p.latest = 'Y'")
           ->orderBy('p.time', 'DESC');
        $query = $qb->getQuery();
        
        
        
        if (isset($startnum) && is_numeric($startnum) && $startnum > 1) {
            $query->setFirstResult($offset = $startnum-1);
        }
        if (isset($numitems) && is_numeric($numitems) && $numitems > 0) {
            $query->setMaxResults($limit = $numitems);

        }    
        


        // return the page or false if failed
        $revisions = $query->getArrayResult();
        
        if ($revisions === false) {
            return LogUtil::registerError(__('Error! Getting revisions failed.'));
        }

        
        if (!empty($formated) && $formated) {
            $curday = '';
            $pagelist = array();
            foreach ($revisions as $page) {
                $day  = $page['time']->format('Y-m-d');
                if ($day != $curday) {
                    $dateformatted = $page['time']->format('D, d M Y');
                    $curday = $day;
                }

                if ($page['user'] == System::getVar('anonymous')) {
                    $page['user'] .= ' ('.$this->__('anonymous user').')'; // anonymous user
                }

                $pagelist[$dateformatted][] = $page;
            }
            return $pagelist;
        }


        return $revisions;

    }


    /**
     * Load all wiki pages
     *
     * @param array $args Arguments.
     * 
     * @return array wiki pages
     */
    public function LoadAllPages($args)
    {
        
        $args['orderBy'] = 'tag';         
        $pages = $this->LoadPages($args);

        if ($pages === false) {
            return LogUtil::registerError($this->__('Error! Getting all pages failed.'));
        }

        return $pages;

    }

    
    /**
     * Load all wiki pages
     *
     * @param array $args Arguments.
     * 
     * @return array wiki pages
     */
    public function LoadPages($args)
    {
        if (!isset($args['tag'])) {
            $args['tag'] = null;
        }
        
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $args['tag']);
  
        
        $em = $this->getService('doctrine.entitymanager');
        $query = $em->createQueryBuilder();
        $query->select('p')
              ->from('Wikula_Entity_Pages', 'p');
                
        
        if (isset($args['orphaned']) &&  $args['orphaned']) { 
            $query->leftJoin('p.links', 'l');
            $query->where("p.links IS EMPTY");    
        }

        
        
        if (!empty($args['q'])) {
            $query->andWhere("p.tag LIKE :q")
                  ->setParameter('q', '%'.$args['q'].'%');        
        }
        
        if (!empty($args['s'])) {
            $query->andWhere("p.tag LIKE :s")
                  ->setParameter('s', $args['s'].'%');        
        }
        
        if (!isset($args['latest']) || $args['latest']) {
            $query->andWhere("p.latest = 'Y'");        
        }
        
        if (!is_null($args['tag']) ) {
            $query->andWhere('p.tag = :tag')
                  ->setParameter('tag', $args['tag']);
        }
        
        if (isset($args['orderBy']) ) {
            if (empty($args['orderDirection'])) {
                $args['orderDirection'] = 'ASC';
            }
            $query->orderBy('p.'.$args['orderBy'], $args['orderDirection']);
        }
        
        
        if (isset($args['startnum']) && is_numeric($args['startnum']) && $args['startnum'] > 1) {
            $query->setFirstResult($args['startnum']-1);
        }
        if (isset($args['numitems']) && is_numeric($args['numitems']) && $args['numitems'] > 0) {
            $query->setMaxResults($args['numitems']);

        }
        
        
        $query = $query->getQuery();  
        
        // Paginator
        if (isset($args['itemsperpage']) && is_numeric($args['itemsperpage'])) {
            if (empty($args['startnum'])) {
                $args['startnum'] = 1;
            }
            $count = \DoctrineExtensions\Paginate\Paginate::getTotalQueryResults($query);
            $paginateQuery = \DoctrineExtensions\Paginate\Paginate::getPaginateQuery($query, $args['startnum'] -1 , $args['itemsperpage']); // Step 2 and 3
            return array(
                'pages' => $paginateQuery->getArrayResult(),
                'count' => $count
            );
        } else {
            // No paginator
            return $query->getArrayResult();
        }
        
    }
    
    /**
     * Load all wiki pages edited by a user
     *
     * @param array $args Arguments.
     * 
     * @return array wiki pages
     */
    public function LoadAllPagesEditedByUser($args)
    {        
        extract($args);
        unset($args);

        if (!isset($uname)) {
            return false;
        }
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->where('p.user = :user')
           ->setParameter('user', $uname);        


        if (!isset($all) || (isset($all) && !$all)) {
            $qb->andWhere("p.latest = 'Y'");
        }
        if (isset($alpha) && $alpha == 1) {
            $qb->add('orderBy', 'p.tag ASC, p.time DESC');
        } else {
            $qb->add('orderBy', 'p.time DESC, p.tag ASC');
        }
        $query = $qb->getQuery();

        
        if (isset($startnum) && is_numeric($startnum) && $startnum > 1) {
            $query->setFirstResult($offset = $startnum-1);
        }
        if (isset($numitems) && is_numeric($numitems) && $numitems > 0) {
            $query->setMaxResults($limit = $numitems);

        }    
        
        
        $pages = $query->getArrayResult();
        

        if ($pages === false) {
            return LogUtil::registerError(__('Error! Getting all pages by user failed.'));
        }

        return $pages;

    }

    /**
     * Count the total number of active pages in the Wiki
     *
     * @return integer total wiki pages
     */
    public function CountAllPages()
    {                  
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('count(p.id) as num')
           ->from('Wikula_Entity_Pages', 'p')
           ->where("p.latest = 'Y'" );
        $query = $qb->getQuery();
        $count = $query->getSingleScalarResult();
        return $count;

    }

    /**
     * Load all the pages owned by a specific User
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * string  $args['uname'] username to search.
     * integer $args['startnum'] (optional) start point.
     * integer $args['numitems'] (optional) number of items to fetch.
     * integer $args['justcount'] (optional) flag to perform just a page count and not return the wiki pages.
     * 
     * @param array $args Pages arguments.
     * 
     * @return arrray Wiki pages.
     */
    public function LoadAllPagesOwnedByUser($args)
    {
        extract($args);
        unset($args);
        if (!isset($uname) || empty($uname)) {
            return LogUtil::registerArgsError();
        }
       
        // defaults
        if (!isset($justcount) || !is_bool($justcount)) {
            $justcount = false;
        }
        if (!isset($orderby)) {
            $orderby = 'tag';
        }
        if (!isset($orderdir) || !in_array(strtoupper($orderdir), array('ASC', 'DESC'))) {
            $orderdir = 'ASC';
        }
                 
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->orderBy('p.'.$orderby, $orderdir)
           ->where("p.latest = 'Y' AND p.owner = :owner" )
           ->setParameter('owner', $uname);
        
        
        if (isset($startnum) && is_numeric($startnum) && $startnum > 1) {
            $query->setFirstResult($offset = $startnum-1);
        }
        if (isset($numitems) && is_numeric($numitems) && $numitems > 0) {
            $query->setMaxResults($limit = $numitems);
        }  

        
        if ($justcount) {
            $qb->select('count(p.id) as num');
            $query = $qb->getQuery();
            $count = $query->getSingleScalarResult();
        } else {
            $query = $qb->getQuery();
            $pages = $query->getArrayResult();;
        }
     

        
        // build the result array
        $result = array();

        if ($justcount) {
            $result['count'] = $count;
        } else {
            $result['pages'] = $pages;
            $result['count'] = count($pages);
        }
        $result['total'] = $this->CountAllPages();

        return $result;
    }

    /**
     * Redirect to search() function
     *
     * @param array $args Arguments.
     * 
     * @return wiki pages
     */
    public function FullTextSearch($args)
    {
        return $this->Search($args);
    }
    
    /**
     * Search for a wiki page
     *
     * @param array $args Arguments.
     * 
     * @return wiki pages
     */
    public function Search($args)
    {
        
        if (!isset($args['phrase'])) {
            return LogUtil::registerArgsError();
        }
        
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->orderBy('p.time', 'DESC')
           ->where("p.latest = 'Y'" )
           ->setParameter('tag', '%'.$args['phrase'].'%');
        if (isset($args['fulltextsearch']) && $args['fulltextsearch']) {
            if (!$this->getVar('fulltextsearch', true)) {
                return LogUtil::registerError($this->__('Full text search is not enabled!'));
            }
            $qb->andWhere(
                    $qb->expr()->orx(
                        $qb->expr()->like('p.body', ':tag'),
                        $qb->expr()->like('p.tag',  ':tag')
                    )
                );
        } else {
            $qb->andWhere( $qb->expr()->like('p.tag', ':tag'));
        }
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
       

        // ToDo: Rewrite page permission
        foreach ($result as $key => $value) {
            if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$value['tag'], ACCESS_READ)) {
                unset($result[$key]);
            }
        }
                 
        return $result;

    }

    /**
     * Returns all wanted pages
     *
     * @return wiki pages
     */
    public function LoadWantedPages()
    {
        
        $pages0 = $this->LoadAllPages(array());
        $pages  = array();
        foreach ($pages0 as $key => $value) {
            $pages[] = $value['tag'];
        }
        
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('l')
           ->from('Wikula_Entity_Links2', 'l')
           ->groupBy('l.to_tag');
        $query = $qb->getQuery();
        $links = $query->getArrayResult();
        

        foreach ($links as $key => $value) {
            if (in_array($value['to_tag'], $pages)) {
                unset($links[$key]);
                unset($pages[$key]); 
            }
        }
                
        return $links;

    }
    
    
    /**
     * Returns all orphaned pages
     *
     * @param array $args Pages arguments.
     * 
     * @return array Wiki pages.
     */
    public function LoadOrphanedPages($args)
    {
        $args['orphaned'] = true;
        $args['orderBy'] = 'p.tag';  
        return $this->LoadPages($args);
    }
    
    
    /**
     * Check if a wiki page is orphaned
     *
     * @param string $tag Tag of a wiki page.
     * 
     * @return boolean
     */
    public function IsOrphanedPage($tag)
    {
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('count(l)')
           ->from('Wikula_Entity_Links2', 'l')
           ->where('l.to_tag = :tag')
           ->setParameter('tag', $tag);
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    /**
     * Save a new revision of a wiki page
     *
     * @param array $args Arguments.
     * 
     * @return boolean
     */
    public function SavePage($args)
    {
        
        extract($args);
        unset($args);
        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }
        

        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canEdit', $tag);

        $user = UserUtil::getVar('uname');

        // Check if page is new
        $oldpage =  ModUtil::apiFunc($this->name, 'user', 'LoadPage', array('tag' => $tag));

        // only save if new body differs from old body
        if ($oldpage && $oldpage['body'] == $body) {
            return LogUtil::registerError(__('The content of the page to save is the same of the current revision. New page not saved.'));
        }

        if (!$oldpage) {
            $owner = $user;
        } else {
            $owner = $oldpage['owner'];
        }

        // set all other revisions to old
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->update('Wikula_Entity_Pages', 'p')
           ->where("p.tag = :tag AND p.latest = 'Y'")
           ->setParameter('tag', $tag)
           ->set('p.latest', ':newlatest')
           ->setParameter('newlatest', 'N');
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
                
        
 
        // add new revision
        $tag = DataUtil::formatForStore($tag);
        $newrev = array(
            'tag'    => $tag,
            'body'   => $body,
            'note'   => DataUtil::formatForStore($note),
            'owner'  => $owner,
            'user'   => $user,
            'latest' => 'Y'
        );        
       

        $res = new Wikula_Entity_Pages();
        $res->merge($newrev);
        $this->entityManager->persist($res);


        if ($res === false) {
            return LogUtil::registerError(__('Saving revision failed!'));
        }


        $this->updateLinksAndCategories( array(
            'tag'  => $tag,
            'text' => $body
        ) );
        

        if (!$oldpage) {
             LogUtil::registerStatus(__('New page created!'));
             ModUtil::apiFunc($this->name, 'user', 'NotificateNewPage', $tag);
        } else {
             LogUtil::registerStatus(__('New revision saved!'));
             ModUtil::apiFunc($this->name, 'user', 'NotificateNewRevsion', $tag);
        }

        
        $this->entityManager->flush();

        
        return true;
    }
    
    /**
     * Update links and categories
     *
     * @param array $args Arguments.
     * 
     * @return boolean
     */
    public function updateLinksAndCategories($args) {
        
        $tag  = $args['tag'];
        $text = $args['text'];
        
        // remove old links
        $oldlinks = $this->entityManager->getRepository('Wikula_Entity_Links2')
                                        ->findBy(array('from_tag' => $tag));
        foreach ($oldlinks as $oldlink) {
            $this->entityManager->remove($oldlink);
            $this->entityManager->flush();
        }
        
        // remove old categories
        $oldcategories = $this->entityManager->getRepository('Wikula_Entity_Categories')
                                        ->findBy(array('tag' => $tag));
        foreach ($oldcategories as $oldcategory) {
            $this->entityManager->remove($oldcategory);
            $this->entityManager->flush();
        }
        
        // get new links and categories from the hook
        $hook = new Zikula_FilterHook(
            'wikula.filter_hooks.body.filter', 
            $text
        );
        $hook->setCaller('WikulaSaver');  
        $data = ServiceUtil::getManager()->getService('zikula.hookmanager')
                                        ->notify($hook)->getData(); 
        $pagelinks      = $data['links'];
        $pagecategories = $data['categories'];


        // set new links
        if (isset($pagelinks) && is_array($pagelinks) ) {
            foreach ($pagelinks as $pagelink) {
                $link = array(
                    'from_tag' => $tag,
                    'to_tag'   => $pagelink
                );
                $d = new Wikula_Entity_Links2();
                $d->merge($link);
                $this->entityManager->persist($d);
                $this->entityManager->flush();
            }
        }

        // set new categories
        if (isset($pagecategories) && is_array($pagecategories) ) {
            foreach ($pagecategories as $pagecategory) {
                $category = array(
                    'tag'      => $tag,
                    'category' => $pagecategory
                );
                $d = new Wikula_Entity_Categories();
                $d->merge($category);
                $this->entityManager->persist($d);
                $this->entityManager->flush();
            }
        }
        return true;
        
    }
    

    /**
     * Notificate user about a new wiki page
     *
     * @param string $tag Tag of a wiki page.
     * 
     * @return boolean
     */
    public function NotificateNewPage($tag)
    {

        if (empty($tag) || !$this->getVar('subscription') ) {
            return false;
        }

        // prepare email design
        $view = Zikula_View::getInstance($this->name, false);
        $view->assign('baseUrl', System::getBaseUrl());
        $view->assign('tag', $tag);
        $view->assign('uname', UserUtil::getVar('uname') );
        $message = $view->fetch('notification/newPage.tpl');
        $subject = $this->__('Wiki update');


        
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('s')
           ->from('Wikula_Entity_Subscriptions', 's');
        $query = $qb->getQuery();
        $users = $query->getArrayResult();
                
        // send emails        
        foreach ($users as $user) {
            $uid = $user['uid'];
            $toaddress = UserUtil::getVar('email', $uid);
            if (!empty($toaddress)) {
                ModUtil::apiFunc('Mailer', 'user', 'sendmessage', array(
                    'toaddress' => $toaddress,
                    'subject'   => $subject,
                    'body'      => $message,
                    'html'      => true
                ));
            }
        }
        return true;
        
    }

    /**
     * Notificate user about a new wiki page revision
     *
     * @param string $tag Tag of a wiki page.
     * 
     * @return boolean
     */
    public function NotificateNewRevsion($tag)
    {
        if (empty($tag) || !$this->getVar('subscription') ) {
            return false;
        }

        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->from('Wikula_Entity_Pages', 'p')
           ->select('p')
           ->where("p.user = :user AND p.latest = 'N'")
           ->setParameter('user', UserUtil::getVar('uname'))
           ->orderBy('p.time', 'desc')
           ->setMaxResults(1);
        $query = $qb->getQuery();
        $lastEdit = $query->getArrayResult();

        $notification = false;
        if (count($lastEdit) == 0 ) {
            $notification = true;
        } else {
            $lastEdit = $lastEdit[0]['time'];
            $timeDiff = DateUtil::getDatetimeDiff($lastEdit, DateUtil::getDatetime());
            $minutesSinceLastEdit = 0;
            if (array_key_exists('m', $timeDiff) ) {
                $minutesSinceLastEdit = $timeDiff['m'];
            }
            if (array_key_exists('h', $timeDiff) ) {
                $minutesSinceLastEdit = $minutesSinceLastEdit + $timeDiff['h'] * 60;

            }
            if (array_key_exists('d', $timeDiff) ) {
                $minutesSinceLastEdit = $minutesSinceLastEdit + $timeDiff['d'] * 60 * 24;
            }
            if ($minutesSinceLastEdit > 20) {
                $notification = true;
            }
        }
        
        

        if ($notification) {
            $view = Zikula_View::getInstance($this->name, false);
            $view->assign('baseUrl', System::getBaseUrl());
            $view->assign('tag', $tag);
            $view->assign('uname', UserUtil::getVar('uname') );
            $message = $view->fetch('notification/newRevision.tpl');
            $subject = $this->__('Wiki update');

            
            // find all subscriptions
            $em = $this->getService('doctrine.entitymanager');
            $qb = $em->createQueryBuilder();
            $qb->select('s')
               ->from('Wikula_Entity_Subscriptions', 's');
            $query = $qb->getQuery();
            $users = $query->getArrayResult();
            
                    
            // send emails
            foreach ($users as $user) {
                $uid = $user['uid'];
                $toaddress = UserUtil::getVar('email', $uid);
                if ($toaddress) {
                    ModUtil::apiFunc('Mailer', 'user', 'sendmessage', array(
                        'toaddress' => $toaddress,
                        'subject'   => $subject,
                        'body'      => $message,
                        'html'      => true
                    ));
                }
            }
        }
    }


    /**
     * Build a wiki link code
     * 
     * @param array $args Link array.
     *
     * @todo needs rework
     * @todo can we index all the Links and check if exists in the DB once?
     * 
     * @return string
     */
    public function Link($args)
    {
        
        if (!isset($args['tag'])) {
            return false;
        }

        if (!isset($args['text']) || empty($args['text'])) {
            // No text, we fill the page with at least its tag
            $args['text'] = $args['tag'];
        }
        if (!isset($args['title'])) {
            // No text, we fill the page with at least its tag
            $args['title'] = $args['tag'];
        }

        // is this an interwiki link?
        if (preg_match('/^([A-Z][A-Z,a-z]+)[:]([A-Z,a-z,0-9]*)$/s', $args['tag'], $matches)) {

            $link =  ModUtil::apiFunc($this->name, 'user', 'GetInterWikiUrl',
                                array('name' => $matches[1],
                                    'tag'  => isset($matches[2]) ? $matches[2] : ''));

            $textlink = (isset($matches[2]) && !empty($matches[2])) ? $matches[2] : $matches[1];

            return '<a class="ext" href="'.$link.'" title="'.$matches[1].' - '.$matches[2].'">'.$textlink.'</a><span class="exttail">&#8734;</span>';

        } else if (preg_match('/[^[:alnum:]]/', $args['tag'])) {

            // is this a full link? i.e., does it contain non alpha-numeric characters?
            // Note : [:alnum:] is equivalent [0-9A-Za-z]
            //        [^[:alnum:]] means : some caracters other than [0-9A-Za-z]
            // For example : "www.address.com", "mailto:address@domain.com", "http://www.address.com"

            // check for email addresses
            if (preg_match('/^.+\@.+$/', $args['tag'])) {
                // Building spam safe email link and text
                if ($args['text'] == $args['tag']) {
                    $args['text'] = htmlspecialchars(str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $args['text']));
                }
                $mailto = '&#109;&#97;&#105;&#108;&#116;&#111;&#58;';
                $address = htmlspecialchars($args['tag']);
                $address_encode = '';
                for ($x=0; $x < strlen($address); $x++) {
                    if (preg_match('!\w!',$address[$x])) {
                        $address_encode .= '%' . bin2hex($address[$x]);
                    } else {
                        $address_encode .= $address[$x];
                    }
                }
                $args['tag'] = $mailto . $address_encode;

            } else if (!preg_match('/:\/\//', $args['tag'])) {
                // check for protocol-less URLs
                $args['tag'] = 'http://'.$args['tag'];  // Very important for xss (avoid javascript:() hacking)
            }

            if ($args['text'] != $args['tag'] && preg_match('/.(gif|jpeg|png|jpg)$/i', $args['tag'])) {
                return '<img src="'.DataUtil::formatForDisplay($args['tag']).'" alt="'.DataUtil::formatForDisplay($args['text']).'" />';
            }

        } else {
            // it's a Wiki link!
            $pageid =  ModUtil::apiFunc($this->name, 'user', 'PageExists', $args['tag']);

            $linktable = SessionUtil::getVar('linktable');
            if (is_array(unserialize($linktable))) {
                $linktable = unserialize($linktable);
            }
            $linktable[] = $args['tag']; //$args['page']['tag'];
            SessionUtil::setVar('linktable', serialize($linktable));

            if (!empty($pageid)) {
                //$pnurl = urlencode( ModUtil::url('Wikula', 'user', 'main', array('tag' => $args['tag'])));
                //$text = DataUtil::formatForDisplay($args['text']);
                return '<a href="'. ModUtil::url('Wikula', 'user', 'main', array('tag' => DataUtil::formatForDisplay(urlencode($args['tag'])))).'" title="'.$args['text'].'">'.$args['text'].'</a>';
            } else {
                return '<span class="missingpage">'.$args['text'].'</span><a href="'. ModUtil::url('Wikula', 'user', 'edit', array('tag' => urlencode($args['tag']))).'" title="'.DataUtil::formatForDisplay($args['tag']).'">?</a>';
            }
        }

        // Non Wiki external link ?
        $external_link_tail = '<span class="exttail">&#8734;</span>';
        return !empty($args['tag']) ? '<a title="'.$args['text'].'" href="'.$args['tag'].'">'.$args['text'].'</a>'.$external_link_tail : $args['text']; //// ?????
    }



    /**
     * Action
     * 
     * @param array $args Action array.
     * 
     * @return mixed
     */
    public function Action($args)
    {
        
        if (!isset($args['action'])) {
            return LogUtil::registerError(__('Action argument missing!'));
        }

        $action = trim($args['action']);
        unset($args['action']);

        $vars   = array();
        // only search for parameters if there is a space
        if (strpos($action, ' ') !== false) {
            // treat everything after the first whitespace as parameter
            preg_match('/^([A-Za-z0-9]*)\s+(.*)$/', $action, $matches);

            // extract $action and $vars_temp ("raw" attributes)
            $action    = isset($matches[1]) ? $matches[1] : '';
            $vars_temp = isset($matches[2]) ? $matches[2] : '';

            if (!empty($action)) {
                // match all attributes (key and value)
                preg_match_all('/([A-Za-z0-9]*)="(.*)"/U', $vars_temp, $matches);

                // prepare an array for extract() to work with (in $this->IncludeBuffered())
                if (is_array($matches)) {
                    for ($a = 0; $a < count($matches[0]); $a++) {
                        $vars[$matches[1][$a]] = $matches[2][$a];
                    }
                }
                //$vars['wikka_vars'] = trim($vars_temp); // <<< add the buffered parameter-string to the array
            } else {
                return '<span class="error"><em>'.__f('Unknown action %s; the action name must not contain special characters', DataUtil::formatForDisplay($action)).'.</em></span>'; // <<< the pattern ([A-Za-z0-9])\s+ didn't match!
            }
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $action)) {
            return '<span class="error"><em>'.__f('Unknown action %s; the action name must not contain special characters', DataUtil::formatForDisplay($action)).'.</em></span>';
        }

        $vars = array_merge($args, $vars);


        // return the Action result
        return  ModUtil::apiFunc($this->name, 'action', strtolower($action), $vars);
    }

    /**
     * Check if a tag is valid 
     * 
     * @param string $tag Tag of a wiki page.
     * 
     * @return statement
     */
    public function CheckTag($tag = null)
    {
        if (is_null($tag)) {
            $tag = $this->getVar('root_page');
        }
        
        // redirect if tag contains spaces
        if (strpos($tag, ' ') !== false) {
            $arguments = array(
                'tag'  => str_replace(' ', '_', $tag),
            );
            $redirecturl = ModUtil::url($this->name, 'user', 'show', $arguments);
            System::redirect($redirecturl);
        }
        
        // redirect if tag contains spaces
        if (ModUtil::apiFunc($this->name, 'SpecialPage', 'isSpecialPage', $tag) ) {
            return System::redirect( ModUtil::url($this->name, 'user', 'main', array('tag' => $tag)) );
        }

    }
    
    
        
    /**
     * decode a short url
     */
    public function decodeurl($args)
    {
        // check we actually have some vars to work with...
        if (!isset($args['vars'])) {
            return LogUtil::registerArgsError();
        }
        
        
        if (isset($args['vars'][2])) {
            if ($args['vars'][2] == $this->__('Categories')) {
                System::queryStringSetVar('func', 'categories');
                return true;
            }
            
            System::queryStringSetVar('tag', $args['vars'][2]);
        }
        if (isset($args['vars'][3])) {
            System::queryStringSetVar('func', $args['vars'][3]);
        } else {
            System::queryStringSetVar('func', 'show');
        }
        
        return true;

    }
    
    
    
    /**
     * encode an url into a shorturl
     */
    public function encodeurl($vars)
    {
        $shorturl = $vars['modname'];
        
        if ($vars['func'] == 'categories') {
            return $shorturl.'/'.$this->__('Categories');
        }
        
        
        if (isset($vars['args']['tag'])) {
            $shorturl .= '/'.$vars['args']['tag'];            
        }
        
        if ($vars['func'] != 'main' && $vars['func'] != 'show') {
            $shorturl .= '/'.$vars['func'];
        }
        
        return $shorturl;
    }

    
}





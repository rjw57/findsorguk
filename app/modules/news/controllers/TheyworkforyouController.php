<?php

/**  Controller for accessing they work for you based news
 *
 * @category   Pas
 * @package    Pas_Controller_Action
 * @subpackage Admin
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version    1.1
 * @since      1/2/2012
 * @uses Pas_Twfy_Hansard
 * @uses Zend_Paginator
 * @uses Pas_Twfy_Person
 * @uses Pas_Twfy_Geometry
 * @uses Pas_Solr_Handler
 * @uses Pas_Exception_Param
 * @uses Pas_Twfy_Constituencies
 * @uses Pas_Solr_FieldGeneratorFinds
 * @uses Pas_Twfy_FindConstituency
 * @uses PostcodeForm
 *
 *
 */
class News_TheyworkforyouController extends Pas_Controller_Action_Admin
{

    /** Initialise contexts
     * @access public
     * @return void
     */
    public function init()
    {
        $this->_helper->_acl->allow(null);
//        $this->_helper->contextSwitch()->setAutoJsonSerialization(false);
//        $this->_helper->contextSwitch()
//            ->setAutoDisableLayout(true)
//            ->addContext('kml', array('suffix' => 'kml'))
//            ->addContext('rss', array('suffix' => 'rss'))
//            ->addContext('atom', array('suffix' => 'atom'))
//            ->addActionContext('finds', array('xml', 'json', 'kml', 'rss', 'atom'))
//            ->addActionContext('members', array('xml', 'json'))
//            ->addActionContext('constituencies', array('xml', 'json'))
//            ->addActionContext('index', array('xml', 'json'))
//            ->initContext();
    }

    /** Get the index page and results for PAS search of twfy
     * @uses Pas_Twfy_Hansard
     * @return void
     */
    public function indexAction()
    {
        $this->getFlash()->addMessage('Due to MySociety API charges, this service is no longer available.');
        $this->getResponse()->setHttpResponseCode(410)->setRawHeader('HTTP/1.1 Gone');
        $this->renderScript('pageGone.phtml');
        $term = $this->getParam('term');
        $search = $term ? $term : 'portable antiquities scheme';
        $twfy = new Pas_Twfy_Hansard();
        $twfy->setLimit(20);
        $twfy->setSearch($search);
        $twfy->setPage($this->getPage());
        $arts = $twfy->fetchData();
        $data = array();
        foreach ($arts->rows as $row) {
            $data[] = get_object_vars($row);
        }
        $pagination = array(
            'page' => (int)$this->getPage(),
            'perpage' => (int)$arts->info->results_per_page,
            'total_results' => (int)$arts->info->total_results
        );
        $paginator = Zend_Paginator::factory($pagination['total_results']);
        $paginator->setCurrentPageNumber($pagination['page'])->setItemCountPerPage($pagination['perpage']);
        $paginator->setCache($this->getCache());
        $this->view->data = $data;
        $this->view->paginator = $paginator;
    }

    /** Get data for a MP
     * @access public
     * @uses Pas_Twfy_Person
     * @throws Pas_Exception_Param
     * @return void
     */
    public function mpAction()
    {
        $this->getFlash()->addMessage('Due to MySociety API charges, this service is no longer available.');
        $this->getResponse()->setHttpResponseCode(410)->setRawHeader('HTTP/1.1 Gone');
        $this->renderScript('pageGone.phtml');
        if ($this->getParam('id', false)) {
            $person = new Pas_Twfy_Person();
            $unclean = $person->get($this->getParam('id'));
            $clean = array();
            foreach ($unclean as $object) {
                $mp = array();
                foreach ($object as $k => $v) {
                    if (!is_array($v)) {
                        $mp[$k] = $v;
                    } elseif (is_array($v)) {
                        $property = array();
                        $office = $v[0];
                        foreach($office as $k => $v){
                            $property[$k] = $v;
                        }
                    $mp['office'] = $property;
                    }
                }
                $clean[] = $mp;
            }
            $this->view->data = $clean;
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }

    /** Get the finds within a consituency
     * @uses Pas_Twfy_Geometry
     * @throws Pas_Exception_Param
     */
    public function findsAction()
    {
        $this->getFlash()->addMessage('Due to MySociety API charges, this service is no longer available.');
        $this->getResponse()->setHttpResponseCode(410)->setRawHeader('HTTP/1.1 Gone');
        $this->renderScript('pageGone.phtml');
        if ($this->getParam('constituency', false)) {
            $geo = new Pas_Twfy_Geometry();
            $const = urldecode($this->getParam('constituency'));
            $cons = $geo->get($const);
            $boundingBox = array(
                $cons->min_lat, $cons->min_lon, $cons->max_lat,
                $cons->max_lon
            );
            $search = new Pas_Solr_Handler();
            $search->setCore('objects');
            $context = $this->_helper->contextSwitch->getCurrentContext();
            $fields = new Pas_Solr_FieldGeneratorFinds($context);
            $search->setFields($fields->getFields());
            $params = $this->getAllParams();
            $params['bbox'] = implode(',', $boundingBox);
            $search->setFacets(array(
                'objectType', 'county', 'broadperiod',
                'institution', 'workflow'
            ));
            $search->setParams($params);
            $search->execute();
            $this->view->facets = $search->processFacets();
            $this->view->paginator = $search->createPagination();
            $this->view->finds = $search->processResults();
            $this->view->constituency = $const;
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }

    /** Get a list of constituencies
     * @uses Pas_Twfy_Constituencies
     * @access public
     * @return void
     */
    public function constituenciesAction()
    {
        $this->getFlash()->addMessage('Due to MySociety API charges, this service is no longer available.');
        $this->getResponse()->setHttpResponseCode(410)->setRawHeader('HTTP/1.1 Gone');
        $this->renderScript('pageGone.phtml');
        $cons = new Pas_Twfy_Constituencies();
        $data = $cons->get('2015-05-07');
        $clean = array();
        foreach ($data as $dat) {
            foreach ($dat as $k => $v) {
                $clean[] = array('name' => utf8_encode($v));
            }
        }
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($clean));
        $paginator->setCurrentPageNumber($this->getPage())->setItemCountPerPage(40);
        $paginator->setCache($this->getCache());
        $this->view->data = $paginator;
    }

    /** get a list of members of parliament
     * @uses Pas_Twfy_Mps
     * @uses Zend_Paginator
     * @access public
     */
    public function membersAction()
    {
        $this->getFlash()->addMessage('Due to MySociety API charges, this service is no longer available.');
        $this->getResponse()->setHttpResponseCode(410)->setRawHeader('HTTP/1.1 Gone');
        $this->renderScript('pageGone.phtml');
        $members = new Pas_Twfy_Mps();
        $data = $members->get();
        $clean = array();
        foreach ($data as $d) {
            $mp = array();
            foreach ($d as $k => $v) {
                $mp[$k] = utf8_encode($v);
            }
            $clean[] = (object)$mp;
        }
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($clean));
        $paginator->setCurrentPageNumber((int)$this->getPage());
        $paginator->setItemCountPerPage(30)->setPageRange(10);
        $paginator->setCache($this->getCache());
        $this->view->data = $paginator;
    }

    /** Get an MP based on a postcode
     * @access public
     * @return void
     */
    public function findmympAction()
    {
        $this->getFlash()->addMessage('Due to MySociety API charges, this service is no longer available.');
        $this->getResponse()->setHttpResponseCode(410)->setRawHeader('HTTP/1.1 Gone');
        $this->renderScript('pageGone.phtml');
        $constituency = new Pas_Twfy_FindConstituency();
        $form = new PostcodeForm();
        $form->removeElement('thumbnail');
        $form->removeElement('distance');
        $form->postcode->setLabel('Search by postcode');
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $place = $constituency->get($form->getValue('postcode'));
            }
            $this->redirect('/news/theyworkforyou/finds/constituency/' . $place->name);
        } else {
            $this->getFlash()->addMessage('Please check and correct errors!');
            $form->populate($this->_request->getPost());
        }
    }
}
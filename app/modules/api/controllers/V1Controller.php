<?php

class Api_V1Controller extends REST_Controller
{

	protected $_context = 'xml';
	
	public function init() {
	$this->_helper->_acl->allow(null);
	$this->_helper->layout()->disableLayout();
	$this->_helper->viewRenderer->setNoRender(true);
	$this->_helper->contextSwitch()->removeContext('html');
	$this->view->baseUrl = Zend_Registry::get('siteurl');
    }
    	
	
	public function indexAction(){
	$params = $this->_getAllParams();
	$search = new Pas_Solr_Handler();
        $search->setCore('objects');
	$fields = new Pas_Solr_FieldGeneratorFinds($this->_helper->contextSwitch->getCurrentContext());
	$search->setFields($fields->getFields());
	$search->setParams($params);
	$search->execute();
	$this->view->pagination = $this->createPagination($search->createPagination());
	$this->view->stats = $search->processStats();
	$this->view->results = $search->processResults();
	$this->view->params = $this->_getAllParams();
	$this->_response->ok();
    }
    
    private function createPagination($paginator){
    	$pagination = array(
    	'currentPage' => $paginator->getCurrentPageNumber(),
    	'totalResults' => $paginator->getTotalItemCount(), 
    	'resultsPerPage' => $paginator->getItemCountPerPage()
    	);
    	return $pagination;
    }
    
	public function headAction()
    {
    	$this->_response->ok();
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
    	$id  = $this->_getParam('id');
    	$this->view->message = $id;
    	$this->_response->ok();
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        $this->_response->notImplemented();
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        $this->_response->notImplemented();
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        $this->_response->notImplemented();
    }
}
<?php
/** Controller for displaying os opendata gazetteer
*
* @category   Pas
* @package Pas_Controller_Action
* @subpackage Admin
* @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
* @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
*/
class Datalabs_OsdataController extends Pas_Controller_Action_Admin
{

	protected $_contexts;

	/** Set up the ACL and contexts
	*/
	public function init(){
	$this->_helper->_acl->allow('public',null);
	
	$this->_contexts = array('xml','json');
	$this->_helper->contextSwitch()->setAutoJsonSerialization(false);
	$this->_helper->contextSwitch()->setAutoDisableLayout(true)
		->addActionContext('oneto50k',$this->_contexts)
		->addActionContext('index',$this->_contexts)
		->initContext();
	}

	const REDIRECT = 'datalabs/osdata/';

	/** Display a paginated list of OS data points
	*/
	public function indexAction() {
	$form = new SolrForm();
    $form->removeElement('thumbnail');
        $form->q->setLabel('Search OS open data: ');
    $form->q->setAttribs(array('placeholder' => 'Try barrow for instance'));
    $this->view->form = $form;

    $params = $this->_getAllParams();

    $search = new Pas_Solr_Handler();
    $search->setCore('beogeodata');
    $search->setFields(array('*'));
    $search->setFacets(array('county'));

    if($this->getRequest()->isPost() && $form->isValid($this->_request->getPost())
            && !is_null($this->_getParam('submit'))){

    if ($form->isValid($form->getValues())) {
    $params = $form->getValues();
	unset($params['csrf']);
    $this->_helper->Redirector->gotoSimple('index','osdata','datalabs',$params);
    } else {
    $form->populate($form->getValues());
    $params = $form->getValues();
    }
    } else {

    $params = $this->_getAllParams();
    $form->populate($this->_getAllParams());


    }

	$q = $this->_getParam('q');
	if(is_null($q)){
	$params['q'] = 'type:R OR type:A';
	} else {
		$params['q'] = 'type:R || type:A && ' . $q;
	}
	$params['source'] = 'osdata';
    $params['sort'] = 'id';
    $params['direction'] = 'asc';
    $search->setParams($params);
    $search->execute();
    $this->view->paginator = $search->createPagination();
    $this->view->results = $search->processResults();
	$this->view->facets = $search->processFacets();
    }

	/** Set up the one to 50k entry page
	*/
	public function oneto50kAction(){
	if($this->_getParam('id',false)){
	$gazetteers = new OsData();
	$this->view->gazetteer = $gazetteers->getGazetteer($this->_getParam('id'));
	} else {
		throw new Pas_Exception_Param($this->_missingParameter, 500);
	}
	}

	public function mapAction() {
	}
}


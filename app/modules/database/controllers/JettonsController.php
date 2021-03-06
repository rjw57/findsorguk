<?php
/** Controller for managing jettons etc
 * 
 * @category   Pas
 * @package Pas_Controller_Action
 * @subpackage Admin
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @version 1
 * @uses Coins
 * @uses Pas_Exception
 * @uses Pas_Exception_Param
 * @uses TokenJettonForm
 * @uses Finds
 * 
 */
class Database_JettonsController extends Pas_Controller_Action_Admin {
	
    /** The coins model
     * @access protected
     * @var \Coins
     */
    protected $_coins;
    
    /** Setup the contexts by action and the ACL.
     * @access public
     * @return void
     */	
    public function init()  {	
        $this->_helper->_acl->allow('member',array('add','edit','delete'));
        $this->_helper->_acl->allow('flos',null);
        $this->_coins = new Coins();
    }
    
    /** The redirect script
     * 
     */
    const REDIRECT = '/database/artefacts/';
    
    /** Redirect of the user due to no action existing.
     * @access public
     * @return void
     */
    public function indexAction() {
        $this->_helper->flashMessenger->addMessage('There is not a root action for jettons');
        $this->_redirect(Zend_Controller_Request_Http::getServer('referer'));
    }
    
    /** Add jetton data
     * @todo rewrite for audit etc
     * @throws Pas_Exception
     * @throws Pas_Exception_Param
     * @access public
     * @return void
     */
    public function addAction() {
        if( ($this->_getParam('broadperiod',false)) 
                && ($this->_getParam('findID',false) )){
            $this->_coins->checkCoinData($this->_getParam('findID'));
            $broadperiod = (string)$this->_getParam('broadperiod');
            switch ($broadperiod) {
                case 'MEDIEVAL':
                    $form = new TokenJettonForm();
                    $form->details->setLegend('Add Medieval jetton data');
                    $form->submit->setLabel('Add jetton data');
                    $this->view->headTitle('Add a Medieval jetton\'s details');
                    break; 
                case 'POST MEDIEVAL':
                    $form = new TokenJettonForm();
                    $form->details->setLegend('Add Post Medieval jetton data');
                    $form->submit->setLabel('Add jetton data');
                    $this->view->headTitle('Add a Post Medieval jetton\'s details');
                    break; 
                default:
                    throw new Pas_Exception('You cannot have a token for that period.');
            }		

            $last = $this->_getParam('copy');
            if($last == 'last') {
                $this->_helper->flashMessenger->addMessage('Your last record data has been cloned');
                $coindata = $this->_coins->getLastRecord($this->getIdentityForForms());
                foreach($coindata as $coindataflat){
                    $form->populate($coindataflat);
                }
            }
            $this->view->form = $form;
            if($this->getRequest()->isPost() && $form->isValid($this->_request->getPost())){
                if ($form->isValid($form->getValues())) {
                    $insertData = $form->getValues();
                    $insertData['secuid'] = $this->secuid();
                    $insertData['findID'] = $this->_getParam('findID');
                    $insert = $this->_coins->add($insertData);
                        $this->_helper->solrUpdater->update('beowulf', $this->_getParam('returnID'));
                    $this->_helper->flashMessenger->addMessage('Jetton data saved for this record.');
                    $this->_redirect(self::REDIRECT . 'record/id/' . $this->_getParam('returnID'));
                }  else {
                    $form->populate($this->_request->getPost());
                }
            }
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }

    /** Edit jetton data
     * @todo rewrite for audit etc
     * @access public
     * @return void
    */		
    public function editAction() {
        if($this->_getParam('id',false)){
            $finds = new Finds();
            $this->view->finds = $finds->getFindNumbersEtc($this->_getParam('returnID'));
            $broadperiod = (string)$this->_getParam('broadperiod');
            switch ($broadperiod) {
                case 'MEDIEVAL':
                    $form = new TokenJettonForm();
                    $form->details->setLegend('Edit Medieval jetton data');
                    $form->submit->setLabel('Save data');
                    $this->view->headTitle('Edit a Medieval jetton\'s details');
                    break; 
                case 'POST MEDIEVAL':
                    $form = new TokenJettonForm();
                    $form->details->setLegend('Edit Post Medieval jetton data');
                    $form->submit->setLabel('Save data');
                    $this->view->headTitle('Edit a Post Medieval jetton\'s details');
                    break; 
                default:
                    throw new Pas_Exception('You cannot have a jetton for that period.');
            }		
            $this->view->form = $form;
            if($this->getRequest()->isPost() 
                    && $form->isValid($this->_request->getPost())) {
                if ($form->isValid($form->getValues())) {
                    $updateData = $form->getValues();

                    $oldData = $this->_coins->fetchRow('id=' . $this->_getParam('id'))->toArray();

                    $where =  $this->_coins->getAdapter()->quoteInto('id = ?', $this->_getParam('id'));

                    $this->_coins->update($updateData, $where);

                    $this->_helper->audit($updateData, $oldData, 'CoinsAudit', 
                            $this->_getParam('id'), $this->_getParam('returnID'));

                    $this->_helper->flashMessenger->addMessage('Numismatic details updated.');

                    $this->_redirect(self::REDIRECT . 'record/id/' . $this->_getParam('returnID'));

                    $this->_helper->solrUpdater->update('beowulf', $this->_getParam('returnID'));

                } else {
                    $this->_helper->flashMessenger->addMessage('Please check your form for errors');
                    $form->populate($this->_request->getPost());
                }
            } else {
                // find id is expected in $params['id']
                $id = (int)$this->_getParam('id', 0);
                if (is_int($id)) {
                    $coin = $this->_coins->fetchRow('id=' . $this->_getParam('id'))->toArray();
                    $form->populate($coin);
                }
            }
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }
    /** Delete jetton data
     * @access public
     * @return void
    */	
    public function deleteAction() {
        if($this->_getParam('id',false)){
            if ($this->_request->isPost()) {
                $id = (int)$this->_request->getPost('id');
                $returnID = (int)$this->_request->getPost('returnID');
                $del = $this->_request->getPost('del');
                if ($del == 'Yes' && $id > 0) {
                    $where = 'id = ' . $id;
                    $this->_coins->delete($where);
                    $this->_helper->flashMessenger->addMessage('Numismatic data deleted!');
                    $this->_helper->solrUpdater->update('beowulf', $returnID);
                    $this->_redirect(self::REDIRECT.'record/id/' . $returnID);
                }
            } else {
                $id = (int)$this->_request->getParam('id');
                if ($id > 0) {
                    $this->view->coins = $this->_coins->getFindToCoinDelete($id);
                }
            }
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }
}
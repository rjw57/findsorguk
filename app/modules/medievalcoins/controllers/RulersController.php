<?php
/** Controller for displaying Medieval rulers pages
 * @category   Pas
 * @package Pas_Controller_Action 
 * @subpackage Admin
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @version 1
 * @uses Rulers
 * @uses Pas_Exception_Param
 * @uses Denominations
 * @uses MedievalTypes
 * @uses Mints
 * 
 */
class MedievalCoins_RulersController extends Pas_Controller_Action_Admin {

    /** The rulers model
     * @access protected
     * @var \Rulers
     */
    protected $_rulers;
    
    /** Setup the contexts by action and the ACL.
     * @access public
     * @return void
     */
    public function init() {
        $this->_helper->_acl->allow(null);
        $this->_helper->contextSwitch()->setAutoJsonSerialization(false);
        $this->_helper->contextSwitch()->setAutoDisableLayout(true)
                ->addActionContext('index', array('xml','json'))
                ->addActionContext('ruler', array('xml','json'))
                ->addActionContext('foreign', array('xml','json'))
                ->initContext();
        $this->_rulers = new Rulers();
    }
    /** Internal period ID number
     * @access protected
     * @var integer
     */
    protected $_period = 29;
    
    /** Index page for the list of rulers
     * @access public
     * @return void
     */
    public function indexAction() {
        $this->view->normans 	= $this->_rulers->getMedievalRulersListed('2','29');
        $this->view->shortlong 	= $this->_rulers->getMedievalRulersListed('14','29');
        $this->view->edwardian 	= $this->_rulers->getMedievalRulersListed('15','29');
        $this->view->latemed 	= $this->_rulers->getMedievalRulersListed('16','29');
    }
    /** Index page for list of foreign rulers
     * @access public
     * @return void
    */	
    public function foreignAction() {
        $this->view->ferengi = $this->_rulers->getMedievalRulersListed($this->_period,'29');
        $this->view->doges = $this->_rulers->getForeign($this->_period, $country = '1');
        $this->view->scots = $this->_rulers->getForeign($this->_period, $country = '2');
        $this->view->low = $this->_rulers->getForeign($this->_period, $country = '3');
        $this->view->imitate = $this->_rulers->getForeign($this->_period, $country = '4');
        $this->view->portugal = $this->_rulers->getForeign($this->_period, $country = '5');
        $this->view->shortlongs = $this->_rulers->getForeign($this->_period, $country = '6');
        $this->view->french = $this->_rulers->getForeign($this->_period, $country = '7');
    }
    /** Individual ruler pages
     * @access public
     * @return void
     */	
    public function rulerAction() {
        if($this->_getParam('id',false)){
            $id = (int)$this->_getParam('id');
            $this->view->id = $id;
            $this->view->rulers = $this->_rulers->getRulerImage($id);
            $this->view->monarchs = $this->_rulers->getRulerProfileMed($id);
            $denominations = new Denominations();
            $this->view->denominations = $denominations->getEarlyMedRulerToDenomination($id);
            $types = new MedievalTypes();
            $this->view->types = $types->getMedievalTypeToRuler($id);
            $mints = new Mints();
            $this->view->mints = $mints->getMedMintRuler($id);
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }
	
}

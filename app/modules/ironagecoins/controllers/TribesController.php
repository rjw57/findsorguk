<?php
/** Controller for Iron Age tribes
 *
 * @category   Pas
 * @package Pas_Controller_Action
 * @subpackage Admin
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @version 1
 * @uses Tribes
 * @uses Pas_Exception_Param
 */
class IronAgeCoins_TribesController extends Pas_Controller_Action_Admin {

    /** Setup the contexts by action and the ACL.
    */
    public function init() {
        $this->_helper->_acl->allow(null);
        $this->_helper->contextSwitch()->setAutoJsonSerialization(false);
        $this->_helper->contextSwitch()->setAutoDisableLayout(true)
                ->addActionContext('index', array('xml','json'))
                ->addActionContext('tribe', array('xml','json'))
                ->initContext();
        $this->_tribes = new Tribes();
    }

    /** Setup the index page for Iron Age tribes
     * @access public
     * @return void
     */
    public function indexAction() {
        $this->view->tribes = $this->_tribes->getTribesList();
    }
    /** Setup individual tribe page
     * @access public
     * @return void
     * @throws Pas_Exception_Param
     */
    public function tribeAction() {
        if($this->_getParam('id',false)) {
            $id = (int)$this->_getParam('id');
            $this->view->id = $id;
            $this->view->tribes = $this->_tribes->getTribe($id);
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }
}
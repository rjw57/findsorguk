<?php
/** Controller for displaying Early Medieval coin categories
 *
 * @category   Pas
 * @package    Pas_Controller_Action
 * @subpackage Admin
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @version 1
 * @uses CategoriesCoins
 * @uses MedievalTypes
 * @uses Pas_Exception_Param
*/
class EarlyMedievalCoins_CategoriesController extends Pas_Controller_Action_Admin
{
    /** The category model
     * @access protected
     * @var \CategoriesCoins
     */
    protected $_cats;
    
    
    /** Initialise the ACL and contexts
    */
    public function init()  {
        $this->_helper->_acl->allow(null);
        $this->_helper->contextSwitch()->setAutoJsonSerialization(false);
        $this->_helper->contextSwitch()
                ->setAutoDisableLayout(true)
                ->addActionContext('index', array('xml','json'))
                ->addActionContext('category', array('xml','json'))
                ->initContext();
        $this->_cats = new CategoriesCoins();
    }

    /** Internal period number for querying the database
     * @access protected
     * @var integer
     */
    protected $_period = 47;

    /** Set up index page for categories
     * @access public
     * @return void
     */
    public function indexAction() {
        $this->view->categories = $this->_cats->getCategoriesPeriod($this->_period);
    }

    /** Get details of each individual category
     * @access public
     * @throws Pas_Exception_Param
     * @return void
     */
    public function categoryAction() {
        if($this->_getParam('id', false)) {
            $id = (int)$this->_getParam('id');
            $this->view->categories = $this->_cats->getCategory($id);
            $types = new MedievalTypes();
            $this->view->types = $types->getCoinTypeCategory($id);
            $this->view->rulers = $this->_cats->getMedievalRulersToType($id);
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }
}
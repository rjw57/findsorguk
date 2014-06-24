<?php
/** Controller for all Annual and Treasure reports
* 
* @category   Pas
* @package    Pas_Controller
* @subpackage ActionAdmin
* @author     Daniel Pett <dpett@britishmuseum.org>
* @copyright  Daniel Pett <dpett@britishmuseum.org>
* @license    GNU General Public License
* @todo       Move adding data and editing into model
*/
class Publications_TreasureController extends Pas_Controller_Action_Admin
{
	protected $_cache = NULL;
	
	protected $_config = NULL;
	
	/** Initialise the ACL and contexts
	*/ 
	public function init(){
                $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->_helper->acl->allow('public',null);
		$this->_config = Zend_Registry::get('config');
		$this->_cache = Zend_Registry::get('rulercache');
	}


	/** Render treasure report pages
	*/ 
    public function indexAction() {
        $content = new Content();
        $this->view->contents = $content->getContent('treports',$this->_getParam('slug'));
        }
}

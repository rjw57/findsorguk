<?php
/** Controller for displaying Medieval index pages
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @version 1
 * @category   Pas
 * @package Pas_Controller_Action
 * @subpackage Admin
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @uses Content
 */
class MedievalCoins_IndexController extends Pas_Controller_Action_Admin {
    
    /** Setup the contexts by action and the ACL.
     * @access public
     * @return void
     */
    public function init() {
        $this->_helper->_acl->allow(null);
    }
    /** Setup the index page with examples and front blurb
     * @access public
     * @return void
     */
    public function indexAction() {
        $content = new Content();
        $this->view->content =  $content->getFrontContent('medievalcoins');
    }
}

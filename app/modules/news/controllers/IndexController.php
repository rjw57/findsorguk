<?php
/**  Controller for index of the news module
 *
 * @category   Pas
 * @package    Pas_Controller_Action
 * @subpackage Admin
 * @author     Daniel Pett <dpett@britishmuseum.org>
 * @copyright  Daniel Pett 2011 <dpett@britishmuseum.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 2
 * @since version 1
 * @uses News
 */
class News_IndexController extends Pas_Controller_Action_Admin {

    /** Initialise the ACL and contexts
     * @access public
     * @return void
     */
    public function init() {
        $this->_helper->_acl->allow(null);
	$this->_helper->contextSwitch()->setAutoJsonSerialization(false);
	$this->_helper->contextSwitch()
                ->setAutoDisableLayout(true)
                ->addContext('rss',array('suffix' => 'rss','header' => 'application/rss+xml'))
                ->addContext('atom',array('suffix' => 'atom','header' => 'application/atom+xml'))
                ->addActionContext('index', array('xml','json','rss','atom'))
                ->initContext();
    }

    /** Generate the list of news articles for the index page
     * @access public
     * @return void
     */
    public function indexAction() {
        $news = new News();
	$this->view->news = $news->getAllNewsArticles($this->_getAllParams());
    }
}
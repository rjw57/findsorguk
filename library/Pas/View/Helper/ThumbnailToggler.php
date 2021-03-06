<?php
/**
 * A view helper for toggling the thumbnails on and off.
 *
 * This view helper allows the user to choose whether the images are on or off
 * on the finds listers. If people took photos of everything, this would not be
 * needed!
 *
 * <code>
 * <?php
 * echo $this->thumbnailToggler();
 * ?>
 * </code>
 *
 * @author Daniel Pett <dpett@britishmuseum.org>
 * @copyright (c) 2014, Daniel Pett
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 1
 * @category Pas
 * @package Pas_View_Helper
 * @copyright (c) 2014, Daniel Pett
 * @example /app/modules/database/views/scripts/images/index.phtml
 * 
 */
class Pas_View_Helper_ThumbnailToggler extends Zend_View_Helper_Abstract
{
    /**
     * Request variable
     * @var object
     * @access protected
     */
    protected $_request;

    /** Get the request object
     * @access public
     * @return object The request parameters
     */
    public function getRequest() {
        $this->_request = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        return $this->_request;
    }

    /** The in active class
     * @access protected
     * @var string
     */
    protected $_inactive = 'inverse';

    /** The active class
     * @access protected
     * @var string
     */
    protected $_active = 'success';

    /** Get the inactive class
     * @access public
     * @return string
     */
    public function getInactive() {
        return $this->_inactive;
    }

    /** Get the active class
     * @access public
     * @return string
     */
    public function getActive() {
        return $this->_active;
    }

    /** Set the inactive class
     * @access public
     * @param string $inactive
     * @return \Pas_View_Helper_ThumbnailToggler
     */
    public function setInactive($inactive) {
        $this->_inactive = $inactive;
        return $this;
    }

    /** Set the active class
     * @access public
     * @param string $active
     * @return \Pas_View_Helper_ThumbnailToggler
     */
    public function setActive($active) {
        $this->_active = $active;
        return $this;
    }

    /** The function to return
     * @access public
     * @return \Pas_View_Helper_ThumbnailToggler
     */
    public function thumbnailToggler(){
        return $this;
    }

    /** The to string method
     * @access public
     * @return string
     */
    public function __toString() {
        $request = $this->getRequest();
        $html = '';
        $html .= '<div>Only results with images:<br />';
        if (array_key_exists('thumbnail', $request) && $request['thumbnail'] == '1') {
            $html .= '<a class="btn btn-small btn-';
            $html .= $this->getActive();
            $html .= '" href="';
            $html .= $this->view->url($request,'default',true);
            $html .= '">on</a> ';
            $html .= '<a class="btn btn-small btn-';
            $html .= $this->getInactive();
            $html .= '" href="';
            unset($request['thumbnail']);
            $html .= $this->view->url($request,'default',true);
            $html .= '">off</a>';
        } else {
            $html .= '<a class="btn btn-small btn-';
            $html .= $this->getInactive();
            $html .= '" href="';
            $html .= $this->view->url(array_merge($request, array('thumbnail' => 1)),'default',true);
            $html .= '">on</a> ';
            $html .= '<a class="btn btn-small btn-';
            $html .= $this->getActive();
            $html .= '" href="';
            $html .= $this->view->url($request,'default',true);
            $html .= '">off</a>';
        }
        $html .= '</div>';
        return $html;
     }
}
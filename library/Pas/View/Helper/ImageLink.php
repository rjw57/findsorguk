<?php

/**
 * This class is to help display links for edit or delete image functions
 *
 * It takes data from the finds model and checks it conditionally against
 * if/else statements and renders the link if possible.
 *
 * To use:
 *
 * <code>
 * <?php
 * echo $this->imageLink()
 * ->setInstitution($institution)
 * ->setSecuID($secuid)
 * ->setCreatedBy($createdBy)
 * ->setRecordType($type);
 * ?>
 * </code>
 *
 *
 * @category   Pas
 * @package    View
 * @subpackage Helper
 * @copyright  Copyright (c) 2011 dpett @ britishmuseum.org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @uses Zend_View_Helper_Abstract
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @since September 13 2011
 * @version 2
 *
 */
class Pas_View_Helper_ImageLink extends Zend_View_Helper_Abstract
{

    /** Set up the user groups with no access
     * @access protected
     * @var array $noaccess
     */
    protected $_noaccess = array('public', null);

    /** Set up the user groups with limited access
     * @access protected
     * @var array $restricted
     */
    protected $_restricted = array('member', 'research', 'hero');

    /** Set up the user groups with recorder access
     * @access protected
     * @var array $recorders
     */
    protected $_recorders = array('flos');

    /** Set up the user groups with higher level access
     * @access protected
     * @var array $higherLevel
     */
    protected $_higherLevel = array('admin', 'fa', 'treasure');

    /** The auth object
     * @access protected
     * @var object
     */
    protected $_auth;

    /** The creator
     * @access protected
     * @var int
     */
    protected $_createdBy;

    /** The institution of the recorder creator
     * @access protected
     * @var string
     */
    protected $_institution = 'PUBLIC';

    /** The institution of the user
     * @access protected
     * @var string
     */
    protected $_inst;

    /** The record's secuid
     * @access protected
     * @var string
     */
    protected $_secuid;

    /** The role of the user
     * @access protected
     * @var string
     */
    protected $_role = null;

    protected $_recordType = null;

    /**
     * @return null
     */
    public function getRecordType()
    {
        return $this->_recordType;
    }

    /**
     * @param null $recordType
     */
    public function setRecordType($recordType)
    {
        $this->_recordType = $recordType;
        return $this;
    }


    /** get the creator
     * @access public
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->_createdBy;
    }

    /** Get the institution
     * @access public
     * @return string
     */
    public function getInstitution()
    {
        return $this->_institution;
    }

    /** Get the secure ID
     * @access public
     * @return string
     */
    public function getSecuid()
    {
        return $this->_secuid;
    }

    /** Set created by
     * @access public
     * @param int $createdBy
     * @return \Pas_View_Helper_ImageLink
     */
    public function setCreatedBy($createdBy)
    {
        $this->_createdBy = $createdBy;
        return $this;
    }

    /** Set the institution
     * @access public
     * @param string $institution
     * @return \Pas_View_Helper_ImageLink
     */
    public function setInstitution($institution)
    {
        $this->_institution = $institution;
        return $this;
    }

    /** Set the secure ID
     * @access public
     * @param string $secuid
     * @return \Pas_View_Helper_ImageLink
     */
    public function setSecuid($secuid)
    {
        $this->_secuid = $secuid;
        return $this;
    }

    /** Get the user's role
     * @access public
     * @return string
     */
    public function getRole()
    {
        if ($this->getAuth()->hasIdentity()) {
            $user = $this->getAuth()->getIdentity();
            $this->_role = $user->role;
        }
        return $this->_role;
    }

    /** Get the auth object
     * @access public
     * @return object
     */
    public function getAuth()
    {
        $this->_auth = Zend_Registry::get('auth');
        return $this->_auth;
    }

    /** Set the find ID to query
     * @access public
     * @param int $findID
     * @return \Pas_View_Helper_CoinRefAddLink
     */
    public function setFindID($findID)
    {
        $this->_findID = $findID;
        return $this;
    }

    /** The findID
     * @access protected
     * @var int
     */
    protected $_findID;

    /** Get the findID
     * @access public
     * @return int
     */
    public function getFindID()
    {
        return $this->_findID;
    }

    /** The user id
     * @access protected
     * @var type
     */
    protected $_userID;

    /** Get the user's ID
     * @access public
     * @return int
     */
    public function getUserID()
    {
        if ($this->getAuth()->hasIdentity()) {
            $user = $this->getAuth()->getIdentity();
            $this->_userID = $user->id;
        }
        return $this->_userID;
    }

    /** Get the user's institution
     * @access public
     * @return string
     */
    public function getInst()
    {
        if ($this->getAuth()->hasIdentity()) {
            $user = $this->getAuth()->getIdentity();
            $this->_inst = $user->institution;
        }
        return $this->_inst;
    }

    /** Check whether access is allowed by userid for that record
     *
     * This function conditionally checks to see if a user is in the restricted
     * group and then checks whether they created the record. If true, they can
     * edit it.
     *
     * @access public
     * @param int $createdBy
     * @return boolean
     */
    public function checkAccessbyUserID($createdBy)
    {
        if (in_array($this->getRole(), $this->_restricted)) {
            if ($createdBy == $this->getUserID()) {
                $allowed = true;
            } else {
                $allowed = false;
            }
        }
        return $allowed;
    }

    /** Check institutional access by user's institution
     *
     * This function conditionally checks whether a user's institution allows
     * them editing rights to a record.
     *
     * First condition: if role is in recorders array and their institution is
     * the same, then allow.
     *
     * Second condition: if role is in higher level, then allow
     *
     * Third condition: if role is in restricted (public) and they created,
     * then allow.
     *
     * Fourth condition: if role is in restricted and institution is public,
     * then allow.
     *
     * @access public
     * @param string $institution
     * @return boolean
     *
     */
    public function checkAccess()
    {
        // If role = public return false
        if (in_array($this->getRole(), $this->_noaccess)) {
            return false;
        }
        //If role in restricted and created = created by return true
        else if (in_array($this->getRole(), $this->_restricted) && $this->getCreatedBy() == $this->getUserID()) {
            return true;
        }
        //If role in recorders and institution = inst or created by = created return true
        else if (in_array($this->getRole(), $this->_recorders) && $this->getInst() == $this->getInstitution()
            || $this->getCreatedBy() == $this->getUserID()
            || in_array($this->getRole(), $this->_recorders) && $this->getInstitution() == 'PUBLIC') {
            return true;
        }
        //If role in higher level return true
        else if (in_array($this->getRole(), $this->_higherLevel)) {
            return true;
        } else {
            return false;
        }
    }

    /** The function to return
     * @access public
     * @return \Pas_View_Helper_ImageLink|boolean
     */
    public function ImageLink()
    {
        return $this;
    }

    /** To string function
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->generateLink();
    }

    /** Generate the link
     * @access public
     * @return string
     */
    public function generateLink()
    {
        $html = '';
        if ($this->checkAccess()) {
            $html .= $this->buildHtml();
        }
        return $html;
    }

    /** Build just the url
     * @access public
     * @return string
     */
    public function urlBuild()
    {
        $url = array(
            'module' => 'database',
            'controller' => 'images',
            'action' => 'add',
            'secuid' => $this->getSecuID(),
            'id' => $this->getFindID(),
            'recordtype' => $this->getRecordType()
        );
        return $url;
    }

    /** Build the html
     * @access public
     * @return string
     */
    public function buildHtml()
    {
        $url = $this->view->url($this->urlBuild(), null, true);
        $html = '';
        $html .= '<a class="btn btn-small btn-primary" href="';
        $html .= $url;
        $html .= '" title="Add an image to this find">';
        $html .= 'Add an image</a>';
        return $html;
    }

}

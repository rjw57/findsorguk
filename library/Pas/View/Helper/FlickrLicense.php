<?php
/** A view helper for displaying a flickr licence
 *
 * An example of use:
 * <code>
 * <?php
 * echo $this->flickrLicense()->setLicense(2);
 * ?>
 * </code>
 *
 * @category   Pas
 * @package    Pas_View_Helper
 * @subpackage Abstract
 * @copyright  Copyright (c) 2011 dpett @ britishmuseum.org
 * @license    GNU
 * @version 1
 * @example /app/modules/flickr/views/scripts/photos/details.phtml
 * @author Daniel Pett <dpett@britishmuseum.org>
 *
 */
class Pas_View_Helper_FlickrLicense extends Zend_View_Helper_Abstract
{
    /**
     * The all rights reserved string
     *
     */
    const ALLRIGHTS = 'All Rights Reserved';

    /**
     * Base of the creative commons license url
     */
    const BASECREATIVE = 'http://creativecommons.org/licenses/';

    /**
     *  Text for view license
     */
    const VIEWLIC = 'View license restrictions';

    /** The license variable
     * @access protected
     * @var int
     */
    protected $_license;

    /** Get the license
     * @access public
     * @return int
     */
    public function getLicense() {
        return $this->_license;
    }

    /**
     * Set the license
     * @access public
     * @param int $license
     * @return \Pas_View_Helper_FlickrLicense
     */
    public function setLicense($license) {
        $this->_license = $license;
        return $this;
    }

    /** Get the cache object
     * @access public
     * @return \Zend_Cache
     */
    public function getCache() {
        $this->_cache = Zend_Registry::get('cache');
        return $this->_cache;
    }

    /** The cache object
     * @access public
     * @var \Zend_Cacge
     */
    protected $_cache;

    /** Function to return
     * @access public
     * @return \Pas_View_Helper_FlickrLicense
     */
    public function flickrLicense(){
        return $this;
    }

    /** Build the html to return to string
     * @access public
     * @param int $license
     * @return type
     */
    public function getHtmlLicense( $license ){
        if (!($this->getCache()->test('cclicense' . $license))) {
            switch ($license) {
                case 0:
                    $licensetype = self::ALLRIGHTS;
                    break;
                case 1:
                    $licensetype = '<a href="';
                    $licensetype .= self::BASECREATIVE;
                    $licensetype .= 'by-nc-sa/2.0/" title="';
                    $licensetype .= self::VIEWLIC;
                    $licensetype .= '">Attribution-NonCommercial-ShareAlike License</a>';
                    break;
                case 2:
                    $licensetype = '<a href="';
                    $licensetype .= self::BASECREATIVE;
                    $licensetype .= 'by-nc/2.0/" title="';
                    $licensetype .= self::VIEWLIC;
                    $licensetype .= '">Attribution-NonCommercial License</a>';
                    break;
                case 3:
                    $licensetype = '<a href="';
                    $licensetype .= self::BASECREATIVE;
                    $licensetype .= 'by-nc-nd/2.0/" title="';
                    $licensetype .= self::VIEWLIC;
                    $licensetype .= '">Attribution-NonCommercial-NoDerivs License</a>';
                    break;
                case 4:
                    $licensetype = '<a href="';
                    $licensetype .= self::BASECREATIVE;
                    $licensetype .= 'by/2.0/" title="';
                    $licensetype .= self::VIEWLIC;
                    $licensetype .= '">Attribution License</a>';
                    break;
                case 5:
                    $licensetype = '<a href="';
                    $licensetype .= self::BASECREATIVE;
                    $licensetype .= 'by-sa/2.0/" title="';
                    $licensetype .= self::VIEWLIC;
                    $licensetype .= '">Attribution-ShareAlike License</a>';
                    break;
                case 6:
                    $licensetype = '<a href="';
                    $licensetype .= self::BASECREATIVE;
                    $licensetype .= 'by-nd/2.0/" title="';
                    $licensetype .= self::VIEWLIC;
                    $licensetype .= '">Attribution-NoDerivs License</a>';
                    break;
                case 7:
                    $licensetype =  '<a href="http://www.flickr.com/commons/usage/" title="';
                    $licensetype .= self::VIEWLIC;
                    $licensetype .= '">No known copyright restrictions</a>';
                    break;
                case 8:
                    $licensetype =  '<a href="http://www.usa.gov/copyright.shtml" title="';
                    $licensetype .= self::VIEWLIC;
                    $licensetype .= '">United States Government Work</a>';
                    break;
                default:
                    $licensetype = self::ALLRIGHTS;
                    break;
            }
            $this->getCache()->save($licensetype);
            } else {
                $licensetype = $this->getCache()->load('cclicense' . $license);
        }
        return $licensetype;
    }

    /** To string function
     * @access public
     * @return string
     */
    public function __toString() {
        return $this->getHtmlLicense($this->getLicense());
    }
}
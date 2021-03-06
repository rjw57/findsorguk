<?php
/**
 * An action helper for sending updates to our solr instances via the controller
 *
 * An example of use:
 *
 * <code>
 * <?php
 * $this->_helper->solrUpdater->update($model, $insertData, $type);
 * ?>
 * </code>
 *
 * @author Daniel Pett <dpett@britishmuseum.org>
 * @copyright (c) 2014 Daniel Pett
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @category Pas
 * @package Controller_Action
 * @subpackage Helper
 * @version 1
 *
 */
class Pas_Controller_Action_Helper_SolrUpdater
    extends Zend_Controller_Action_Helper_Abstract {

    /** The list of cores available
     * @access protected
     * @var array
     * @todo change core names through out site
     */
    protected $_cores = array(
        'beowulf', 'beopeople', 'beoimages',
        'beopublications','beobiblio','beocontent'
        );

    /** The solr object
     * @access protected
     * @var
     */
    protected $_solr;

    /** The config object
     * @access protected
     * @var \Zend_Config
     */
    protected $_config;

    /** The constructor
     * @access public
     */
    public function __construct(){
        $this->_config = Zend_Registry::get('config')->solr->toArray();
    }

    /** Get the solr configuration for a core
     * @access public
     * @param string $core
     * @return \Solarium_Client
     * @throws Exception
     */
    public function getSolrConfig($core){
        if(in_array($core, $this->_cores)){
            $solrAdapter = $this->_config;
            $solrAdapter['core'] = $core;
            $solr = new Solarium_Client(
                    array(
                        'adapteroptions' =>
                        $solrAdapter
                    ));
            return $solr;
        } else {
            throw new Exception('That core does not exist', 500);
        }
    }

    /** Update a core
     * @access public
     * @param string $core
     * @param integer $id
     * @param string $type
     * @return integer
     */
    public function update($core, $id, $type = null){
        $data = $this->getUpdateData($core, $id, $type);
        $this->_solr = $this->getSolrConfig($core);
        $update = $this->_solr->createUpdate();
        $doc = $update->createDocument();
        foreach($data as $k => $v){
            $doc->$k = $v;
        }
        $update->addDocument($doc);
        $update->addCommit();
        return $this->_solr->update($update);
    }

    /** Delete by an id number
     * @access public
     * @param string $core
     * @param integer $id
     * @return integer
     */
    public function deleteById($core, $id){
        $this->_solr = $this->getSolrConfig($core);
        $update = $this->_solr->createUpdate();
        $update->addDeleteByID( $this->_getIdentifier($core) . $id);
        $update->addCommit();
        return  $this->_solr->update($update);
    }

    /** Get the preferred identifier by core
     * @access protected
     * @param string $core
     * @return string
     * @throws Exception
     */
    protected function _getIdentifier($core){
	if(in_array($core, $this->_cores)){
            switch($core) {
                case 'beowulf':
                    $identifier = 'finds-';
                    break;
                case 'beopeople':
                    $identifier = 'people-';
                    break;
                case 'beocontent':
                    $identifier = 'content-';
                    break;
                case 'beobiblio':
                    $identifier = 'biblio-';
                    break;
                case 'beoimages':
                    $identifier = 'images-';
                    break;
                case 'beopublications':
                    $identifier = 'publications-';
                    break;
                default:
                    throw new Exception('Your core does not exist',500);
		}
		return $identifier;
	} else {
            throw new Exception('That core does not exist', 500);
	}
    }

    /** Get update data for a core
     * @access public
     * @param string $core
     * @param integer $id
     * @param boolean $type
     * @return array
     * @throws Exception
     */
    public function getUpdateData($core, $id, $type = null){
	if(in_array($core, $this->_cores)){
            switch($core){
                case 'beowulf':
                    $model = new Finds();
                    break;
                case 'beopeople':
                    $model = new People();
                    break;
                case 'beocontent':
                    $type = ucfirst($type);
                    $model = new $type;
                    break;
                case 'beobiblio':
                    $model = new Bibliography();
                    break;
                case 'beoimages':
                    $model = new Slides();
                    break;
                case 'beopublications':
                    $model = new Publications();
                    break;
                default:
                    throw new Exception('Your core does not exist',500);
            }
            $data = $model->getSolrData($id);
            $cleanData = $this->cleanData($data[0]);
            return $cleanData;
	} else {
            throw new Exception('That core does not exist',500);
	}
    }

    /** Clean the data up
     * @access public
     * @param array $data
     * @return array
     */
    public function cleanData(array $data){
	if(array_key_exists('datefound1',$data)){
            if(!is_null($data['datefound1'])) {
                $df1 = $data['datefound1'] . 'T00:00:00Z';
                $data['datefound1'] = $df1;
            } else {
                $data['datefound1'] = null;
            }
	}
	if(array_key_exists('datefound2',$data)){
            if(!is_null($data['datefound2'])) {
                $df2 = $data['datefound2'] . 'T00:00:00Z';
                $data['datefound2'] = $df2;
            } else {
                $data['datefound2'] = null;
            }
	}
	if(array_key_exists('created',$data)){
            if(!is_null($data['created'])) {
                $created = $this->todatestamp($data['created']);
		$data['created'] = $created;
            } else {
                $data['created'] = null;
            }
	}
	if(array_key_exists('updated',$data)){
            if(!is_null($data['updated'])) {
                $updated = $this->todatestamp($data['updated']);
                $data['updated'] = $updated;
            } else {
                $data['updated'] = null;
            }
	}
	foreach($data as $k => $v){
            $data[$k] = strip_tags($v);
            if (is_null($v) || $v === "") {
                unset($data[$k]);
            }
	}
	return $data;
    }


    /** Format the date and return as unix stamp
     * @access public
     * @param Zend_Date $date
     * @return string
     */
    public function todatestamp($date) {
        $st = strtotime($date);
        $zendDate = new Zend_Date();
        $zendDate->set($st);
        return substr($zendDate->get(Zend_Date::W3C), 0, -6) . 'Z';
    }
}
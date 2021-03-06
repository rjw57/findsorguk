<?php
/** A model for manipulating the bibliograpic data;
 * Books are stored in publications table
 *
 * An example of use:
 *
 * <code>
 * <?php
 * $model = new Bibliography();
 * $data = $model->fetchFindBook($id);
 * ?>
 * </code>
 * @author Daniel Pett <dpett@britishmuseum.org>
 * @copyright (c) 2014, Daniel Pett
 * @category Pas
 * @package Db_Table
 * @subpackage Abstract
 * @copyright 2010 - DEJ Pett
 @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 1.0
 * @since 22 September 2011
 * @example /app/modules/database/controllers/ReferencesController.php
*/

class Bibliography extends Pas_Db_Table_Abstract {

    /** The table name
     * @access protected
     * @var string
     */
    protected $_name = 'bibliography';

    /** The primary key
     * @access protected
     * @var int
     */
    protected $_primary = 'id';

    /** Get cached data for a book
     * @access public
     * @param integer $id
     * @return array
     */
    public function fetchFindBook($id){
    	if (!$data = $this->_cache->load('bibliobook' . (int)$id)) {
        $refs = $this->getAdapter();
        $select = $refs->select()
                ->from($this->_name, array('pages_plates','reference','pubID'))
                ->joinLeft('publications','publications.secuid = bibliography.pubID',
                        array('publicationtitle' => 'title', 'authors'))
                ->joinLeft('finds','finds.secuid = bibliography.findID', array('id'))
                ->where($this->_name . '.id = ?', $id);
        $data = $refs->fetchAll($select);
    	$this->_cache->save($data, 'bibliobook' . (int)$id);
    	}
        return $data;
    }
}

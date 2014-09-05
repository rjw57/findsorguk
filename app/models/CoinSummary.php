<?php

/** Access, manipulate and delete coin summary data.
 *
 * An example of use:
 *
 * <code>
 * <?php
 * $model = new CoinSummary();
 * $data = $model->getCoinSummary($id);
 * ?>
 * </code>
 *
 * @author Mary Chester-Kadwell <mchester-kadwell at britishmuseum.org>
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @copyright (c) 2014 Mary Chester-Kadwell
 * @copyright (c) 2014 Daniel Pett
 * @category Pas
 * @package Db_Table
 * @subpackage Abstract
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 1
 * @since 4 August 2014
 * @example /app/modules/database/controllers/HoardsController.php
 */
class CoinSummary extends Pas_Db_Table_Abstract {

    /** The table name
     * @access protected
     * @var string
     */
    protected $_name = 'coinsummary';

    /** The primary key
     * @access protected
     * @var integer
     */
    protected $_primary = 'id';

    /** Get coin summary for a hoard record
    * @access public
    * @param integer $hoardId
    * @return array
    * @todo Make this work properly for all periods of coinage
    */
    public function getCoinSummary($hoardId){
        $select = $this->select()
            ->from($this->_name, array(
                'hoardID',
                'broadperiod',
                'denomination',
                'geographyID',
                'ruler_id',
                'mint_id',
                'numdate1',
                'numdate2',
                'quantity'
            ))
            ->joinLeft('hoards','coinsummary.hoardID = hoards.secuid',
                array(
                    'id'))
            ->joinLeft('denominations','coinsummary.denomination = denominations.id',
                array(
                'denomination'))
            ->joinLeft('rulers','coinsummary.ruler_id = rulers.id',
                array(
                'ruler' => 'issuer'))
            ->joinLeft('mints','coinsummary.mint_id = mints.id',
                array(
                'mint' => 'mint_name'))
            ->joinLeft('geographyironage','coinsummary.geographyID = geographyironage.id',
                array(
                    'geographicarea' => 'region'))
            ->where('hoards.id = ?', (int)$hoardId);
            $select->setIntegrityCheck(false);
        return $this->getAdapter()->fetchAll($select);

    }
}
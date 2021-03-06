<?php
/** Form for manipulating numismatic mint data 
 * 
 * An example of code:
 * 
 * <code>
 * <?php
 * $form = new ReeceEmperorForm();
 * $form->submit->setLabel('Submit details');
 * ?>
 * </code>
 * 
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @category   Pas
 * @package    Pas_Form
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 1
 * @uses Reeces
 * @example /app/modules/admin/controllers/NumismaticsController.php
*/
class ReeceEmperorForm extends Pas_Form {

    /** The constructor
     * @access public
     * @param array $options
     * @return void
     */
    public function __construct(array $options = null) {
	
	$periods = new Reeces();
	$period_actives = $periods->getReeces();

	parent::__construct($options);

	$this->setName('reeceEmpeor');

	$reece_period = new Zend_Form_Element_Select('reeceperiod_id');
	$reece_period->setLabel('Reece Period: ')
                ->setRequired(true)
		->addValidator('Alnum',false, array('allowWhiteSpace' => true))
		->addFilters(array('StripTags','StringTrim'))
		->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow')
		->addMultiOptions(array(
                    null => 'Choose period',
                    'Available periods:' => $period_actives
                ))
		->addValidator('InArray', false, array(array_keys($period_actives)));

		//Submit button 
	$submit = new Zend_Form_Element_Submit('submit');

	$hash = new Zend_Form_Element_Hash('csrf');
	$hash->setValue($this->_salt)->setTimeout(4800);
	
	$this->addElements(array($reece_period, $hash,$submit));
	
	$this->addDisplayGroup(array(
            'reeceperiod_id', 'period', 'valid', 
            'submit'), 'details');
	
	$this->details->setLegend('Reece period to emperor: ');
	
	parent::init();
    }
}
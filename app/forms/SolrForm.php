<?php
/** 
 * Form for solr based single word search
 * 
 * An example of use:
 * 
 * <code>
 * <?php
 * $form = new SolrForm();
 * ?>
 * </code>
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @version 1
 * @category   Pas
 * @package    Pas_Form
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @example /app/modules/database/controllers/IndexController.php
*/
class SolrForm extends Pas_Form {
    
    /** The init function
     * @access public
     * @return void
     */
    public function init(){
	$this->setName('solr');

	$q = new Zend_Form_Element_Text('q');
	$q->setLabel('Search content: ')
		->setRequired(true)
		->addFilters(array('StripTags','StringTrim'))
		->setAttrib('class', 'span10')
		->setAttrib('placeholder', 'Try coin for example')
		->addErrorMessage('Please enter a search term');

        $thumbnail = new Zend_Form_Element_Checkbox('thumbnail');
        $thumbnail->setLabel('Only with images?')->setUnCheckedValue(null);

	$submit = new Zend_Form_Element_Submit('submit');
	$submit->setLabel('Search!');

	$hash = new Zend_Form_Element_Hash('csrf');
	$hash->setValue($this->_salt)
		->removeDecorator('DtDdWrapper')
		->removeDecorator('HtmlTag')
		->removeDecorator('label')
		->setTimeout(4800);

	$this->addElements(array($q, $thumbnail, $submit, $hash ));
	parent::init();
    }
}
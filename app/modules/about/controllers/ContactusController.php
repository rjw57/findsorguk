<?php
/**  An action controller for handling the submission of comments and questions
 * 
 * @package Pas_Controller_Action
 * @subpackage Admin
 * @category Pas
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @copyright (c) 2014 Daniel Pett
 * @version 1
 * @since 13th January 2011
 * @uses ContactUsForm
 * @uses Messages
 *
 */
class About_ContactUsController extends Pas_Controller_Action_Admin {

    /** Initialize the acl helper and messenger
     * @access public
     */
    public function init() {
	
	$this->_helper->acl->allow('public',null);
        
    }

    /** The action that allows people to send in requests via the contact us 
     * form
     * @access public
     * @return void
     */
    public function indexAction() {
	$form = new ContactUsForm();
        $form->removeElement('captcha');
	$this->view->form = $form;
	if($this->getRequest()->isPost() && $form->isValid($this->_request->getPost())){
            if ($form->isValid($form->getValues())) {
                $insertData = $form->getValues();
                $messages = new Messages();
                $messages->addComplaint($insertData);
                $cc = array();
                $cc[] = array(
                    'email' => $form->getvalue('comment_author_email'),
                    'name' => $form->getValue('comment_author')
		);
                $this->_helper->mailer($insertData, 'contactUs', null, $cc, $cc);
                $this->getFlash()->addMessage('Your enquiry has been submitted to the Scheme');
                $this->_redirect('about/contactus/');
            } else {
                $this->getFlash()->addMessage('There are problems with your submission');
                $form->populate($form->getValues());
            }
	}
    }
}
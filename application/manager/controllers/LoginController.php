<?php
/**
 * Login
 */
class Manager_LoginController extends BaseController {
    /**
     * Default
     */
    public function indexAction() {
        $form = $this->view->form;

        if ( $this->getRequest()->isPost() ) {
            if ( $this->indexValid( $form ) ) {
                // go back to it when there is a return destination
                if ( $this->getRequest()->getParam('return_path') ) {
                    $this->_redirect( $this->getRequest()->getParam('return_path') );
                }
                // The management screen to the top when there is no
                else {
                    $this->_redirect('/manager');
                }
            } else {
                $this->view->error_str = "Username atau Password yang dimasukkan salah.";
            }
        }
        else {
            $form->getElement('return_path')->setValue( $this->getRequest()->getParam('return_path') );
        }

        $this->view->subtitle = 'Login';
    }

    /**
     * Login check
     */
    private function indexValid($form) {
        if (!$form->isValid($_POST)) {
            return false;
        }
        
        $models = $this->model('Dao_Admin')->search(array(
            'account  = ?' => $form->getValue('login_id'),
            'password = ?' => $form->getValue('login_pw')
        ));
        if (count($models)) {
            $session = new Zend_Session_Namespace('admin');
            $session->id = $models->current()->id;
        } else {
            return false;
        }
    
        return true;
    }
	
    /**
     * Called just before the action is performed
     */
    public function preDispatch() {
        // want to change to html extension of view
        $this->_helper->viewRenderer->setViewSuffix('html');
        
        // Read config file
        $config = Zend_Registry::get('config');
        
        // Common information
        $this->view->app = $config->app;
        
        // Automatic generation of form
        $this->createForm();
    }
}

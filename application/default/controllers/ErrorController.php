<?php
/**
 * Error
 */
class ErrorController extends BaseController {
    /**
     * error
     */
    public function errorAction() {
    }
	
    /**
     * Called just before the action is performed
     */
    public function preDispatch() {
        // change to html view
        $this->_helper->viewRenderer->setViewSuffix('html');
        
        // get config
        $config = Zend_Registry::get('config');
        $this->view->app = $config->app;
    }
}

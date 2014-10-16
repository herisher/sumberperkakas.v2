<?php
/**
 * index
 */
class IndexController extends BaseController {
    /**
     * index
     */
    public function indexAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
    }
}

<?php
/**
 * トップ
 */
class Manager_IndexController extends BaseController {
    /**
     * デフォルト
     */
    public function indexAction() {
    }

    /**
     * システム情報
     */
    public function sysinfoAction() {
        // ビューを無効にする
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        phpinfo();
    }
}

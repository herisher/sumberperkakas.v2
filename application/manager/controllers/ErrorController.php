<?php
/**
 * エラー
 */
class Manager_ErrorController extends BaseController {
    /**
     * デフォルト
     */
    public function errorAction() {
    }

    /**
     * 各アクションが実行される直前に呼ばれる
     */
    public function preDispatch() {
        // ビューの拡張子をhtmlに変更する
        $this->_helper->viewRenderer->setViewSuffix('html');
        
        // コンフィグファイルの読み出し
        $config = Zend_Registry::get('config');
        $this->view->app = $config->app;
    }
}

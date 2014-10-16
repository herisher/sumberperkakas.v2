<?php
/**
 * ログアウト
 */
class Manager_LogoutController extends BaseController {
    /**
     * ログアウト
     */
    public function indexAction() {
        // セッションをクリア
        session_unset();
        session_regenerate_id(true);

        // ログインページへ
        $this->_redirect('/manager/login');
    }

    /**
     * 各アクションが実行される直前に呼ばれる
     */
    public function preDispatch() {
        // ビューの拡張子をhtmlに変更する
        $this->_helper->viewRenderer->setViewSuffix('html');
        
        // コンフィグファイルの読み出し
        $config = Zend_Registry::get('config');
        
        // 共通情報
        $this->view->app = $config->app;
    }
}

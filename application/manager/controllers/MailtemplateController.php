<?php
/**
 * メール管理
 */
class Manager_MailtemplateController extends BaseController {
	const NAMESPACE_LIST = '/manager/mailtemplate/list';

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Mailtemplate');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value ) {
                    $where['id = ?'] = $value;
                }
            }
        }
        return $table->createWherePhrase($where, $order_by);
    }

    /**
     * 検索条件復元
     */
    private function restoreSearchForm($form) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        if ($session->post) {
            $form->setDefaults($session->post);
        }
    }

    /**
     * 一覧
     */
    public function listAction() {
        // 権限チェック
        if ($this->view->admin['type'] > 1) {
            $this->view->error_str = '操作が許可されていません。';
            $this->_forward('error', 'Error');
            return;
        }

        // フォーム設定読み込み
        $form = $this->view->form;

        // 検索・クリア
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('clear') ) {
                // クリア
                Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
            } elseif ( $this->getRequest()->getParam('search') ) {
                // 検索開始
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect(self::NAMESPACE_LIST);
            } else {
                // 検索条件復元
                $this->restoreSearchForm($form);
            }
        } else {
            // 検索条件復元
            $this->restoreSearchForm($form);
        }

        // 一覧取得
        $this->createNavigator(
            $this->model('Dao_Mailtemplate')->createWherePhrase(array(), 'id')
        );

        // 表示用カスタマイズ
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            $model['type'] = Dao_Mailtemplate::$types[$model['type']];
            array_push($models, $model);
        }
        $this->view->models = $models;
    }

    /**
     * 編集チェック
     */
    private function editValid($form) {
        // フォームチェック
        if (! $form->isValid($_POST) ) {
            $error_str = array();
            $this->checkForm($form, $this->view->config, $error_str);
            $this->view->error_str = $error_str;
            return false;
        }
        
        return true;
    }

    /**
     * 編集
     */
    public function editAction() {
        // フォーム設定読み込み
        $form = $this->view->form;
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Mailtemplate')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = '指定されたメールテンプレートは削除されたか存在しません。';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $present = $model->toArray();
            $form->setDefaults($present);
            $present['disp_type'] = Dao_Mailtemplate::$types[$present['type']];
            $this->view->model = $present;

            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
                    $this->doUpdate($present['id'], $form);
                }
            }
        }
        else {
            $this->view->error_str = '指定されたメールテンプレートは削除されたか存在しません。';
            $this->_forward('error', 'Error');
            return;
        }
    }

    /**
     * 編集開始
     */
    private function doUpdate($id, $form) { 
        $table = $this->model('Dao_Mailtemplate');
        $model_id = $table->update(
            array(
                'sender'  => $form->getValue('sender'),
                'subject' => $form->getValue('subject'),
                'body'    => $form->getValue('body'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );
        $this->gobackList();
    }
}

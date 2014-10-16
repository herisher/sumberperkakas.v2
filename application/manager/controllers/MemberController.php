<?php
/**
 * 利用者
 */
class Manager_MemberController extends BaseController {
    const NAMESPACE_LIST = "/manager/member/list";

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Member');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        
        // セッションから検索条件を復元する
        $where = array();
        
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'introduce_id' && $value ) {
                    $where['introduce_id = ?'] = $value;
                }
                if ( $key === 'status' && $value ) {
                    $where['status = ?'] = $value;
                }
                if ( $key === 'name2' && $value ) {
                    $where['name2 like ?'] = '%'.$value.'%';
                }
                if ( $key === 'name1' && $value ) {
                    $where['name1 like ?'] = '%'.$value.'%';
                }
                if ( $key === 'name2_kana' && $value ) {
                    $where['name2_kana like ?'] = '%'.$value.'%';
                }
                if ( $key === 'name1_kana' && $value ) {
                    $where['name1_kana like ?'] = '%'.$value.'%';
                }
                if ( $key === 'nickname' && $value ) {
                    $where['nickname like ?'] = '%'.$value.'%';
                }
                if ( $key === 'email' && $value ) {
                    $where['email = ?'] = $value;
                }
                if ( $key === 'gender' && $value ) {
                    $where['gender = ?'] = $value;
                }
                if ( $key === 'birthday' && $value ) {
                    $where['birthday = ?'] = $value;
                }
                if ( $key === 'blood_type' && $value ) {
                    $where['blood_type = ?'] = $value;
                }
                if ( $key === 'melmaga' && strlen($value) ) {
                    $where['melmaga = ?'] = $value;
                }
                if ( $key === 'create_date_from' && $value ) {
                    $where['? <= date(create_date)'] = $value;
                }
                if ( $key === 'create_date_to' && $value ) {
                    $where['? >= date(create_date)'] = $value;
                }
                if ( $key === 'payment_date_from' && $value ) {
                    $where['? <= date(payment_date)'] = $value;
                }
                if ( $key === 'payment_date_to' && $value ) {
                    $where['? >= date(payment_date)'] = $value;
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
     * CSVダウンロード
     */
    public function csvAction() {
        // リミッター解除
        ini_set('memory_limit','-1');

        $models = $this->model('Dao_Member')->fetchAll($this->createWherePhrase('id'));
        
        // ビューを無効にする
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        // ヘッダ出力
        header('Content-type: application/octet-stream');
        if (preg_match("/MSIE 8\.0/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: filename=titian_member_' . time() . '.csv');
        } else {
            header('Content-Disposition: attachment; filename=titian_member_' . time() . '.csv');
        }
        header('Pragma: public');
        header('Cache-control: public');

        $arrCsvOutputCols = array('id','status','email','nickname','gender','blood','birthday','melmaga','create_date','last_login_date');
        $arrCsvOutputTitle = '"会員管理ID","状況","メールアドレス","ニックネーム","性別","血液型","生年月日","メルマガフラグ","登録日","最終ログイン日時"';

        // CSV出力
        echo mb_convert_encoding($arrCsvOutputTitle, 'SJIS-win', 'UTF-8') . "\r\n";
        foreach ($models as $model) {
            $item = $model->toArray();
            $cols = array();
            foreach ($arrCsvOutputCols as $col) {
                if ($col) {
                    // 改行をトル
                    $value = $item[$col];
                    $value = str_replace("\r", "", $value);
                    $value = str_replace("\n", "", $value);
                    $value = str_replace("\"", "\"\"", $value);
                    
                    // 列ごとに整形
                    if ( $col ===  'status' ) {
                        array_push($cols, '"' . Dao_Member::$statics['status'][$value] . '"');
                    } elseif ( $col ===  'gender' ) {
                        array_push($cols, '"' . Dao_Member::$statics['gender'][$value] . '"');
                    } elseif ( $col ===  'melmaga' ) {
                        array_push($cols, '"' . Dao_Member::$statics['melmaga'][$value] . '"');
                    } else {
                        array_push($cols, '"' . $value . '"');
                    }
                } else {
                    array_push($cols, '');
                }
            }
            echo mb_convert_encoding(join(",", $cols), 'SJIS-win', 'UTF-8') . "\r\n";
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
        $form->getElement('status')->setMultiOptions(array('' => '▼選択') + Dao_Member::$statics['status']);
        $form->getElement('gender')->setMultiOptions(array('' => '▼選択') + Dao_Member::$statics['gender']);
        $form->getElement('melmaga')->setMultiOptions(array('' => '▼選択') + Dao_Member::$statics['melmaga']);
        
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
                $this->_redirect('/manager/member/list');
            } elseif ( $this->getRequest()->getParam('csv_download') ) {
                // CSVダウンロード
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect('/manager/member/csv');
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
            $this->createWherePhrase()
        );
        // 表示用カスタマイズ
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            $model['status'] = Dao_Member::$statics['status'][$model['status']];
            $model['melmaga'] = Dao_Member::$statics['melmaga'][$model['melmaga']];
            array_push($models, $model);
        }
        $this->view->models = $models;
    }

    /**
     * 詳細
     */
    public function viewAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $member = $this->model('Dao_Member')->retrieve($id);
            
            if (!$member) {
                $this->view->error_str = '指定された会員は削除されたか存在しません。';
                $this->_forward('error', 'Error');
                return;
            }
            
            $model = $member->toArray();
            $model['status']     = Dao_Member::$statics['status'][$model['status']];
            $model['gender']     = Dao_Member::$statics['gender'][$model['gender']];
            $model['address1']   = Dao_Dietitian::$statics['address1'][$model['address1']];
            $model['melmaga']    = Dao_Member::$statics['melmaga'][$model['melmaga']];
            $this->view->model   = $model;
        }

        $this->view->subtitle = '会員詳細';
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
        // 権限チェック
        if ($this->view->admin['type'] > 1) {
            $this->view->error_str = '操作が許可されていません。';
            $this->_forward('error', 'Error');
            return;
        }   
        
        $form = $this->view->form;
        $form->getElement('gender')->setMultiOptions(array('' => '▼選択') + Dao_Member::$statics['gender']);
        $form->getElement('blood')->setMultiOptions(array('' => '▼選択') + Dao_Member::$statics['blood']);
        $form->getElement('melmaga')->setMultiOptions(array('' => '▼選択') + Dao_Member::$statics['melmaga']);
        //$form->getElement('status')->setMultiOptions(array('' => '▼選択') + Dao_Member::$statics['status2']);
        
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $member = $this->model('Dao_Member')->retrieve($id);
            
            if (!$member) {
                $this->view->error_str = '指定されたユーザーは削除されたか存在しません。';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $model = $member->toArray();
            $form->setDefaults($model);
            $model['disp_status'] = Dao_Member::$statics['status'][$model['status']];
            $this->view->model = $model;
                        
            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                
            	if ( $this->getRequest()->getParam('delete') ) {
	                $form->isValid($_POST);
	                
	                // ダミー画像にする
	                $model_id = $this->model('Dao_Member')->update(
	                    array(
	                        'image_url' => '/img/dummy/dummy_140x140.gif',
	                        'thumb_url' => '/img/dummy/dummy_140x140.gif',
	                    ),
	                    $this->model('Dao_Member')->getAdapter()->quoteInto(
	                        'id = ?', $model['id']
	                    )
	                );

					$this->view->model['thumb_url'] = '/img/dummy/dummy_140x140.gif';
				} elseif ( $this->editValid($form) ) {
                    $this->doUpdate($member['id'], $form);
                }
            }
        }
        else {
            $this->view->error_str = '指定されたユーザーは削除されたか存在しません。';
            $this->_forward('error', 'Error');
            return;
        }
    }

    /**
     * 無料会員にする
     */
    public function changeFreeAction() {
        $id = $this->_getParam("id");

        // すでに無料会員または退会済みの場合は何もしない
        $status = $this->db()->fetchOne("SELECT status FROM dtb_member WHERE id = ?", $id);
        if ($status == 1) {
            $this->gobackList();
            return;
        }

        // 定期課金の解約予約
        $this->model('Logic_Subscription')->doRemove(array(
            'member_id' => $id
        ));

        // 無料会員にする
        $table = new Dao_Member();
        $table->update(
            array(
                'status'      => 1,
                'update_date' => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );

        // 定期課金の解約予約
        $this->model('Logic_Subscription')->doRemove(array(
            'member_id' => $id
        ));

        $this->gobackList();
    }

    /**
     * 退会にする
     */
    public function changeLeaveAction() {
        $id = $this->_getParam("id");

        // すでに退会済みの場合は何もしない
        $status = $this->db()->fetchOne("SELECT status FROM dtb_member WHERE id = ?", $id);
        if ($status == 2) {
            $this->gobackList();
            return;
        }

        // 定期課金の解約予約
        $this->model('Logic_Subscription')->doRemove(array(
            'member_id' => $id
        ));

        // 退会にする
        $table = new Dao_Member();
        $table->update(
            array(
                'status'      => 2,
                'update_date' => new Zend_Db_Expr('now()'),
                'leave_date'  => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );

        $this->gobackList();
    }

    /**
     * 編集開始
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_Member');
		$model = $table->retrieve($id);

        $results = $this->doUpload('image_url', 140, 140, 'member');
        if ($results) {
            $image_url = $results['image_url'];
            $thumb_url = $results['thumb_url'];
        } else {
            $image_url = $model['image_url'];
            $thumb_url = $model['thumb_url'];
        }

        $table->update(
            array(
                'name1'         => $form->getValue('name1'),
                'name2'         => $form->getValue('name2'),
                'name1_kana'    => $form->getValue('name1_kana'),
                'name2_kana'    => $form->getValue('name2_kana'),
                'nickname'      => $form->getValue('nickname'),
                'email'         => $form->getValue('email'),
                'gender'        => $form->getValue('gender'),
                'blood'         => $form->getValue('blood'),
                'image_url'     => $image_url,
                'thumb_url'     => $thumb_url,
                'amount_adult'  => $form->getValue('amount_adult'),
                'amount_child'  => $form->getValue('amount_child'),
                'amount_baby'   => $form->getValue('amount_baby'),
                'profile'       => $form->getValue('profile'),
                'policy'        => $form->getValue('policy'),
                'melmaga'       => $form->getValue('melmaga'),
                'update_date'   => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );

        $this->gobackList();
    }

    /**
     * パスワード編集チェック
     */
    private function passwordEditValid($form) {
        $error_str = array();
        
        // フォームチェック
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
            $this->view->error_str = $error_str;
            return false;
        }
        
        if ( $form->getValue('pass') != $form->getValue('pass_conf') ) {
            $error_str['pass'] = 'パスワードが間違っています。';
            $error_str['pass_conf'] = 'パスワードが間違っています。';
            $this->view->error_str = $error_str;
            return false;
        }
        
        return true;
    }
    
    /**
     * パスワード編集
     */
    public function passwordAction() {
        // 権限チェック
        if ($this->view->admin['type'] > 1) {
            $this->view->error_str = '操作が許可されていません。';
            $this->_forward('error', 'Error');
            return;
        }

        $form = $this->view->form;
        
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Member')->findById($id);
            
            if (!$model) {
                $this->view->error_str = '指定されたユーザーは削除されたか存在しません。';
                $this->_forward('error', 'Error');
                return;
            }
            
            // 初期値設定
            $member = $model;
            $form->setDefaults($member);
            $this->view->model = $member;
            
            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->passwordEditValid($form) ) {
                    $this->changePassword($form, $id);
                    $this->gobackList();
                }
            }
        }
        else {
            $this->view->error_str = '指定されたユーザーは削除されたか存在しません。';
            $this->_forward('error', 'Error');
            return;
        }
    }
    
    /**
     * パスワード変更
     */ 
    private function changePassword($form, $member_id){
        $table = $this->model('Dao_Member');
        
        $model_id = $table->update(
            array(
                'password' => sha1($form->getValue('pass')),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $member_id
            )
        );
    }
    
    /**
     * 削除
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $table = $this->model('Dao_Member');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );

            // 定期課金の解約予約
            $this->model('Logic_Subscription')->doRemove(array(
                'member_id' => $id
            ));

            $this->gobackList();
        }
        else {
            $this->view->error_str = '不正なURLです。';
            $this->_forward('error', 'Error');
        }
    }
}

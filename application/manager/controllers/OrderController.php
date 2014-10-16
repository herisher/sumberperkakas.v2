<?php
/**
 * 注文
 */
class Manager_OrderController extends BaseController {
    const NAMESPACE_LIST = '/manager/order/list';

    /**
     * 検索条件生成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_ViewOrder');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'status' && $value != null ) {
                    $where['status = ?'] = $value;
                }
                if ( $key === 'order_type' && $value ) {
                    $where['order_type like ?'] = $value;
                }
                if ( $key === 'person_type' && $value != null ) {
                    $where['person_type = ?'] = $value;
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

        $models = $this->model('Dao_ViewOrder')->fetchAll($this->createWherePhrase('id'));
        
        // ビューを無効にする
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        // ヘッダ出力
        header('Content-type: application/octet-stream');
        if (preg_match("/MSIE 8\.0/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: filename=titian_order_' . time() . '.csv');
        } else {
            header('Content-Disposition: attachment; filename=titian_order_' . time() . '.csv');
        }
        header('Pragma: public');
        header('Cache-control: public');

        $arrCsvOutputCols = array('id','event_id','status','order_type','person_type','total');
        $arrCsvOutputTitle = '"決済ID","イベントID","状態","決済種別","会員種別","金額"';

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
                        array_push($cols, '"' . Dao_Order::$statics['status'][$value] . '"');
                    } elseif ( $col ===  'order_type' ) {
                        array_push($cols, '"' . Dao_Order::$statics['order_type'][$value] . '"');
                    } elseif ( $col ===  'person_type' ) {
                        array_push($cols, '"' . Dao_Order::$statics['person_type'][$value] . '"');
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
        
        $form = $this->view->form;
        $form->getElement('status')->setMultiOptions(array('' => '▼選択') + Dao_ViewOrder::$statics['status']);
        $form->getElement('order_type')->setMultiOptions(array('' => '▼選択') + Dao_ViewOrder::$statics['order_type']);
        $form->getElement('person_type')->setMultiOptions(array('' => '▼選択') + Dao_ViewOrder::$statics['person_type']);

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
            } elseif ( $this->getRequest()->getParam('csv_download') ) {
                // CSVダウンロード
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect('/manager/order/csv');
            }  else {
                // 検索条件復元
                $this->restoreSearchForm($form);
            }
        } else {
            // 検索条件復元
            $this->restoreSearchForm($form);
        }   
        
        $this->createNavigator(
            $this->createWherePhrase()
        );

        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            $model['status']      = Dao_ViewOrder::$statics['status'][$model['status']];
            $model['order_type']  = Dao_ViewOrder::$statics['order_type'][$model['order_type']];
            $model['person_type'] = Dao_ViewOrder::$statics['person_type'][$model['person_type']];
            
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
            $tips = $this->model('Dao_ViewOrder')->retrieve($id);
            if (!$tips) {
                $this->view->error_str = '指定されたデータは削除されたか存在しません。';
                $this->_forward('error', 'Error');
                return;
            }

            $model = $tips->toArray();
            $model['status']      = Dao_ViewOrder::$statics['status'][$model['status']];
            $model['order_type']  = Dao_ViewOrder::$statics['order_type'][$model['order_type']];
            $model['disp_person_type'] = Dao_ViewOrder::$statics['person_type'][$model['person_type']];
			$this->view->event = $this->model('Dao_Event')->retrieve($model['event_id']);
			$this->view->event_apply = $this->model('Logic_EventApply')->getEventApplyByOrderId($model['id']);
            $this->view->model = $model;
        }
    }

    /**
     * 編集
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
     * データ更新
     */
    private function doUpdate($id, $form) { 
        $table = $this->model('Dao_ViewOrder');
        $model_id = $table->update(
            array(
                'status'        => $form->getValue('status'),
                //'order_type'    => $form->getValue('order_type'),
                //'person_type'   => $form->getValue('person_type'),
                'total'         => $form->getValue('total'),
                'memo'          => $form->getValue('memo'),
                'update_date'   => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );

        $this->model('Dao_EventApply')->update(
            array(
                'apply_amount'	=> $form->getValue('apply_amount'),
            ),
            $this->model('Dao_EventApply')->getAdapter()->quoteInto(
                'id = ?', $this->view->event_apply['id']
            )
        );

        $this->gobackList();
    }

    /**
     * 編集
     */
    public function editAction() {
       if ($this->view->admin['type'] > 1) {
            $this->view->error_str = '操作が許可されていません。';
            $this->_forward('error', 'Error');
            return;
        }
        
        $form = $this->view->form;
        $form->getElement('status')->setMultiOptions(array('' => '▼選択') + Dao_ViewOrder::$statics['status']);
        //$form->getElement('order_type')->setMultiOptions(array('' => '▼選択') + Dao_ViewOrder::$statics['order_type']);
        //$form->getElement('person_type')->setMultiOptions(array('' => '▼選択') + Dao_ViewOrder::$statics['person_type']);

        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_ViewOrder')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = '指定されたユーザーは削除されたか存在しません。';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $order = $model->toArray();
            $order['disp_order_type']  = Dao_ViewOrder::$statics['order_type'][$order['order_type']];
            $order['disp_person_type'] = Dao_ViewOrder::$statics['person_type'][$order['person_type']];
			$this->view->event = $this->model('Dao_Event')->retrieve($order['event_id']);
			$this->view->event_apply = $this->model('Logic_EventApply')->getEventApplyByOrderId($order['id']);
            $form->setDefault('apply_amount', $this->view->event_apply['apply_amount']);
            $form->setDefaults($order);
            $this->view->model = $order;
                        
            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
                   $this->doUpdate($order['id'], $form);
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
     * 削除
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $table = $this->model('Dao_Order');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
            $this->gobackList();
        }
        else {
            $this->view->error_str = '不正なURLです。';
            $this->_forward('error', 'Error');
        }
    }
}

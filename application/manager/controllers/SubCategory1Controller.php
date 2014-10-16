<?php
/**
 * sub category 1
 */
class Manager_SubCategory1Controller extends BaseController {
    const NAMESPACE_LIST = '/manager/sub-category1/list';
    
    /**
     * Search for creating conditions
     */
    private function createWherePhrase($order_by = 'disp_order asc') {
        $table = $this->model('Dao_SubCategory1');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);        

        // restore the search criteria from the session
        $where = array();
		
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // Search conditions set
                if ( $key === 'name' && $value != NULL ) {
                    $where['name like ?'] = '%'.$value.'%';
                }
                if ( $key === 'category_id' && $value != NULL ) {
                    $where['category_id = ?'] = $value;
                }
                if ( $key === 'sub_category_id' && $value != NULL ) {
                    $where['sub_category_id = ?'] = $value;
                }
                if ( $key === 'disp_flag' && $value != NULL ) {
                    $where['disp_flag = ?'] = $value;
                }
            }
        }
        
        $order_by = array('disp_order asc');        
        return $table->createWherePhrase($where, $order_by);
    }

    /**
     * Search restoration
     */
    private function restoreSearchForm($form) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        if ($session->post) {
			if($session->post['category_id'] != NULL) {
				$sub_category = $this->model('Logic_SubCategory')->findByCategoryId($session->post['category_id']);
				$form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);			
			}
            $form->setDefaults($session->post);
        }
    }

    /**
     * list
     */
    public function listAction() {
		$session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        $form = $this->view->form;
		$category = $this->model('Logic_Category')->getAllByHash();
		$sub_category = $this->model('Logic_SubCategory')->getAllByHash();
		
        $form->getElement('category_id')->setMultiOptions(array('' => '▼Pilih') + $category);
        $form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
        $form->getElement('disp_flag')->setMultiOptions(array('' => '▼Pilih') + Dao_Product::$statics['disp_flag']);
		
        // Search Clear
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('clear') ) {
                // Clear
                Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
            }
            elseif ( $this->getRequest()->getParam('search') ) {
                // Start Search
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
				$this->restoreSearchForm($form);
            }
        } else {
			
			$this->restoreSearchForm($form);
		}

		// List acquisition
		$this->createNavigator(
			$this->createWherePhrase(),50
		);

		// Display customization
		$models = array();
		foreach ($this->view->paginator as $model) {
			$model = $model->toArray();
			if( $session->post['disp_flag'] && $session->post['category_id'] && $session->post['sub_category_id'] && !$session->post['name']) {
				$model['up'] = 'Naik';
				$model['down'] = 'Turun';
			} else {
				$model['up'] = '';
				$model['down'] = '';
			}
			$model['category'] = $this->model('Dao_Category')->retrieve($model['category_id']);
			$model['sub_category'] = $this->model('Dao_SubCategory')->retrieve($model['sub_category_id']);
			$model['disp_flag'] ? $model['disp_flag'] = 'o' : $model['disp_flag'] = 'x';
			array_push($models, $model);
		}
		$this->view->models = $models;
    }

    /**
     * New registration check
     */
    private function createValid($form, $datas) {
        $error_str = array();
        // Check form
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }
		
		if( $form->getValue('position') == 3 && count($datas) && !$form->getValue('disp_order')) {
			$error_str['position'] = 'Harus diisi.';
		}
		
        if(count($error_str)) {
            $this->view->error_str = $error_str;
            return false;
        }

        return true;
    }

    /**
     * New registration
     */
    private function doCreate($form, $datas) {
        $table = $this->model('Dao_SubCategory1');
		$position = $form->getValue('position');
		
		if( $position == 1 || !count($datas) ) { //first
			$sub_category1_id = $this->model('Logic_SubCategory1')->doNewTop($form->getValue('sub_category_id'));
		} elseif( $position == 2 ) { //last
			$sub_category1_id = $this->model('Logic_SubCategory1')->doNewRow($form->getValue('sub_category_id'));			
		} elseif( $position == 3 ) { //after
			$sub_category1_id = $this->model('Logic_SubCategory1')->doInsert($form->getValue('sub_category_id'), $form->getValue('disp_order'));
		}
        
        $model_id = $table->update(
            array(
                'name'                 	=> $form->getValue('name'),
                'category_id'           => $form->getValue('category_id'),
                'sub_category_id'       => $form->getValue('sub_category_id'),
                'disp_flag'             => $form->getValue('disp_flag'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $sub_category1_id
            )
        );
        
        $this->gobackList();
    }

    /**
     * create
     */
    public function createAction() {
        $form = $this->view->form;
		$category = $this->model('Logic_Category')->getAllByHash();
		
        $form->getElement('category_id')->setMultiOptions(array('' => '▼Pilih') + $category);
        $form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih'));
        $form->getElement('position')->setMultiOptions(array('1' => 'Pertama', '2' => 'Terakhir', '3' => 'Setelah'));
        $form->getElement('position')->setSeparator(' ');
        $form->getElement('disp_order')->setMultiOptions(array('' => '▼Pilih'));
        $form->getElement('disp_flag')->setMultiOptions(Dao_Product::$statics['disp_flag']);
        $form->getElement('disp_flag')->setSeparator(' ');

        // Error checking
        if ( $this->getRequest()->isPost() ) {
			$sub_category = $this->model('Logic_SubCategory')->findByCategoryId($this->getRequest()->getParam('category_id'));
			$form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
			
			$sub_category1 = $this->model('Logic_SubCategory1')->findBySubCategoryId($this->getRequest()->getParam('sub_category_id'));
			$form->getElement('disp_order')->setMultiOptions(array('' => '▼Pilih') + $sub_category1);
			$form->setDefault('disp_order', $this->getRequest()->getParam('disp_order'));
			
            if ( $this->createValid($form, $sub_category1) ) {
                $this->doCreate($form, $sub_category1);
            }
        }/**/
    }

    /**
     * detail
     */
    private function editValid($form) {
        $error_str = array();
        // Check form
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }
		
        if(count($error_str)) {
            $this->view->error_str = $error_str;
            return false;
        }

        return true;
    }

    /**
     * edit
     */
    public function editAction() {
        $id = $this->getRequest()->getParam('id');
		
        // Form settings read
        $form = $this->view->form;
		
        $form->getElement('disp_flag')->setMultiOptions(Dao_Product::$statics['disp_flag']);
        $form->getElement('disp_flag')->setSeparator(' ');

        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_SubCategory1')->retrieve($id);
            
            // Initial value setting
            $models = $model->toArray();
			$form->setDefaults($models);
            $this->view->model = $models;            

            // Error checking
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
					$this->doUpdate($id, $form);
                }
            }
        }
        else {
            $this->view->error_str = 'Produk tidak ada atau telah dihapus.';
            $this->_forward('error', 'Error');
            return;
        }
    }
    
    /**
     * Start editing
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_SubCategory1');

        $model_id = $table->update(
            array(
                'name'                 	=> $form->getValue('name'),
                'disp_flag'             => $form->getValue('disp_flag'),
                'update_date'           => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );

        $this->gobackList();
    }
	
    /**
     * Delete
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            // Delete data
            $table = $this->model('Dao_SubCategory1');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
			$this->db()->query("DELETE FROM dtb_sub_category2 WHERE category_id = ?", $id);
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'Ini adalah URL ilegal.';
            $this->_forward('error', 'Error');
            return;
        }
    }
	
    public function getDetailAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $q = $this->_getParam("q");
        $models = $this->model("Logic_SubCategory1")->findById($q);
		echo json_encode(
			array(
				"cid" => $models['category_id'],
				"scid" => $models['sub_category_id']
			)
		);
	}
	
    public function getListAction() {
        $this->_helper->layout()->disableLayout();
        $q = $this->_getParam("q");
        $models = $this->model("Logic_SubCategory1")->getAllByCategory($q);
        $this->view->models = $models;
	}
	
    public function getListByCategoryAction() {
        $this->_helper->layout()->disableLayout();
        $q = $this->_getParam("q");
        $models = $this->model("Logic_SubCategory1")->getAllByMainCategory($q);
        $this->view->models = $models;
	}
	
	public function doupAction() {
        $id = $this->getRequest()->getParam('id');
        $scid = $this->getRequest()->getParam('scid');
		$this->model('Logic_SubCategory1')->doUp($scid, $id);
		$this->_redirect('/manager/sub-category1/list');
	}
	
	public function dodownAction() {
        $id = $this->getRequest()->getParam('id');
        $scid = $this->getRequest()->getParam('scid');
		$this->model('Logic_SubCategory1')->doDown($scid, $id);
		$this->_redirect('/manager/sub-category1/list');
	}
}

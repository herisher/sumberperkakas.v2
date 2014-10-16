<?php
/**
 * sub category
 */
class Manager_SubCategoryController extends BaseController {
    const NAMESPACE_LIST = '/manager/sub-category/list';
    
    /**
     * Search for creating conditions
     */
    private function createWherePhrase($order_by = 'disp_order asc') {
        $table = $this->model('Dao_SubCategory');
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
        $form->getElement('category_id')->setMultiOptions(array('' => '▼Pilih') + $category);
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
			if( $session->post['name'] || !$session->post['disp_flag'] || !$session->post['category_id'] ) {
				$model['up'] = '';
				$model['down'] = '';
			} else {
				$model['up'] = 'Naik';
				$model['down'] = 'Turun';
			}
			$model['category'] = $this->model('Dao_Category')->retrieve($model['category_id']);
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
        $table = $this->model('Dao_SubCategory');
		$position = $form->getValue('position');
		
		if( $position == 1 || !count($datas) ) { //first
			$sub_category_id = $this->model('Logic_SubCategory')->doNewTop($form->getValue('category_id'));			
		} elseif( $position == 2 ) { //last
			$sub_category_id = $this->model('Logic_SubCategory')->doNewRow($form->getValue('category_id'));
		} elseif( $position == 3 ) { //after
			$sub_category_id = $this->model('Logic_SubCategory')->doInsert($form->getValue('category_id'), $form->getValue('disp_order'));
		}
        
        $model_id = $table->update(
            array(
                'name'                 	=> $form->getValue('name'),
                'category_id'           => $form->getValue('category_id'),
                'disp_flag'             => $form->getValue('disp_flag'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $sub_category_id
            )
        );
        
        Zend_Session::namespaceUnset('/manager/sub-category/create');
        $this->gobackList();
    }

    /**
     * create
     */
    public function createAction() {
		$session_create = new Zend_Session_Namespace('/manager/sub-category/create');
        $form = $this->view->form;
		$category = $this->model('Logic_Category')->getAllByHash();
		
        $form->getElement('category_id')->setMultiOptions(array('' => '▼Pilih') + $category);
        $form->getElement('position')->setMultiOptions(array('1' => 'Pertama', '2' => 'Terakhir', '3' => 'Setelah'));
        $form->getElement('position')->setSeparator(' ');
		$form->getElement('disp_order')->setMultiOptions(array('' => '▼Pilih'));
        $form->getElement('disp_flag')->setMultiOptions(Dao_Product::$statics['disp_flag']);
        $form->getElement('disp_flag')->setSeparator(' ');

        // Error checking
        if ( $this->getRequest()->isPost() ) {
			$sub_category = $this->model('Logic_SubCategory')->findByCategoryId($this->getRequest()->getParam('category_id'));
			$form->getElement('disp_order')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
			$form->setDefault('disp_order', $this->getRequest()->getParam('disp_order'));
			
            if ( $this->createValid($form, $sub_category) ) {
                $this->doCreate($form, $sub_category);
            }
        }
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
            $model = $this->model('Dao_SubCategory')->retrieve($id);
            
            // Initial value setting
            $models = $model->toArray();
			$form->setDefaults($models);
            $this->view->model = $models;            

            // Error checking
            if ( $this->getRequest()->isPost() ) {
				if ( $this->editValid($form, $apply) ) {
					$this->doUpdate($id, $form, $models);
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
        $table = $this->model('Dao_SubCategory');

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
            $table = $this->model('Dao_SubCategory');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
			$this->db()->query("DELETE FROM dtb_sub_category1 WHERE category_id = ?", $id);
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
        $models = $this->model("Logic_SubCategory")->findById($q);
        echo $models['category_id'];
	}
	
    public function getListAction() {
        $this->_helper->layout()->disableLayout();
        $q = $this->_getParam("q");
        $models = $this->model("Logic_SubCategory")->getAllByCategory($q);
        $this->view->models = $models;
	}
	
	public function doupAction() {
        $id = $this->getRequest()->getParam('id');
        $cid = $this->getRequest()->getParam('cid');
		$this->model('Logic_SubCategory')->doUp($cid, $id);
		$this->_redirect('/manager/sub-category/list');
	}
	
	public function dodownAction() {
        $id = $this->getRequest()->getParam('id');
        $cid = $this->getRequest()->getParam('cid');
		$this->model('Logic_SubCategory')->doDown($cid, $id);
		$this->_redirect('/manager/sub-category/list');
	}
}

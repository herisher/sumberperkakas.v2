<?php
/**
 * category
 */
class Manager_CategoryController extends BaseController {
    const NAMESPACE_LIST = '/manager/category/list';
    
    /**
     * Search for creating conditions
     */
    private function createWherePhrase($order_by = 'disp_order asc') {
        $table = $this->model('Dao_Category');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);        

        // restore the search criteria from the session
        $where = array();
		
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // Search conditions set
                if ( $key === 'name' && $value != NULL ) {
                    $where['name like ?'] = '%'.$value.'%';
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
			if( $session->post['name'] || !$session->post['disp_flag'] ) {
				$model['up'] = '';
				$model['down'] = '';
			} else {
				$model['up'] = 'Naik';
				$model['down'] = 'Turun';
			}
            $model['disp_flag'] ? $model['disp_flag'] = 'o' : $model['disp_flag'] = 'x';
            array_push($models, $model);
        }
        $this->view->models = $models;
    }

    /**
     * New registration check
     */
    private function createValid($form) {
        $error_str = array();
        // Check form
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }
		
		if( $form->getValue('position') == 3 && !$form->getValue('disp_order') ) {
			$error_str['position'] = 'Harus dipilih.';
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
    private function doCreate($form) {
        $table = $this->model('Dao_Category');
		$position = $form->getValue('position');
		
		if( $position == 1 ) { //first
			$category_id = $this->model('Logic_Category')->doNewTop();
		} elseif( $position == 2 ) { //last
			$category_id = $this->model('Logic_Category')->doNewRow();
		} elseif( $position == 3 ) { //after
			$category_id = $this->model('Logic_Category')->doInsert($form->getValue('disp_order'));
		}
		
        $model_id = $table->update(
            array(
                'name'                 	=> $form->getValue('name'),
                'disp_flag'             => $form->getValue('disp_flag'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $category_id
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
		$this->view->category = $category;
		
		if( !$category ) {
			$form->getElement('position')->setMultiOptions(array('1' => 'Pertama'));
			$form->getElement('position')->setSeparator(' ');
		} else {
			$form->getElement('position')->setMultiOptions(array('1' => 'Pertama', '2' => 'Terakhir', '3' => 'Setelah'));
			$form->getElement('position')->setSeparator(' ');
			$form->getElement('disp_order')->setMultiOptions(array('' => '▼Pilih') + $category);
		}
		
        $form->getElement('disp_flag')->setMultiOptions(Dao_Product::$statics['disp_flag']);
        $form->getElement('disp_flag')->setSeparator(' ');

        // Error checking
        if ( $this->getRequest()->isPost() ) {
            if ( $this->createValid($form) ) {
                $this->doCreate($form);
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
            $model = $this->model('Dao_Category')->retrieve($id);
            
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
        $table = $this->model('Dao_Category');

        $model_id = $table->update(
            array(
                'name'                 	=> $form->getValue('name'),
                'disp_order'            => $form->getValue('disp_order'),
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
            $table = $this->model('Dao_Category');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
			$this->db()->query("DELETE FROM dtb_sub_category WHERE category_id = ?", $id);
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
	
	public function doupAction() {
        $id = $this->getRequest()->getParam('id');
		$this->model('Logic_Category')->doUp($id);
		$this->_redirect('/manager/category/list');
	}
	
	public function dodownAction() {
        $id = $this->getRequest()->getParam('id');
		$this->model('Logic_Category')->doDown($id);
		$this->_redirect('/manager/category/list');
	}
}

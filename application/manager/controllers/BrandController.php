<?php
/**
 * brand
 */
class Manager_BrandController extends BaseController {
    const NAMESPACE_LIST = '/manager/brand/list';
    
    /**
     * Search for creating conditions
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Brand');
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
        
        $order_by = array('id desc');        
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
            $this->createWherePhrase(),10
        );

        // Display customization
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
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
        $table = $this->model('Dao_Brand');
		
		$result = $this->model('Logic_Images')->doUpload($form, 'image_url1', 'brand');
		if( $result ) {
			$image_url = $result['image_url'];
			$thumb_url = $result['thumb_url'];
		} else {
			$image_url = "";
			$thumb_url = "";
		}
		
        $model_id = $table->insert(
            array(
                'name'         	=> $form->getValue('name'),
                'url'           => $form->getValue('url'),
                'image_url'		=> $image_url,
                'thumb_url'		=> $thumb_url,
                'disp_flag'		=> $form->getValue('disp_flag'),
                'update_date'	=> new Zend_Db_Expr('now()'),
                'create_date'	=> new Zend_Db_Expr('now()'),
            )
        );
        
        $this->gobackList();
    }

    /**
     * create
     */
    public function createAction() {
        $form = $this->view->form;
		$form = $this->model('Logic_Images')->getImageForm($form, 'brand');
		
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
    private function editValid($form, $brand_apply) {
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
		$form = $this->model('Logic_Images')->getImageForm($form, 'brand');
		
        $form->getElement('disp_flag')->setMultiOptions(Dao_Product::$statics['disp_flag']);
        $form->getElement('disp_flag')->setSeparator(' ');

        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Brand')->retrieve($id);
            
            // Initial value setting
            $models = $model->toArray();
			$form->setDefaults($models);
            $this->view->model = $models;            

            // Error checking
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
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
    private function doUpdate($id, $form, $brand) {
        $table = $this->model('Dao_Brand');
            $image_url = "";
            $thumb_url = "";
			
        if ($_POST['checkbox1']) {
            $thumb_url = "";
            $thumb_url = "";
        } else {
			$result = $this->model('Logic_Images')->doUpload($form, 'image_url1', 'brand');
			if( $result ) {
				$image_url = $result['image_url'];
				$thumb_url = $result['thumb_url'];
			} else {
                $image_url = $brand['image_url'];
                $thumb_url = $brand['thumb_url'];
            }
        }
		
        $model_id = $table->update(
            array(
                'name'         	=> $form->getValue('name'),
                'url'           => $form->getValue('url'),
                'image_url'		=> $image_url,
                'thumb_url'		=> $thumb_url,
                'disp_flag'		=> $form->getValue('disp_flag'),
                'update_date'	=> new Zend_Db_Expr('now()'),
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
            $table = $this->model('Dao_Brand');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'Ini adalah URL ilegal.';
            $this->_forward('error', 'Error');
            return;
        }
    }
}

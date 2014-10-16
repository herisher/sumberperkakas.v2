<?php
/**
 * news
 */
class Manager_NewsController extends BaseController {
    const NAMESPACE_LIST = '/manager/news/list';

    /**
     * Search for creating conditions
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_News');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // restore the search criteria from the session
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // Search conditions set
                if ( $key === 'title' && $value ) {
                    $where['title like ?'] = '%'.$value.'%';
                }
                if ( $key === 'disp_flag' && $value != null ) {
                    $where['disp_flag = ?'] = $value;
                }
                if ( $key === 'disp_date_from' && $value ) {
                    $where['? <= date(disp_date)'] = $value;
                }
                if ( $key === 'disp_date_to' && $value ) {
                    $where['? >= date(disp_date)'] = $value;
                }
            }
        }
        
        $order_by = array('sort_order asc', 'create_date desc', 'id desc');
        
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
     *  list
     */
    public function listAction() {
        // Form settings read
        $form = $this->view->form;
        $form->getElement('disp_flag')->setMultiOptions(array('' => '▼Pilih') + Dao_News::$statics['disp_flag']);

        // Search Clear
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('clear') ) {
                // clear
                Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
            } elseif ( $this->getRequest()->getParam('search') ) {
                // search
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect(self::NAMESPACE_LIST);
            } else {
                // Search restoration
                $this->restoreSearchForm($form);
            }
        } else {
            // Search restoration
            $this->restoreSearchForm($form);
        }   
        
        // List acquisition
        $this->createNavigator(
            $this->createWherePhrase()
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
     * renumber
     */
    public function renumberAction() {
        $table  = $this->model('Dao_News');
        $models = $table->search(array(),'sort_order');
        $sort_order = 10;
        foreach ($models as $model) {
            $table->update(array('sort_order' => $sort_order),'id = '. $model->id);
            $sort_order += 10;
        }
        $this->gobackList();
    }

    /**
     * New registration check
     */
    private function createValid($form) {
        // Check form
        if (! $form->isValid($_POST) ) {
            $error_str = array();
            $this->checkForm($form, $this->view->config, $error_str);
            $this->view->error_str = $error_str;
            return false;
        }
        
        return true;
    }
    
    /**
     * New registration start
     */
    private function doCreate($form) {
        $table = $this->model('Dao_News');
		
		$result = $this->model('Logic_Images')->doUpload($form, 'image_url1', 'news');
		if( $result ) {
			$image_url = $result['image_url'];
			$thumb_url = $result['thumb_url'];
		} else {
			$image_url = "";
			$thumb_url = "";
		}
		
        $model_id = $table->insert(
            array(
                'title'         => $form->getValue('title'),
                'content'       => $form->getValue('content'),
                'disp_date'     => $form->getValue('disp_date'),
                'disp_flag'     => $form->getValue('disp_flag'),
                'sort_order'    => $form->getValue('sort_order'),
                'url'           => $form->getValue('url'),
                'image_url'		=> $image_url,
                'thumb_url'		=> $thumb_url,
                'create_date'   => new Zend_Db_Expr('now()'),
                'update_date'   => new Zend_Db_Expr('now()'),
            )
        );
        $this->gobackList();
    }
    
    /**
     * create
     */
    public function createAction() {
        // Form settings read
        $form = $this->view->form;
        $form->getElement('disp_flag')->setMultiOptions(array('' => '▼Pilih') + Dao_News::$statics['disp_flag']);
        $form->setDefaults(array('disp_date' => date("Y-m-d")));
		$form = $this->model('Logic_Images')->getImageForm($form, 'news');
        
        // Error checking
        if ( $this->getRequest()->isPost() ) {
            if ( $this->createValid($form) ) {
                $this->doCreate($form);
            }
        }
    }
    
    /**
     * Edit check
     */
    private function editValid($form) {
        // Check form
        if (! $form->isValid($_POST) ) {
            $error_str = array();
            $this->checkForm($form, $this->view->config, $error_str);
            $this->view->error_str = $error_str;
            return false;
        }
        
        return true;
    }

    /**
     * edit
     */
    public function editAction() {
        // Form settings read
        $form = $this->view->form;
		$form = $this->model('Logic_Images')->getImageForm($form, 'news');
        $form->getElement('disp_flag')->setMultiOptions(array('' => '▼Pilih') + Dao_News::$statics['disp_flag']);
        
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_News')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Informasi tidak ada atau telah dihapus.';
                $this->_forward('error', 'Error');
                return;
            }

            // Initial value setting
            $present = $model->toArray();
            $form->setDefaults($present);
            $present['disp_flag'] = Dao_News::$statics['disp_flag'][$present['disp_flag']];
            $this->view->model = $present;

            // Error checking
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
                    $this->doUpdate($present['id'], $form, $present);
                }
            }
        }
        else {
            $this->view->error_str = 'Informasi tidak ada atau telah dihapus.';
            $this->_forward('error', 'Error');
            return;
        }
    }

    /**
     * Start editing
     */
    private function doUpdate($id, $form, $model) { 
        $table = $this->model('Dao_News');
		
        if ($_POST['checkbox1']) {
            $thumb_url = "";
            $thumb_url = "";
        } else {
			$result = $this->model('Logic_Images')->doUpload($form, 'image_url1', 'news');
			if( $result ) {
				$image_url = $result['image_url'];
				$thumb_url = $result['thumb_url'];
			} else {
                $image_url = $model['image_url'];
                $thumb_url = $model['thumb_url'];
            }
        }
		
        $model_id = $table->update(
            array(
                'title'         => $form->getValue('title'),
                'content'       => $form->getValue('content'),
                'url'           => $form->getValue('url'),
                'image_url'		=> $image_url,
                'thumb_url'		=> $thumb_url,
                'sort_order'    => $form->getValue('sort_order'),
                'disp_date'     => $form->getValue('disp_date'),
                'disp_flag'     => $form->getValue('disp_flag'),
                'update_date'   => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );
        $this->gobackList();
    }
	
    /**
     * delete
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            // Delete data
            $table = $this->model('Dao_News');
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
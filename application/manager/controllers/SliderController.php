<?php
/**
 * image slider
 */
class Manager_SliderController extends BaseController {
    const NAMESPACE_LIST = '/manager/slider/list';
    
    /**
     * list
     */
    public function listAction() {
        // List acquisition
        $this->createNavigator(
            $this->model('Dao_ImageSlider')->createWherePhrase($where,'disp_order'),50
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
        $table = $this->model('Dao_ImageSlider');
        
        $model_id = $table->insert(
            array(
                'image_url'         => $image_url,
                'detail_image_url'  => $detail_image_url,
                'disp_order'        => 0,
                'disp_flag'         => $form->getValue('disp_flag'),
                'update_date'       => new Zend_Db_Expr('now()'),
                'create_date'       => new Zend_Db_Expr('now()'),
            )
        );
        
        $this->gobackList();
    }

    /**
     * create
     */
    public function createAction() {
        $form = $this->view->form;
        
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
            $model = $this->model('Dao_ImageSlider')->retrieve($id);
            
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
            $this->view->error_str = 'Image tidak ada atau telah dihapus.';
            $this->_forward('error', 'Error');
            return;
        }
    }
    
    /**
     * Start editing
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_ImageSlider');

        $model_id = $table->update(
            array(
                'image_url'         => $image_url,
                'detail_image_url'  => $detail_image_url,
                'disp_flag'         => $form->getValue('disp_flag'),
                'update_date'       => new Zend_Db_Expr('now()'),
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
            $table = $this->model('Dao_ImageSlider');
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

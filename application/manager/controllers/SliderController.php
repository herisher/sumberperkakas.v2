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
            $this->model('Dao_ImageSlider')->createWherePhrase(array(),'disp_order')
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
     * New registration check
     */
    private function createValid($form) {
        $error_str = array();
        // Check form
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }
        
        if ( !empty($_FILES['image_url']['tmp_name']) ) {
            if ( ($_FILES['image_url']['type'] != 'image/jpeg') && ($_FILES['image_url']['type'] != 'image/gif') && ($_FILES['image_url']['type'] != 'image/png') ) {
                $error_str['image_url'] = "Type gambar yang diizinkan : jpg,png,gif";
            }

            if ($_FILES['image_url']['size'] == 0) {
                $error_str['image_url'] = "Harap pilih gambar";
            }

            if ($_FILES['image_url']['size'] > 5000000) {
                $error_str['image_url'] = "Ukuran maksimal gambar adalah 5Mb?";
            }
            
            $image_info = getimagesize($_FILES["image_url"]["tmp_name"]);
            if( $image_info[0]!=870 || $image_info[1]!=315 ) {
                $error_str['image_url'] = "Ukuran gambar harus 870x315px";
            }
        }

        if ( !empty($_FILES['detail_image_url']['tmp_name']) ) {
            if ( ($_FILES['detail_image_url']['type'] != 'image/jpeg') && ($_FILES['detail_image_url']['type'] != 'image/gif') && ($_FILES['detail_image_url']['type'] != 'image/png') ) {
                $error_str['detail_image_url'] = "Type gambar yang diizinkan : jpg,png,gif";
            }

            if ($_FILES['detail_image_url']['size'] == 0) {
                $error_str['detail_image_url'] = "Harap pilih gambar";
            }

            if ($_FILES['detail_image_url']['size'] > 5000000) {
                $error_str['detail_image_url'] = "Ukuran maksimal gambar adalah 5Mb?";
            }
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
        
        $result1 = $this->model('Logic_Images')->doUploadStd('image_url', 'slider');
        if( $result1 ) {
            $image_url = $result1;
        } else {
            $image_url = "";
        }
        
        $result2 = $this->model('Logic_Images')->doUploadStd('detail_image_url', 'slider/detail');
        if( $result2 ) {
            $detail_image_url = $result2;
        } else {
            $detail_image_url = "";
        }
        
        $model_id = $table->insert(
            array(
                'image_url'         => $image_url,
                'detail_image_url'  => $detail_image_url,
                'disp_order'        => $form->getValue('disp_order'),
                'disp_flag'         => $form->getValue('disp_flag'),
                'update_date'       => new Zend_Db_Expr('now()'),
                'create_date'       => new Zend_Db_Expr('now()'),
            )
        );
        
        $this->gobackList();
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
                    $this->doUpdate($models, $form);
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
     * detail
     */
    private function editValid($form) {
        $error_str = array();
        // Check form
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }
        
        if ( !empty($_FILES['image_url']['tmp_name']) ) {
            if ( ($_FILES['image_url']['type'] != 'image/jpeg') && ($_FILES['image_url']['type'] != 'image/gif') && ($_FILES['image_url']['type'] != 'image/png') ) {
                $error_str['image_url'] = "Type gambar yang diizinkan : jpg,png,gif";
            }

            if ($_FILES['image_url']['size'] == 0) {
                $error_str['image_url'] = "Harap pilih gambar";
            }

            if ($_FILES['image_url']['size'] > 5000000) {
                $error_str['image_url'] = "Ukuran maksimal gambar adalah 5Mb?";
            }
            
            $image_info = getimagesize($_FILES["image_url"]["tmp_name"]);
            if( $image_info[0]!=870 || $image_info[1]!=315 ) {
                $error_str['image_url'] = "Ukuran gambar harus 870x315px";
            }
        }

        if ( !empty($_FILES['detail_image_url']['tmp_name']) ) {
            if ( ($_FILES['detail_image_url']['type'] != 'image/jpeg') && ($_FILES['detail_image_url']['type'] != 'image/gif') && ($_FILES['detail_image_url']['type'] != 'image/png') ) {
                $error_str['detail_image_url'] = "Type gambar yang diizinkan : jpg,png,gif";
            }

            if ($_FILES['detail_image_url']['size'] == 0) {
                $error_str['detail_image_url'] = "Harap pilih gambar";
            }

            if ($_FILES['detail_image_url']['size'] > 5000000) {
                $error_str['detail_image_url'] = "Ukuran maksimal gambar adalah 5Mb?";
            }
        }

        if(count($error_str)) {
            $this->view->error_str = $error_str;
            return false;
        }

        return true;
    }

    /**
     * Start editing
     */
    private function doUpdate($model, $form) {
        $table = $this->model('Dao_ImageSlider');

        if ( !empty($_FILES['image_url']['tmp_name']) ) {
            $result1 = $this->model('Logic_Images')->doUploadStd('image_url', 'slider');
            if( $result1 ) {
                $image_url = $result1;
            } else {
                $image_url = "";
            }
        } else $image_url = $model['image_url'];
        if ( !empty($_FILES['detail_image_url']['tmp_name']) ) {
            $result2 = $this->model('Logic_Images')->doUploadStd('detail_image_url', 'slider/detail');
            if( $result2 ) {
                $detail_image_url = $result2;
            } else {
                $detail_image_url = "";
            }
        } else $detail_image_url = $model['detail_image_url'];
        
        $model_id = $table->update(
            array(
                'image_url'         => $image_url,
                'detail_image_url'  => $detail_image_url,
                'disp_flag'         => $form->getValue('disp_flag'),
                'disp_order'        => $form->getValue('disp_order'),
                'update_date'       => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $model['id']
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
            $model = $table->retrieve($id);
            if( $model['image_url'] ) {
                unlink(APPLICATION_PATH . $model['image_url']);
            }
            if( $model['detail_image_url'] ) {
                unlink(APPLICATION_PATH . $model['detail_image_url']);
            }
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

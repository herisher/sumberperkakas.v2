<?php
/**
 * product
 */
class Manager_ProductController extends BaseController {
    const NAMESPACE_LIST = '/manager/product/list';
    
    /**
     * Search for creating conditions
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Product');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);        

        // restore the search criteria from the session
        $where = array();
		
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // Search conditions set
                if ( $key === 'name' && $value != NULL ) {
                    $where['name like ?'] = '%'.$value.'%';
                }
                if ( $key === 'item_number' && $value != NULL ) {
                    $where['item_number like ?'] = '%'.$value.'%';
                }
                if ( $key === 'brand_id' && $value != NULL ) {
                    $where['brand_id = ?'] = $value;
                }
                if ( $key === 'category_id' && $value != NULL ) {
                    $where['category_id = ?'] = $value;
                }
                if ( $key === 'sub_category_id' && $value != NULL ) {
                    $where['sub_category_id = ?'] = $value;
                }
                if ( $key === 'status' && $value != NULL ) {
                    $where['status = ?'] = $value;
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
			if($session->post['category_id'] != NULL) {
				$sub_category = $this->model('Logic_SubCategory')->findByCategoryId($session->post['category_id']);
				$sub_category1 = $this->model('Logic_SubCategory1')->findByCategoryId($session->post['category_id']);
				$sub_category2 = $this->model('Logic_SubCategory2')->findByCategoryId($session->post['category_id']);
				$form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
				$form->getElement('sub_category1_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category1);
				$form->getElement('sub_category2_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category2);
			}
			
			if($session->post['sub_category_id'] != NULL) {
				$sub_category1 = $this->model('Logic_SubCategory1')->findBySubCategoryId($session->post['sub_category_id']);
				$sub_category2 = $this->model('Logic_SubCategory2')->findBySubCategoryId($session->post['category_id']);
				$form->getElement('sub_category1_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category1);
				$form->getElement('sub_category2_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category2);
			}
			
			if($session->post['sub_category1_id'] != NULL) {
				$sub_category2 = $this->model('Logic_SubCategory2')->findBySubCategory1Id($session->post['sub_category1_id']);
				$form->getElement('sub_category2_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category2);
			}
			
            $form->setDefaults($session->post);
        }
    }

    /**
     * list
     */
    public function listAction() {
        $form = $this->view->form;
		
		$brand = $this->model('Logic_Brand')->getAllByHash();
		$category = $this->model('Logic_Category')->getAllByHash();
		$sub_category = $this->model('Logic_SubCategory')->getAllByHash();
		$sub_category1 = $this->model('Logic_SubCategory1')->getAllByHash();
		$sub_category2 = $this->model('Logic_SubCategory2')->getAllByHash();
		
        $form->getElement('brand_id')->setMultiOptions(array('' => '▼Pilih') + $brand);
        $form->getElement('category_id')->setMultiOptions(array('' => '▼Pilih') + $category);
        $form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
        $form->getElement('sub_category1_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category1);
        $form->getElement('sub_category2_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category2);
        $form->getElement('status')->setMultiOptions(array('' => '▼Pilih') + Dao_Product::$statics['status']);
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
			$model['brand'] = $this->model('Dao_Brand')->retrieve($model['brand_id']);
			$model['category'] = $this->model('Dao_Category')->retrieve($model['category_id']);
            $model['disp_status'] = Dao_Product::$statics['status'][$model['status']];
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
        /*
		if (!$form->image_url->receive()) {
            $error_str['image_url'] = 'Error receiving the file.';
		}*/
		
        if($form->getValue('price') < $form->getValue('promo_price')) {
            $error_str['promo_price'] = "Harga promo harus lebih rendah dari harga normal.";
        }
		
        if(!$form->getValue('call_us') && !$form->getValue('price')) {
            $error_str['price'] = "Harus diisi.";
        }
		
        if($form->getValue('promo_period_start') > $form->getValue('promo_period_end')) {
            $error_str['promo_period'] = "Harap diisi dengan benar.";
        }
		
        if(!$_FILES['image_url1']['size']) {
            $error_str['image_url'] = 'Harus diisi.';
        }
		
        if (isset($_FILES['image_url1'])) {
			$filename = $_FILES['image_url1']['tmp_name'];
			list($width, $height) = getimagesize($filename);
			if($width != $height) {
				$error_str['image_url'] = 'Gambar harus kotak.';
			}
		}
        if (isset($_FILES['image_url2'])) {
			$filename = $_FILES['image_url2']['tmp_name'];
			list($width, $height) = getimagesize($filename);
			if($width != $height) {
				$error_str['image_url'] = 'Gambar harus kotak.';
			}
		}
        if (isset($_FILES['image_url3'])) {
			$filename = $_FILES['image_url3']['tmp_name'];
			list($width, $height) = getimagesize($filename);
			if($width != $height) {
				$error_str['image_url'] = 'Gambar harus kotak.';
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
        $table = $this->model('Dao_Product');
		$result1 = $this->model('Logic_Images')->doUploadProduct($form, 'image_url1', 'product');
		if( $result1 ) {
			$image_url1 = $result1['image_url'];
			$th082_url1 = $result1['th082_url'];
			$th155_url1 = $result1['th155_url'];
			$th270_url1 = $result1['th270_url'];
		} else {
			$image_url1 = "";
			$th082_url1 = "";
			$th155_url1 = "";
			$th270_url1 = "";
		}
		
		$result2 = $this->model('Logic_Images')->doUploadProduct($form, 'image_url2', 'product');
		if( $result2 ) {
			$image_url2 = $result2['image_url'];
			$th082_url2 = $result2['th082_url'];
			$th155_url2 = $result2['th155_url'];
			$th270_url2 = $result2['th270_url'];
		} else {
			$image_url2 = "";
			$th082_url2 = "";
			$th155_url2 = "";
			$th270_url2 = "";
		}
		
		$result3 = $this->model('Logic_Images')->doUploadProduct($form, 'image_url3', 'product');
		if( $result3 ) {
			$image_url3 = $result3['image_url'];
			$th082_url3 = $result3['th082_url'];
			$th155_url3 = $result3['th155_url'];
			$th270_url3 = $result3['th270_url'];
		} else {
			$image_url3 = "";
			$th082_url3 = "";
			$th155_url3 = "";
			$th270_url3 = "";
		}
        
        $model_id = $table->insert(
            array(
                'item_number'          	=> $form->getValue('item_number'),
                'name'                 	=> $form->getValue('name'),
                'call_us'              	=> $form->getValue('call_us'),
                'price'                	=> $form->getValue('price'),
                'promo_price'			=> $form->getValue('promo_price'),
                'promo_period_start'	=> $form->getValue('promo_period_start'),
                'promo_period_end'		=> $form->getValue('promo_period_end'),
                'brand_id'				=> $form->getValue('brand_id'),
                'type'					=> $form->getValue('type'),
                'category_id'			=> $form->getValue('category_id'),
                'sub_category_id'		=> $form->getValue('sub_category_id'),
                'sub_category1_id'		=> $form->getValue('sub_category1_id'),
                'sub_category2_id'		=> $form->getValue('sub_category2_id'),
                'image_url1'			=> $image_url1,
                'image_url2'			=> $image_url2,
                'image_url3'			=> $image_url3,
                'th082_url1'			=> $th082_url1,
                'th082_url2'			=> $th082_url2,
                'th082_url3'			=> $th082_url3,
                'th155_url1'			=> $th155_url1,
                'th155_url2'			=> $th155_url2,
                'th155_url3'			=> $th155_url3,
                'th270_url1'			=> $th270_url1,
                'th270_url2'			=> $th270_url2,
                'th270_url3'			=> $th270_url3,
                'description'			=> $form->getValue('description'),
                'status'            	=> $form->getValue('status'),
                'disp_flag'             => $form->getValue('disp_flag'),
                'update_date'           => new Zend_Db_Expr('now()'),
                'create_date'           => new Zend_Db_Expr('now()'),
            )
        );
        
        $this->gobackList();
    }

    /**
     * create
     */
    public function createAction() {
        $form = $this->view->form;
		$form = $this->model('Logic_Images')->getImageForm($form, 'product');

		$brand = $this->model('Logic_Brand')->getAllByHash();
		$category = $this->model('Logic_Category')->getAllByHash();
		$sub_category = $this->model('Logic_SubCategory')->getAllByHash();
		$sub_category1 = $this->model('Logic_SubCategory1')->getAllByHash();
		$sub_category2 = $this->model('Logic_SubCategory2')->getAllByHash();
		
        $form->getElement('brand_id')->setMultiOptions(array('' => '▼Pilih') + $brand);
        $form->getElement('category_id')->setMultiOptions(array('' => '▼Pilih') + $category);
        $form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
        $form->getElement('sub_category1_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category1);
        $form->getElement('sub_category2_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category2);
        $form->getElement('status')->setMultiOptions(array('' => '▼Pilih') + Dao_Product::$statics['status']);
        $form->getElement('disp_flag')->setMultiOptions(Dao_Product::$statics['disp_flag']);
        $form->getElement('disp_flag')->setSeparator(' ');
		
        // Error checking
        if ( $this->getRequest()->isPost() ) {
			$sub_category = $this->model('Logic_SubCategory')->findByCategoryId($this->getRequest()->getParam('category_id'));
			$form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
			
			$sub_category1 = $this->model('Logic_SubCategory1')->findBySubCategoryId($this->getRequest()->getParam('sub_category_id'));
			$form->getElement('sub_category1_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category1);
			
			$sub_category2 = $this->model('Logic_SubCategory2')->findBySubCategory1Id($this->getRequest()->getParam('sub_category1_id'));
			$form->getElement('sub_category2_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category2);
			
            if ( $this->createValid($form) ) {
                $this->doCreate($form);
            }
        }
    }

    /**
     * detail
     */
    private function editValid($form, $product_apply) {
        $error_str = array();
        // Check form
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }
		
        if(!$form->getValue('call_us') && !$form->getValue('price')) {
            $error_str['price'] = "Harus diisi.";
        }
		
        if($form->getValue('promo_period_start') > $form->getValue('promo_period_end')) {
            $error_str['promo_period'] = "Harap diisi dengan benar.";
        }
        
        if($form->getValue('price') < $form->getValue('discount_price')) {
            $error_str['discount_price'] = "Harga promo harus lebih rendah dari harga normal";
        }

        if(!$_FILES['image_url1']['size'] && $_POST['checkbox1']) {
            $error_str['image_url'] = 'Harus diisi.';
        } elseif( $_FILES['image_url1']['size'] && $_POST['checkbox1'] ) {
			$_POST['checkbox1'] = "";
		}

        if (isset($_FILES['image_url1'])) {
			$filename = $_FILES['image_url1']['tmp_name'];
			list($width, $height) = getimagesize($filename);
			if($width != $height) {
				$error_str['image_url'] = 'Gambar harus kotak.';
			}
		}
        if (isset($_FILES['image_url2'])) {
			$filename = $_FILES['image_url2']['tmp_name'];
			list($width, $height) = getimagesize($filename);
			if($width != $height) {
				$error_str['image_url'] = 'Gambar harus kotak.';
			}
		}
        if (isset($_FILES['image_url3'])) {
			$filename = $_FILES['image_url3']['tmp_name'];
			list($width, $height) = getimagesize($filename);
			if($width != $height) {
				$error_str['image_url'] = 'Gambar harus kotak.';
			}
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
		$form = $this->model('Logic_Images')->getImageForm($form, 'product');
		
		$brand = $this->model('Logic_Brand')->getAllByHash();
		$category = $this->model('Logic_Category')->getAllByHash();
		$sub_category = $this->model('Logic_SubCategory')->getAllByHash();
		$sub_category1 = $this->model('Logic_SubCategory1')->getAllByHash();
		$sub_category2 = $this->model('Logic_SubCategory2')->getAllByHash();
		
        $form->getElement('brand_id')->setMultiOptions(array('' => '▼Pilih') + $brand);
        $form->getElement('category_id')->setMultiOptions(array('' => '▼Pilih') + $category);
        $form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
        $form->getElement('sub_category1_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category1);
        $form->getElement('sub_category2_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category2);
        $form->getElement('status')->setMultiOptions(array('' => '▼Pilih') + Dao_Product::$statics['status']);
        $form->getElement('disp_flag')->setMultiOptions(Dao_Product::$statics['disp_flag']);
        $form->getElement('disp_flag')->setSeparator(' ');

        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Product')->retrieve($id);
            
            // Initial value setting
            $models = $model->toArray();
			$form->setDefaults($models);
            $this->view->model = $models;            

            // Error checking
            if ( $this->getRequest()->isPost() ) {
				$sub_category = $this->model('Logic_SubCategory')->findByCategoryId($this->getRequest()->getParam('category_id'));
				$form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
				
				$sub_category1 = $this->model('Logic_SubCategory1')->findBySubCategoryId($this->getRequest()->getParam('sub_category_id'));
				$form->getElement('sub_category1_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category1);
				
				$sub_category2 = $this->model('Logic_SubCategory2')->findBySubCategory1Id($this->getRequest()->getParam('sub_category1_id'));
				$form->getElement('sub_category2_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category2);
			
                if ( $this->editValid($form, $models) ) {
					$this->doUpdate($id, $form, $models);
                }
            } else {
				$sub_category = $this->model('Logic_SubCategory')->findByCategoryId($models['category_id']);
				$form->getElement('sub_category_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category);
				
				$sub_category1 = $this->model('Logic_SubCategory1')->findBySubCategoryId($models['sub_category_id']);
				$form->getElement('sub_category1_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category1);
				
				$sub_category2 = $this->model('Logic_SubCategory2')->findBySubCategory1Id($models['sub_category1_id']);
				$form->getElement('sub_category2_id')->setMultiOptions(array('' => '▼Pilih') + $sub_category2);
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
    private function doUpdate($id, $form, $product) {
        $table = $this->model('Dao_Product');
		
        if ($_POST['checkbox1']) {
            $image_url1 = "";
            $th082_url1 = "";
            $th155_url1 = "";
            $th270_url1 = "";
        } else {
			$result1 = $this->model('Logic_Images')->doUploadProduct($form, 'image_url1', 'product');
			if( $result1 ) {
				$image_url1 = $result1['image_url'];
				$th082_url1 = $result1['th082_url'];
				$th155_url1 = $result1['th155_url'];
				$th270_url1 = $result1['th270_url'];
			} else {
                $image_url1 = $product['image_url1'];
                $th082_url1 = $product['th082_url1'];
                $th155_url1 = $product['th155_url1'];
                $th270_url1 = $product['th270_url1'];
            }
        }
		
        if ($_POST['checkbox2']) {
            $image_url2 = "";
            $th082_url2 = "";
            $th155_url2 = "";
            $th270_url2 = "";
        } else {
			$result2 = $this->model('Logic_Images')->doUploadProduct($form, 'image_url2', 'product');
			if( $result2 ) {
				$image_url2 = $result2['image_url'];
				$th082_url2 = $result2['th082_url'];
				$th155_url2 = $result2['th155_url'];
				$th270_url2 = $result2['th270_url'];
			} else {
                $image_url2 = $product['image_url2'];
                $th082_url2 = $product['th082_url2'];
                $th155_url2 = $product['th155_url2'];
                $th270_url2 = $product['th270_url2'];
            }
        }
		
        if ($_POST['checkbox3']) {
            $image_url3 = "";
            $th082_url3 = "";
            $th155_url3 = "";
            $th270_url3 = "";
        } else {
			$result3 = $this->model('Logic_Images')->doUploadProduct($form, 'image_url3', 'product');
			if( $result3 ) {
				$image_url3 = $result3['image_url'];
				$th082_url3 = $result3['th082_url'];
				$th155_url3 = $result3['th155_url'];
				$th270_url3 = $result3['th270_url'];
			} else {
                $image_url3 = $product['image_url3'];
                $th082_url3 = $product['th082_url3'];
                $th155_url3 = $product['th155_url3'];
                $th270_url3 = $product['th270_url3'];
            }
        }
		
        $model_id = $table->update(
            array(
                'item_number'          	=> $form->getValue('item_number'),
                'name'                 	=> $form->getValue('name'),
                'call_us'              	=> $form->getValue('call_us'),
                'price'                	=> $form->getValue('price'),
                'promo_price'			=> $form->getValue('promo_price'),
                'promo_period_start'	=> $form->getValue('promo_period_start'),
                'promo_period_end'		=> $form->getValue('promo_period_end'),
                'brand_id'				=> $form->getValue('brand_id'),
                'type'					=> $form->getValue('type'),
                'category_id'			=> $form->getValue('category_id'),
                'sub_category_id'		=> $form->getValue('sub_category_id'),
                'sub_category1_id'		=> $form->getValue('sub_category1_id'),
                'sub_category2_id'		=> $form->getValue('sub_category2_id'),
                'image_url1'			=> $image_url1,
                'image_url2'			=> $image_url2,
                'image_url3'			=> $image_url3,
                'th082_url1'			=> $th082_url1,
                'th082_url2'			=> $th082_url2,
                'th082_url3'			=> $th082_url3,
                'th155_url1'			=> $th155_url1,
                'th155_url2'			=> $th155_url2,
                'th155_url3'			=> $th155_url3,
                'th270_url1'			=> $th270_url1,
                'th270_url2'			=> $th270_url2,
                'th270_url3'			=> $th270_url3,
                'description'			=> $form->getValue('description'),
                'status'            	=> $form->getValue('status'),
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
            $table = $this->model('Dao_Product');
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

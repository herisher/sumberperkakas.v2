<?php
/**
 * product
 */
class ProductController extends BaseController {
    const NS_SEARCH = "/product/search";
    const NS_LIST = "/product/list";
	
    /**
     * list
     */
    public function listAction() {
        $session = new Zend_Session_Namespace(self::NS_LIST);
		$disp_crumb = "";
		$render_url = "";
		
		//unset
        $unset = $this->_getParam('all');
		if( $unset == 1 ) {
			Zend_Session::namespaceUnset(self::NS_LIST);
		} elseif( $unset == 2 ) {
			unset($session->main_category_id);
			unset($session->category_id);
			unset($session->sub_category_id);
			unset($session->sub_category2_id);
		} elseif( $unset == 3 ) {
			unset($session->brand_id);
		}
		
        // Initial search criteria
        $where = array(
			'p.disp_flag = ?' => 1, //SHOWN PRODUCT
        );
		
        // order by
        $sort = $this->_getParam('sort');
        if (isset($sort)) {
            $session->sort = $sort;
        }
        $sort =  $session->sort;
        if (!$sort) { $sort = 1; } //latest
        if ($sort == 1) { //latest
            $order_by = 'create_date DESC';
			$render_url .= "/sort/1";
        } elseif ($sort == 2) { //popular
            $order_by = 'viewer DESC';
			$render_url .= "/sort/2";
        } elseif ($sort == 3) { //promo
            $where['p.promo_price != ?'] = 0;
			$render_url .= "/sort/3";
        }
        $this->view->sort = $sort;
		
        // Refine by category
        $main_category_id = $this->_getParam('mcid');
        $category_id = $this->_getParam('cid');
        $sub_category_id = $this->_getParam('scid');
        $sub_category2_id = $this->_getParam('sc2id');
		
        if (isset($main_category_id)) {
            $session->main_category_id = $main_category_id;
            $session->category_id = "";
            $session->sub_category_id = "";
            $session->sub_category2_id = "";
        } elseif (isset($category_id)) {
            $session->main_category_id = "";
            $session->category_id = $category_id;
            $session->sub_category_id = "";
            $session->sub_category2_id = "";
        } elseif (isset($sub_category_id)) {
            $session->main_category_id = "";
            $session->category_id = "";
            $session->sub_category_id = $sub_category_id;
            $session->sub_category2_id = "";
        } elseif (isset($sub_category2_id)) {
            $session->main_category_id = "";
            $session->category_id = "";
            $session->sub_category_id = "";
            $session->sub_category2_id = $sub_category2_id;
        }
		
        $main_category_id =  $session->main_category_id;
        $category_id =  $session->category_id;
        $sub_category_id =  $session->sub_category_id;
        $sub_category2_id =  $session->sub_category2_id;
		
		$this->view->hashtag = "";
        if ($main_category_id) {
            $where['p.category_id = ?'] = $main_category_id;
            $this->view->mcid = $main_category_id;
            $disp_name = $this->model("Logic_Category")->getName($main_category_id);
			$disp_crumb .= 	'&rArr; <a href="/product/list/mcid/'.$main_category_id.'">'.$disp_name.'</a> ';
			$render_url .= "/mcid/".$main_category_id;
        } elseif ($category_id) {
            $this->view->cid = $category_id;
            $where['p.sub_category_id = ?'] = $category_id;
			$cat = $this->model('Dao_SubCategory')->retrieve($category_id);
            $disp_name1 = $this->model("Logic_Category")->getName($cat['category_id']);
            $disp_name = $this->model("Logic_SubCategory")->getName($category_id);
			$disp_crumb .= 	'&rArr; <a href="/product/list/mcid/'.$cat['category_id'].'">'.$disp_name1.'</a> '.
							'&rArr; <a href="/product/list/cid/'.$category_id.'#mc'.$cat['category_id'].'">'.$disp_name.'</a> ';
			$render_url .= "/cid/".$category_id;
			$this->view->hashtag = '#mc'.$cat['category_id'];
        } elseif ($sub_category_id) {
            $this->view->scid = $sub_category_id;
            $where['p.sub_category1_id = ?'] = $sub_category_id;
			$cat = $this->model('Dao_SubCategory1')->retrieve($sub_category_id);
            $disp_name1 = $this->model("Logic_Category")->getName($cat['category_id']);
            $disp_name2 = $this->model("Logic_SubCategory")->getName($cat['sub_category_id']);
            $disp_name = $this->model("Logic_SubCategory1")->getName($sub_category_id);
			$disp_crumb .= 	'&rArr; <a href="/product/list/mcid/'.$cat['category_id'].'">'.$disp_name1.'</a> '.
							'&rArr; <a href="/product/list/cid/'.$cat['sub_category_id'].'#mc'.$cat['category_id'].'">'.$disp_name2.'</a> '.
							'&rArr; <a href="/product/list/scid/'.$sub_category_id.'#c'.$cat['sub_category_id'].'">'.$disp_name.'</a> ';
			$render_url .= "/scid/".$sub_category_id;
			$this->view->hashtag = '#c'.$cat['sub_category_id'];
        } elseif ($sub_category2_id) {
            $this->view->sc2id = $sub_category2_id;
            $where['p.sub_category2_id = ?'] = $sub_category2_id;
			$cat = $this->model('Dao_SubCategory2')->retrieve($sub_category2_id);
            $disp_name1 = $this->model("Logic_Category")->getName($cat['category_id']);
            $disp_name2 = $this->model("Logic_SubCategory")->getName($cat['sub_category_id']);
            $disp_name3 = $this->model("Logic_SubCategory1")->getName($cat['sub_category1_id']);
            $disp_name = $this->model("Logic_SubCategory2")->getName($sub_category2_id);
			$disp_crumb .= 	'&rArr; <a href="/product/list/mcid/'.$cat['category_id'].'">'.$disp_name1.'</a> '.
							'&rArr; <a href="/product/list/cid/'.$cat['sub_category_id'].'#mc'.$cat['category_id'].'">'.$disp_name2.'</a> '.
							'&rArr; <a href="/product/list/scid/'.$cat['sub_category1_id'].'#c'.$cat['sub_category_id'].'">'.$disp_name3.'</a> '.
							'&rArr; <a href="/product/list/sc2id/'.$sub_category2_id.'#sc'.$cat['sub_category1_id'].'">'.$disp_name.'</a> ';
			$render_url .= "/sc2id/".$sub_category2_id;
			$this->view->hashtag = '#sc'.$cat['sub_category1_id'];
        }
		
        $brand_id = $this->_getParam('bid');
        if (isset($brand_id)) {
            $session->brand_id = $brand_id;
        }
		if($brand_id) $this->_redirect('/product/list'.$this->view->hashtag);
        $brand_id =  $session->brand_id;
		
        if ($brand_id) {
            $where['p.brand_id = ?'] = $brand_id;
            $this->view->bid = $brand_id;
            $brand_name = $this->model("Logic_Brand")->getName($brand_id);
			$disp_crumb .= '&rArr; '.$brand_name.' ';
			$render_url .= "/bid/".$brand_id;
        } else {
            $brand_name = "Semua Merk";
			$disp_crumb .= '&rArr; '.$brand_name.' ';
		}
		
        $select = $this->model("Dao_Product")->createWherePhrase($where, $order_by);
        
        // Clear search session
        $_SESSION['keyword'] = '';

        //JOIN
        $select
            ->setIntegrityCheck(false)
            ->from(array('p' => 'dtb_product'))
            ->join(array('b' => 'dtb_brand'), 'p.brand_id = b.id', array('b.name AS brand_name'));

        $this->createNavigator($select, 20);

        // Display customization
        $models = array();
        foreach($this->view->paginator as $model) {
            // promo setting
            $model = $model->toArray();
            $model['current_price'] = $this->model("Logic_Product")->getCurrentPrice($model);
            array_push($models, $model);
        }

        $this->view->models = $models;
        $this->view->disp_crumb = $disp_crumb;
        $this->view->render_url = $render_url;
    }

    /**
     * Product Search
     */
    public function searchAction() {
        // keyword
        $keyword = $this->_getParam('keyword');
        if (isset($keyword)) {
            $_SESSION['keyword'] = $keyword;
			$this->_redirect('/product/search');
        }
        $keyword = $_SESSION['keyword'];
        $this->view->keyword = urlencode($keyword);
		
        // where clause
        $where = array(
			'p.disp_flag = ?' => 1,
        );

        // Search by Keyword
        if (strlen($_SESSION['keyword'])) {
            $table = new Dao_Product();
            $w1 = $table->getAdapter()->quoteInto('p.name LIKE  ?', '%'.$keyword.'%');
            $w2 = $table->getAdapter()->quoteInto('p.description LIKE ?', '%'.$keyword.'%');
            $where["{$w1} OR {$w2}"] = null;
        }

        $select = $this->model("Dao_Product")->createWherePhrase($where, $order_by);

        // JOIN
        $select
            ->setIntegrityCheck(false)
            ->from(array('p' => 'dtb_product'))
            ->join(array('b' => 'dtb_brand'), 'p.brand_id = b.id', array('b.name AS brand_name'));

        $this->createNavigator($select, 20);

        $models = array();
        foreach($this->view->paginator AS $model) {
            // promo setting
            $model = $model->toArray();
            $model['current_price'] = $this->model("Logic_Product")->getCurrentPrice($model);
            array_push($models, $model);
        }

        $this->view->models = $models;
    }

    /**
     * detail
     */
    public function detailAction() {
        // Required parameter check
        $id = $this->_getParam("id");
        if (!$id) {
            $this->view->error_str = 'Produk tidak ada.';
            $this->_forward("error","Error");
            return;
        }

        // That there are no goods, handling stop
        $item = $this->model("Logic_Product")->findById($id);
        if (!$item) {
            $this->view->error_str = 'Produk tidak ada.';
            $this->_forward("error","Error");
            return;
        }

        // promo setting
        $item['current_price'] = $this->model("Logic_Product")->getCurrentPrice($item);
		
		$item['category'] = $this->model("Logic_Category")->getName($item['category_id']);
		$item['sub_category'] = $this->model("Logic_SubCategory")->getName($item['sub_category_id']);
		$item['sub_category1'] = $this->model("Logic_SubCategory1")->getName($item['sub_category1_id']);
		$item['sub_category2'] = $this->model("Logic_SubCategory2")->getName($item['sub_category2_id']);
		
        $this->view->item        = $item;
        $this->view->brand        = $this->model('Dao_Brand')->retrieve($item['brand_id']);
    }
    
    /**
     * preview
     */
    public function previewAction() {
        
    }
}
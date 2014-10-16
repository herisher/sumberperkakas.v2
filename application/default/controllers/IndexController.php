<?php
/**
 * index
 */
class IndexController extends BaseController {
    /**
     * index
     */
    public function indexAction() {
		//produk terbaru
        $models = $this->model('Logic_Product')->getLatestProduct();
        $this->view->latests = $models;
		
		//produk promo
        $models = $this->model('Logic_Product')->getPromoProduct();
        $this->view->promos = $models;
		
		//produk populer
        $models = $this->model('Logic_Product')->getPopularProduct();
        $this->view->popular = $models;
		
		//berita terbaru
		$models = $this->model('Logic_News')->getLatestNews();
        $this->view->news = $models;
		
		/*
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
	*/
    }
}
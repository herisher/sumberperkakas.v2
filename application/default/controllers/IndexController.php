<?php
/**
 * index
 */
class IndexController extends BaseController {
    /**
     * index
     */
    public function indexAction() {
        //image slider
        $models = $this->model('Logic_Images')->getImageSlider();
        $this->view->image_slider = $models;
        
        //produk promo
        $models = $this->model('Logic_Product')->getPromoProduct();
        $this->view->promos = $models;
        
        //produk populer
        $models = $this->model('Logic_Product')->getPopularProduct();
        $this->view->popular = $models;
        
        //brand
        $models = $this->model('Logic_Brand')->getAll();
        $this->view->brand = $models;
        
        //berita terbaru
        $models = $this->model('Logic_News')->getLatestNews();
        $this->view->news = $models;
    }
}
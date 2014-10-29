<?php
/**
 * product
 */
class Logic_Product extends Logic_Base {
    public function findById($id) {
        if (!$id) {
            return null;
        } else {
            return $this->db()->fetchRow("SELECT * FROM `dtb_product` WHERE `id` = ? AND `disp_flag` = 1", $id);
        }
    }
    
    /**
     * current price
     */
    public function getCurrentPrice($product) {
        if ($product['promo_period_start'] && strtotime($product['promo_period_start']) < time() && $product['promo_period_end'] && strtotime($product['promo_period_end'] . " +1 day") > time())
        {
            $current_price = intval($product['promo_price']);
        }
        elseif ($product['promo_period_start'] && strtotime($product['promo_period_start']) < time() && !$product['promo_period_end'])
        {
            $current_price = intval($product['promo_price']);
        }
        else {
            $current_price = intval($product['price']);
        }
        if ($current_price < 0) {
            $current_price = 0;
        }
        return $current_price;
    }

    /**
     * promo price
     */
    public function getPromoPrice($product) {
        if ($product['promo_period_start'] && strtotime($product['promo_period_start']) < time() && $product['promo_period_end'] && strtotime($product['promo_period_end'] . " +1 day") > time())
        {
            $promo_price = intval($product['promo_price']);
        }
        elseif ($product['promo_period_start'] && strtotime($product['promo_period_start']) < time())
        {
            $promo_price = intval($product['promo_price']);
        }
        else {
            $promo_price = 0;
        }
        if ($promo_price < 0) {
            $promo_price = 0;
        }
        return $promo_price;
    }

    /**
     * latest
     */
    public function getLatestProduct($limit = 3) {
        $products = $this->db()->fetchAll(
            "SELECT * FROM dtb_product WHERE disp_flag = 1 ORDER BY create_date DESC LIMIT {$limit}"
        );
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * latest
     */
    public function getPromoProduct($limit = 12) {
        $products = $this->db()->fetchAll(
            "SELECT * FROM dtb_product WHERE disp_flag = 1 AND promo_price != 0 ".
            "ORDER BY create_date DESC LIMIT {$limit}"
        );
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * popular
     */
    public function getPopularProduct($limit = 12) {
        $products = $this->db()->fetchAll(
            "SELECT * FROM dtb_product AS pr WHERE pr.disp_flag = 1
            ORDER BY viewer DESC LIMIT {$limit}"
        );
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * get product by category
     */
    public function getProductByBrand($brand_id = 0) {
        if ($brand_id == 0){
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1");
        } else {
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1 AND brand_id = ? ", $brand_id);
        }
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * get product by category
     */
    public function getProductByCategory($category_id = 0) {
        if ($category_id == 0){
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1");
        } else {
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1 AND category_id = ? ", $category_id);
        }
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * get product by category
     */
    public function getProductBySubCategory($sub_category_id = 0) {
        if ($sub_category_id == 0){
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1");
        } else {
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1 AND sub_category_id = ? ", $sub_category_id);
        }
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * get product by category
     */
    public function getProductBySubCategory1($sub_category1_id = 0) {
        if ($sub_category1_id == 0){
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1");
        } else {
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1 AND sub_category1_id = ? ", $sub_category1_id);
        }
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * get product by sub category II
     */
    public function getProductBySubCategory2($sub_category2_id = 0) {
        if ($sub_category2_id == 0){
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1");
        } else {
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag = 1 AND sub_category2_id = ? ", $sub_category2_id);
        }
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * LIKE search under the trade name
     */
    public function getProductByName($name) {
        $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE disp_flag =1 AND `name` LIKE ?", "%".$name."%");
        
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }
}

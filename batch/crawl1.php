<?php
// ???
function initialize() {
    // ???????????
    define('APPLICATION_PATH', realpath(dirname(__FILE__)));
    set_include_path(APPLICATION_PATH . '/../library' . PATH_SEPARATOR . APPLICATION_PATH . '/../application/models');

    // ??????????????
    date_default_timezone_set('Asia/Jakarta');

    // ?????????
    require_once 'Zend/Loader.php';
    Zend_Loader::registerAutoload();

    // ??????????
    if ((isset($_ENV['BZ_STAGE']) && $_ENV['BZ_STAGE'] === '1') || file_exists('/etc/id_stage')) {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/../application/configs/config.ini', 'staging');
        Zend_Registry::set('config', $config);
    } else {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/../application/configs/config.ini', 'production');
        Zend_Registry::set('config', $config);
    }

    // ???????????
    $db = Zend_Db::factory('Pdo_Mysql', $config -> database);
    $db -> query("SET names utf8");
    $db -> query("SET time_zone = '+7:00'");
    Zend_Db_Table_Abstract::setDefaultAdapter($db);

    // ???????
    ini_set('memory_limit', '-1');
}

function import_category($list, $category_id, $product_code) {
    $config = Zend_Registry::get('config');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    if (count($list) == 0) {
        return;
    }

    $product = $db->fetchRow("SELECT * FROM `dtb_product_shop10` WHERE `category_id` = ? AND `product_code` = ?", array($category_id, $product_code));
    if (!$product) {
        return;
    }

    $level = 1;
    foreach ($list as $item) {
        if (!$item || preg_match("/sub_subcategory/", $item))
            break;

        $sub_category = $db -> fetchRow("SELECT * FROM mtb_sub_category WHERE category_id = ? AND level = ? AND sub_category_name = ?", array($category_id, $level, $item));

        if (!$sub_category) {
            $table = new Dao_SubCategory();
            $sub_category_id = $table -> insert(array('category_id' => $category_id, 'level' => $level, 'sub_category_name' => $item, 'create_date' => date("Y-m-d H:i:s"), ));
        } else {
            $sub_category_id = $sub_category['id'];
        }

        $product_category = $db->fetchRow("SELECT * FROM dtb_product_category10 WHERE product_id = ? AND category_id = ? AND sub_category_id = ?", array($product['id'], $category_id, $sub_category_id));

        if(!$product_category)
        {
            $subcategory_data = array(
            'product_id' => $product['id'],
            'category_id' => $category_id,
            'sub_category_id' => $sub_category_id
            );
            $table_prodcat10 = new Dao_ProductCategory10();
            $table_prodcat10->insert($subcategory_data);
            echo "Insert into dtb_product_category10\n";
            var_dump($subcategory_data);
            echo "---\n";
        }

        $level++;
    }
}

function import_product($html/*, $category_id, $shop_id*/) {
    $config = Zend_Registry::get('config');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $html = str_replace("\r", "", $html);
    $html = str_replace("\n", "", $html);

    $matches = array();
    preg_match_all("/<div class=\"grid_4 item-category-set\">((.|\n)*?)<div class=\"item-info-color\">/", $html, $matches);
    foreach ($matches[0] as $item) {

        $domDocument = new DOMDocument();
        @$domDocument -> loadHTML($item);
        $xmlString = $domDocument -> saveXML();
        $xmlObject = simplexml_load_string($xmlString);
        $detail_url = (string)$xmlObject->body->div->div[0]->a->attributes()->href;
        $image_url = (string)$xmlObject->body->div->div[0]->a->img->attributes()->src;
        $buy_url = 'http://berrybenka.com' . $detail_url;
        $splitted_url = explode("/", $detail_url);
        $category_name = $splitted_url[1];
        $sub_category_name = $splitted_url[2];
        $product_code = $splitted_url[3];
        $detailHtml = @file_get_contents($buy_url);
        if (!$html) {
            return;
        }
        $productDetail = getProductDetail($detailHtml);
        if (!$productDetail) {
            return;
        }

        $fixed_price = $productDetail['fixed_price'];
        $sales_price = $productDetail['sales_price'];
        $product_name = $productDetail['product_name'];
        $product_desc = $productDetail['product_desc'];
        $brand_name = $productDetail['brand_name'];

        // validates data
        $categories = array($category_name, $sub_category_name);
        if (count($categories) < 1) {
            echo "Cannot get categories, skipping product";
            return;
        }

        $product = $db -> fetchRow("SELECT * FROM `dtb_product_shop10` WHERE `category_id` = ? AND `product_code` = ?", array($category_id, $product_code));
        if ($product) {
            return;
        }

        $brand = $db -> fetchRow("SELECT * FROM `mtb_brand` WHERE `brand_name` = ? AND `category_id` = ?", array($brand_name, $category_id));
        if (!$brand) {
            $table = new Dao_Brand();
            $brand_id = $table -> insert(array('category_id' => $category_id, 'brand_name' => $brand_name, 'create_date' => date("Y-m-d H:i:s"), ));
        } else {
            $brand_id = $brand['id'];
        }

        if (!$product_desc) {
            $product_desc = 'no description.';
        }

        $product_data = array(
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'product_code' => $product_code,
            'product_name' => $product_name,
            'product_desc' => $product_desc,
            'fixed_price' => $fixed_price,
            'sales_price' => $sales_price,
            'buy_url' => $buy_url,
            'image_75x75' => '/sample/detail/noimage-75x75.png',
            'image_105x120' => '/sample/detail/noimage-105x120.png',
            'image_150x150' => '/sample/detail/noimage-150x150.png',
            'create_date' => date("Y-m-d H:i:s"),
            'update_date' => date("Y-m-d H:i:s")
         );

        $table = new Dao_ProductShop10();
        $product_id = $table->insert($product_data);

        $image = @file_get_contents($image_url);
        if ($image) {

            $logic_image = new Logic_Image();
            $result = $logic_image->doUploadProduct2($image_url, $shop_id, $product_id);

            $table = new Dao_ProductShop10();
            $table -> update(array('image_75x75' => $result['th075_url'], 'image_105x120' => $result['th105_url'], 'image_150x150' => $result['th150_url'], ), 'id = ' . $product_id);
        }

        import_category(array($category_name, $sub_category_name), $category_id, $product_code);

        echo "insert {$product_id}...\n";
    }
}

function getProductDetail($detailHtml) {

    $matches = array();
    $sales_price = 0;
    preg_match_all("/<div id=\"detail\" class=\"grid_9\">((.|\n)*?)<br \/><br \/>/", $detailHtml, $matches);
    $domDocument = new DOMDocument();
    @$domDocument -> loadHTML($matches[0][0]);
    $xmlString = $domDocument -> saveXML();
    $xmlObject = simplexml_load_string($xmlString);
    $product_name = (string)$xmlObject->body->div->h1;
    $brand_name = (string)$xmlObject->body->div->h2;
    if(isset($xmlObject->body->div->div[1]->span)) {
        $fixed_price = (string)$xmlObject->body->div->div[1]->span[0];
        $sales_price = (string)$xmlObject->body->div->div[1]->span[1];
    } else {
        $fixed_price = (string)$xmlObject->body->div->div[1];
        $sales_price = (string)$xmlObject->body->div->div[1];
    }
    $fixed_price = str_replace("IDR ", "", $fixed_price);
    $fixed_price = str_replace(".", "", $fixed_price);
    $fixed_price = preg_replace( '/\s+/', '', $fixed_price );
    $sales_price = str_replace("IDR ", "", $sales_price);
    $sales_price = str_replace(".", "", $sales_price);
    $sales_price = preg_replace( '/\s+/', '', $sales_price );

    if(isset($xmlObject->body->div->div[3]->p)) {
        $product_desc = (string)$xmlObject->body->div->div[3]->p[0];
    } elseif(isset($xmlObject->body->div->div[3]->div)) {
        $product_desc = (string)$xmlObject->body->div->div[3]->div[0];
    } else {
        $product_desc = (string)$xmlObject->body->div->div[3];
    }

    $detail = array('fixed_price'         => $fixed_price,
                     'sales_price'         => $sales_price,
                     'product_name'      => $product_name,
                     'brand_name'        => $brand_name,
                     'product_desc'        => ltrim($product_desc),
                     );
    return $detail;
}

function createCategoryList() {
    $categoryList = array(
        "dresses",
        "tops",
        "bottoms",
        "outerwear",
        "flats",
        "heels",
        "wedges",
        "boots",
        "sandals",
        "clutch",
        "shoulder%20and%20handbags",
        "belts",
        "necklaces",
        "rings",
        "bracelets",
        "glasses%20and%20sunglasses",
        "hair%20piece",
        "broochs",
        "scarves",
        "earrings",
        "charms",
        "camisole",
        "bra",
        "panties",
        "bikini",
        "sleepwear",
        "shapewear",
        "boys%20shoes",
        "boys%20accessories",
        "boys%20clothing",
        "girls%20clothing",
        "girls%20accessories",
        "kids%20bags",
        "girls%20shoes",
    );
    return $categoryList;
}

function update_shop_total_products($shop_id) {
    $config = Zend_Registry::get('config');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $table_shop = "dtb_product_shop" . $shop_id;
    $total = $db->fetchOne("SELECT COUNT(*) FROM {$table_shop}");
    $date_now = date("Y-m-d H:i:s");
    $q = "UPDATE mtb_shop SET total_products = ?, last_update_products = ? WHERE id = ?";
    $db->query($q, array($total, $date_now, $shop_id));
}

// ??????
function crawl_shop10() {/*
    $config = Zend_Registry::get('config');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    $category_id = 3;    // FASHION
    $shop_id = 10;    // BERRYBENKA

    $categories = createCategoryList();


    $total_products = 0;

    foreach ($categories as $category) {
        for ($page = 1; $page < 1000; $page++) {
            echo "[{$category}] page = {$page}\n";
            $link = 'http://berrybenka.com/product.php?page='.$page.'&mode='.$category;
            $html = @file_get_contents($link);
            if ($html) {
                if (!preg_match("/<div class=\"grid_4 item-category-set\">/", $html)) {
                    break;
                } else {
                    import_product($html, $category_id, $shop_id);
                }
            }
        }
    }

    // ?????????????????
    $db->query("UPDATE dtb_product_shop10 SET disp_flag = 0 WHERE sales_price < 1000");

    update_shop_total_products($shop_id);
    //$link = 'http://www.indonetwork.co.id/sumberperkakas/cert+462/pengesahan-badan-hukum-perseroan.htm';
    $link = 'http://www.rizqategar.com/';
    $html = file_get_contents($link);
        */
    echo "--started1--";
    fopen("/home/shimizu/svn_backup/myvillage/trunk/home/sites/myvillage/batch/cookies.txt", "w");
    $url="http://sumberperkakas.indonetwork.co.id/";
    $ch = curl_init();

    $header=array('GET /1575051 HTTP/1.1',
        'Host: sumberperkakas.indonetwork.co.id',
        'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language:en-US,en;q=0.8',
        'Cache-Control:max-age=0',
        'Connection:keep-alive',
        'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
        );

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
        curl_setopt( $ch, CURLOPT_COOKIESESSION, true);

        curl_setopt($ch,CURLOPT_COOKIEFILE,'/home/shimizu/svn_backup/myvillage/trunk/home/sites/myvillage/batch/cookies.txt');
        curl_setopt($ch,CURLOPT_COOKIEJAR,'/home/shimizu/svn_backup/myvillage/trunk/home/sites/myvillage/batch/cookies.txt');
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $result=curl_exec($ch);

        if($result) {
            if (!preg_match("/<li class=\"sddmenu\">/", $html)) {
                echo "ga masuk preg_match!";
                break;
            } else {
                echo "ga masuk preg_match!";
                //import_product($html, $category_id, $shop_id);
            }
        } else {
            echo "ga ada weks!";
        }
        
        curl_close($ch);
}

// ?????????
function main() {
    initialize();
    crawl_shop10();
}

main();

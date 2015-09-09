<?php
if (!file_exists($mageFilename)) {
    echo 'Mage file not found';
    exit;
}
require $mageFilename;

if (!Mage::isInstalled()) {
    echo 'Application is not installed yet, please complete install wizard first.';
    exit;
}

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}


// emulate index.php entry point for correct URLs generation in API
Mage::register('custom_entry_point', true);
Mage::$headersSentThrowsException = false;
//Mage::init('admin');
Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_GLOBAL, Mage_Core_Model_App_Area::PART_EVENTS);
Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMINHTML, Mage_Core_Model_App_Area::PART_EVENTS);

// query parameter "type" is set by .htaccess rewrite rule
$apiAlias = Mage::app()->getRequest()->getParam('type');

$store = Mage::app()->getStore();
$store_id = $store->getStoreId();

if( isset($_GET['prod_name']) && $_GET['prod_name'] != "" ){

        $prod_name = trim($_GET['prod_name']);
//      $store_id = 1;
        $_product = Mage::getModel('catalog/product')->setStoreId($store_id)->loadByAttribute('name', $prod_name);

        $prod_no_loaded = $_product->getId();
}


$rows_per_page =  $_GET['rows_per_page'];
if (isset($_GET['page_no']) && $_GET['page_no'] != ""){
        $page_no = $_GET['page_no'];
}else{
        $page_no = 1;
}

if (isset($_GET['rows_per_page']) && $_GET['rows_per_page'] != ""){
        $page_limit = $_GET['rows_per_page'];
}else{
        $page_limit = 24;
}



if ( (isset($_GET["prod_no"]) &&  $_GET["prod_no"] != "") || $prod_no_loaded != "" ){

        $prod_no = $_GET["prod_no"];
        if( $prod_no_loaded != "" ){
                $prod_no = $prod_no_loaded;
        }

	$_product = Mage::getModel('catalog/product')->load($prod_no);
	if ($_product->getTypeId() == 'grouped')
	{
	$_associatedProducts = $_product->getTypeInstance(true)->getAssociatedProducts($_product);

			$cnt_associated = count( $_associatedProducts );
			$total_pages = intval($cnt_associated / $page_limit) + 1;

			ob_start();
			$end_no = ( $page_no * $page_limit );
			$start_no = ( $end_no - ( $page_limit - 1 ));

			$arr_prods = array();

			$i = 1;

			foreach( $_associatedProducts as $p){

					if( $i >= $start_no && $i <= $end_no ){
					echo $p->getName();
					echo "<br />";
							$addurl = Mage::helper('checkout/cart')->getAddUrl($p);
							$arr_prods[] = array(
									"id" => $p->getId(),
									"title" => $p->getName(),
									"image" => $p->getImage(),
									"small_image" => $p->getSmallImage(),
									"url" => str_replace("/index.php/", "", $p->getProductUrl()),
									"price" => $p->getPrice(),
									"final_price" => $p->getFinalPrice(),
									"addurl" => $addurl
							);
					}
					$i++;
			}
			ob_end_clean();

			$arr = array(
			"page_limit" => $page_limit,
			"page_no" => $page_no,
			"total_pages" => $total_pages,
			"total_prods" => $cnt_associated,
			"start_no" => $start_no,
			"end_no" => $end_no,
			"prods" => $arr_prods
			);

			echo json_encode($arr);
	}
	else{

			echo "Unknown type";
	}
}else{
        echo "No Group";
}
?>
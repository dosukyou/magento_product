<?php
// Sample Developement for Custom Bundle Product

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
}
$prod_no = 1021;
$bundled_product = new Mage_Catalog_Model_Product();
$bundled_product->load($prod_no);

$typeInstance = $bundled_product->getTypeInstance(true);
$selectionCollection = $typeInstance->getSelectionsCollection(
	$typeInstance->getOptionsIds($bundled_product), $bundled_product
);

$optionCollection = $typeInstance->getOptionsCollection($bundled_product);

$_options = $optionCollection->appendSelections($selectionCollection, false,
		Mage::helper('catalog/product')->getSkipSaleableCheck()
		);

ob_start();
$bundle_option = array();
$arr_bundle_info = array();

    //$arr_bundle_info["id"] = $prod_no;

    foreach ($_options as $key => $value) {
        $default_title = $value->getData('default_title');
        $option_id = $value->getData('option_id');
        $input_type = $value->getData('type');
        $req = $value->getData('required');
		$arr_bundle_items_info = array();
        foreach ($value['selections'] as $selKey => $selection) {
              $arr_bundle_items_info[] = array(
                "id" => $selection->getId(),
                "selid" => $selection->getSelectionId(),
                "isdef" => $selection->getIsDefault(),
                "pos" => $selection->getPosition(),
                "name" => $selection->getName(),
                "sku" => $selection->getSku(),
                "orig_price" => $selection->getPrice()
                );
       }
        $arr_bundle_info[] = array(
                "option_id" =>$option_id,  "default_title" => $default_title,
                "type" => $input_type, "required" => $req,
                "bundle_items" => $arr_bundle_items_info
        );
	}

ob_end_clean();
print json_encode( $arr_bundle_info);
	
?>
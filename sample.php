<?php

// Get all active categories in Magento
$categories = Mage::getModel('catalog/category')
					->getCollection()
					->addAttributeToSelect('*')
					->addIsActiveFilter();

print_r( $categories );


// Get all products lists w/ a specific category in Magento

$category_no = 96;   // change the input value 
$products = Mage::getModel('catalog/category')->load($category_no);
$productslist = $products->getProductCollection()->addAttributeToSelect('*');
foreach($productslist as $prod){
        echo $prod->getId();
        echo "<br />";
        echo $prod->getName();
	echo "<hr />";
}



// Get all associated products and information w/ a grouped product 

$group_product_no  =  381;  // change the input value 
$product = Mage::getModel('catalog/product')->load($group_product_no);
$associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
foreach ($associatedProducts as $_product) {

        print $_product->getId();
        echo "<br />";
        print $_product->getIsInStock();

}


// Get the product information w/ a simple product.

$product_no = 20;  // change the input value 
$product = Mage::getModel('catalog/product')->load($product_no);

print_r($product);

?>
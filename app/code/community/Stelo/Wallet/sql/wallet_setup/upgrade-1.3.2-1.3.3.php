<?php

    //Create Customer Group 
  	$targetGroup = Mage::getModel('customer/group');
  	$targetGroup->load('Stelo', 'customer_group_code');
	if(!$targetGroup->getId()){
	    $targetGroup = $targetGroup->setData( array('customer_group_code' => 'Stelo','tax_class_id' => 3));
	    $targetGroup->save();

	    // CÓDIGO PARA REINDEXAR OS CATALOG PRODUCT PRICE 
	    $process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price');
		$process->reindexAll();
	}

?>
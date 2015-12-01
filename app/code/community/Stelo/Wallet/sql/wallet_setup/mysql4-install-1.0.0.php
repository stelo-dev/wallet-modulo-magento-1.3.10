<?php
/* 
 * Stelo Payment module
 * Developed By Rodrigo Ribeiro  
 * https://br.linkedin.com/in/rodrigoferreirasantosribeiro
 * 
 * Funded by Stelo
 * http://www.stelo.com.br/
 * 
 * License: OSL 3.0
 */
$installer = $this;

$installer->startSetup();

$query = "Create table stelo_wallet (id int not null auto_increment, mage_id varchar(15) not null, stelo_id varchar(20) not null, status VARCHAR(20) not null, PRIMARY KEY (id))";

$installer->run($query);

$installer->endSetup();
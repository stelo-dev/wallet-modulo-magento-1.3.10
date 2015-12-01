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

$query = "alter table stelo_wallet add stelo_url varchar(200)";

$installer->run($query);

$installer->endSetup();
<?php
if (function_exists('geoip_country_code_by_name') === FALSE) {
	function geoip_country_code_by_name($ip) {
		return NULL;
	}
}

if (function_exists('geoip_country_name_by_name') === FALSE) {
	function geoip_country_name_by_name($ip) {
		return NULL;
	}
}

$container = require __DIR__ . '/../app/bootstrap.php';
$container->getByType('Nette\Application\Application')->run();

<?php
include 'mysql_connect.php';
	$data = file_get_contents('http://www.bank.gov.ua/control/uk/curmetal/detail/currency?period=daily');
	$data = substr($data, strpos($data, 'Офіційний курс</td>'));
	$data = substr($data, strpos($data, '<td'), strpos($data, '</table>'));
	$data2 = file_get_contents('http://www.bank.gov.ua/control/uk/curmetal/detail/currency?period=monthly');
	$data2 = substr($data2, strpos($data2, 'Офіційний курс</td>'));
	$data2 = substr($data2, strpos($data2, '<td'), strpos($data2, '</table>'));
	$data = trim(preg_replace('/\s{2,}/', ' ', strip_tags(str_replace('</td>', ';', $data)))) . trim(preg_replace('/\s{2,}/', ' ', strip_tags(str_replace('</td>', ';', $data2))));
	$array = array();
	$currency = array();
	$j = 0;
	while ($data) {
		for($i = 0; $i < 5; $i++) {
			$array[$j][$i] = trim(substr($data, 0, strpos($data, ';')));
			$data = substr($data, strpos($data, ';')+1);
		}
		$currency_code_result = mysqli_query($CONNECT, "SELECT DISTINCT `currency_code` FROM `currency`;");
		while ($currency_code = mysqli_fetch_assoc($currency_code_result)) {
			if ($array[$j][1] == $currency_code['currency_code']) {
				$exchange_rate = $array[$j][4] / $array[$j][2];
				mysqli_query($CONNECT, "UPDATE `currency` SET `exchange_rate` = '$exchange_rate' WHERE `currency_code` = '$currency_code[currency_code]';");
			}
		}
		$price_query = mysqli_query($CONNECT, "SELECT DISTINCT `price`, `id_currency` FROM `one_tour`;");
		while($price = mysqli_fetch_assoc($price_query)) {
			$price = price($price['price'],$price['id_currency'],1);
			mysqli_query($CONNECT, "UPDATE `one_tour` SET `h_price` = '$price';");
		}
		$j++;
		if (!strpos($data, ';')) $data = '';
	}
	mysqli_query($CONNECT, "UPDATE `other` SET `date_update_currency` = '".date('Y-m-d')."';");
	var_dump($array);
	// %progdir%\modules\php\%phpdriver%\php-win.exe -c %progdir%\modules\php\%phpdriver%\php.ini -q -f %sitedir%\ch.ua\update_currency.php

?>
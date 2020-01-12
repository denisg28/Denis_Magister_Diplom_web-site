<?
	$country = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `name`, `id_currency`, `description`, `photo` FROM `countries` WHERE `code` = '$Module';"));
	$currency = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `name`, `symbol`, `currency_code`, `exchange_rate` FROM `currency` WHERE `id` = '$country[id_currency]';"));
	$_SESSION['tour_query'] = "SELECT `id`, `name`, `itinerary`, `duration`, `short_description`, `photo` FROM `tour` WHERE `countries` LIKE '%$country[id]%'";
	$tours_query = mysqli_query($CONNECT, $_SESSION['tour_query']. " LIMIT 10");
head($country['name']);?>
<div class="box clearfix">
<div class="clearfix">
	<img src="/resource/img/flags/<? echo $Module?>.png" class="flag">
	<h1><? echo $country['name']?></h1>
</div>
<?if ($country['photo']) {?>
	<ul class="slider">
		<?
			$photo = explode('|',$country['photo']);
			foreach ($photo as $num => $file_name) {?>
			<li<?if ($num == 0) echo ' class="active"'?>><img src="/resource/img/galery/original/<?echo $file_name;?>.jpg"></li>
			<?}
		?>
		<div class="prev icon">left</div>
		<div class="next icon">right</div>
		<div class="pause icon">pause</div>
	</ul>
<?}?>
<p><strong>Валюта:</strong>
<?
	echo ' '.$currency['name'] . ((!($country['id_currency'] == $_SESSION['USER_CURRENCY']))?(' (1 ' . (($currency['symbol'])?$currency['symbol']:$currency['currency_code']) . ' = ' . price(1,$country['id_currency'],$_SESSION['USER_CURRENCY']) . ')'):'');
?>
</p>
<h3>Опис країни:</h3>
<p><? echo $country['description']; ?></p>
	<h3>Тури</h3>
	<div class="tour-list">
		<? tours($tours_query);?>
	</div>
</div>

<?foot();?>
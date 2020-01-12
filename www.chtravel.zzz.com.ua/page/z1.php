<?php
	$city_query = mysqli_query($CONNECT, "SELECT `id`, `name` FROM `start_city`;");
	$currency_query = mysqli_query($CONNECT, "SELECT `id`, `name`, `currency_code` FROM `currency`;");
	head('Профіль');
?>
	<div class="box clearfix">
		<img class="avatar" src="/resource/img/avatar/original/<? echo (($_SESSION['USER_FORMAT'])?$_SESSION['USER_ID'].$_SESSION['USER_FORMAT']:'0.jpg'); ?>">
		<div class="legend"><strong>Логін:</strong> <? echo $_SESSION['USER_LOGIN']; ?></div>
		<div class="legend"><strong>E-mail:</strong> <? echo $_SESSION['USER_EMAIL'] ?></div>
		<div class="legend"><strong>Оберіть місто від'їзду:</strong></div>
			<select name="change_start_city">
				<option value="0">——————————</option>
				<?while ($city = mysqli_fetch_assoc($city_query)) {;?>
					<option value="<? echo $city['id']; ?>" <? if ($city['id'] == $_SESSION['USER_CITY']) echo 'selected'?>><? echo $city['name'] ?></option>
				<?}?>
			</select>
		<div class="legend"><strong>Оберіть валюту, в якій будуть відображатися всі ціни:</strong></div>
			<select name="change_user_currency">
				<option value="0">——————————</option>
				<?while ($currency = mysqli_fetch_assoc($currency_query)) {;?>
					<option value="<? echo $currency['id']; ?>" <? if ($currency['id'] == $_SESSION['USER_CURRENCY']) echo 'selected'?>><? echo $currency['name'] ?></option>
				<?}?>
			</select>
	</div>
<?php foot(); ?>
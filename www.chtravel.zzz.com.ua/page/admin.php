<?php

if ($_POST['add_news']) {
	$_POST['caption'] = form_chars($_POST['caption']);
	$_POST['text'] = form_chars($_POST['text']);
	if (!$_POST['caption'] || !$_POST['text']) {
		$_SESSION['news_caption'] = $_POST['caption'];
		$_SESSION['news_text'] = $_POST['text'];
		message_send(1,'Не всі поля заповнені','/admin/news');
	}
	else {
		mysqli_query($CONNECT, "INSERT INTO `news` VALUES ('', '".date('Y-m-d')."', '$_POST[caption]', '$_POST[text]');");
		$_SESSION['news_caption'] = null;
		$_SESSION['news_text'] = null;
		message_send(3,'Новину успішно створено','/admin/news');
	}
}

if ($_POST['save_news']) {
	$_POST['caption'] = form_chars($_POST['caption']);
	$_POST['text'] = form_chars($_POST['text']);
	mysqli_query($CONNECT, "UPDATE `news` SET `date` = '".date('Y-m-d')."', `caption` = '$_POST[caption]', `text` = '$_POST[text]' WHERE `id`= '$Param[0]';");
	message_send(3,'Новину успішно змінено',"/admin/news/$Param[0]/edit");
}

if ($Module == 'news' && $Param[1] == 'delete') {
	mysqli_query($CONNECT, "DELETE FROM `news` WHERE `id` = '$Param[0]';");
	message_send(3,'Новину успішно видалено','/admin/news');
}

if ($_POST['save_about_us']) {
	$_POST['about_us'] = form_chars($_POST['about_us']);
	mysqli_query($CONNECT, "UPDATE `other` SET `about` = '$_POST[about_us]';");
	message_send(3,'Текст успішно оновлено','/admin/about');
}

if (!$Module) head('Панель керування');
?>

<? if ($Module == 'orders') {
	head('Замовлення');
	$order_query = mysqli_query($CONNECT,"SELECT `id`, `state`, `date`, `id_one_tour`, `id_user`, `seats` FROM `orders` ORDER BY `state` DESC");?>
	<div class="order-page">
		<h1>Замовлення</h1>
		<div class="btn-box">
			<input type="submit" value="Скасувати" name="cancel_order">
			<input type="submit" value="Позначити як виконане" name="check">
			<input type="submit" value="Позначити як непрочитане" name="uncheck">
		</div>
		<table class="orders">
			<thead>
				<tr>
					<th>Номер</th>
					<th>Статус</th>
					<th>Дата замовлення</th>
					<th>Тур</th>
					<th>Дата від'їзду</th>
					<th>Місць</th>
					<th>Вартість</th>
					<th>Логін</th>
					<th>ПІБ</th>
					<th>Телефон</th>
				</tr>
			</thead>
			<tbody>
				<?while ($order = mysqli_fetch_assoc($order_query)) {
					$one_tour = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id_tour`, `departure_date`, `price` FROM `one_tour` WHERE `id` = '$order[id_one_tour]'"));
					$tour = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `name` FROM `tour` WHERE `id` = '$one_tour[id_tour]'"));
					$currency = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `symbol`, `currency_code` FROM `currency` WHERE `id` = '$one_tour[id_currency]'"));
					$user = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `pib`, `phone` FROM `users` WHERE `id` = '$order[id_user]'"))?>
				<tr>
					<td>№ <?echo $order['id'];?></td>
					<td>
						<select class="order-state <?echo order_state_color($order[state]);?>-bg" rel="<?echo $order['id'];?>">
							<?for($i = 1; $i <= 7; $i++) {?>
								<option value="<?echo $i;?>" class="<?echo order_state_color($i);?>-bg" <?if ($i == $order['state']) echo 'selected'?>><?echo order_state($i);?></option>
							<?}?>
						</select>
					</td>
					<td><?echo $order['date'];?></td>
					<td><?echo $tour['name'];?></td>
					<td><?echo $one_tour['departure_date'];?></td>
					<td><?echo $order['seats']?></td>
					<td><?echo ($one_tour['price'] * $order['seats']).(($currency['symbol'])?$currency['symbol']:$currency['currency_code'])?></td>
					<td><?echo $user['login'];?></td>
					<td><?echo $user['pib'];?></td>
					<td><? echo tel($user['phone']); ?></td>
				</tr>
				<?}?>
			</tbody>
		</table>
	</div>
<?}

if ($Module == 'news') {
	if (!$Param) {
	head('Новини');?>
	<h1>Новини</h1>
	<form action="/admin/news" method="POST">
		<div class="block clearfix">
			<h4>Заголовок новини:</h4>
			<input type="text" name="caption" class="small-textfield" maxlength="100" value="<? echo $_SESSION['news_caption']; ?>">
		</div>
		<h4>Текст новини:</h4>
		<textarea class="max-height" name="text"><? echo str_replace('<br>','',$_SESSION['news_text']); ?></textarea>
		<input type="submit" value="Додати новину" name="add_news">
	</form>
	<h2>Наявні новини</h2>
	<div class="news-list">
	<?$result_news = mysqli_query($CONNECT, "SELECT DISTINCT `id`, `date`, `caption`, `text` FROM `news` ORDER BY `date` DESC LIMIT 0 , 10;");
		while($news = mysqli_fetch_array($result_news)) {?>
			<div class="news-box">
				<div class="news-btn">
					<div class="icon" title="Редагувати"><a href="/admin/news/<?echo $news[id];?>/edit">edit</a></div>
					<div class="icon" title="Видалити"><a href="/admin/news/<?echo $news[id];?>/delete">delete</a></div>
				</div>
				<div class="caption-box clearfix">
					<div class="date-box">
						<div class="month"><? echo uk_month(date('m', strtotime($news['date']))); ?></div>
						<div class="day"><? echo date('j', strtotime($news['date'])); ?></div>
						<div class="year"><? echo date('Y', strtotime($news['date'])); ?></div>
					</div>
					<h3><a href="/admin/news/<?echo $news[id];?>"><? echo $news['caption']; ?></a></h3>
				</div>
				<p><? echo $news['text']; ?></p>
			</div>
		<?}?>
		</div>
<?}
if ($Param) {
	$news = mysqli_fetch_array(mysqli_query($CONNECT, "SELECT `id`, `date`, `caption`, `text` FROM `news` WHERE `id` = $Param[0];"));
	head("$news[caption] | Новини");
	if (!$Param[1]) {?>
	<div class="news-box">
		<div class="news-btn">
			<div class="icon" title="Редагувати"> <a href="/admin/news/<?echo $news[id];?>/edit">edit</a></div>
			<div class="icon" title="Видалити"><a href="/admin/news/<?echo $news[id];?>/delete">delete</a></div>
		</div>
		<div class="caption-box clearfix">
			<div class="date-box">
				<div class="month"><? echo uk_month(date('m', strtotime($news['date']))); ?></div>
				<div class="day"><? echo date('j', strtotime($news['date'])); ?></div>
				<div class="year"><? echo date('Y', strtotime($news['date'])); ?></div>
			</div>
			<h3><? echo $news['caption']; ?></h3>
		</div>
		<p><? echo $news['text']; ?></p>
	</div>
<?}}
	if ($Param[1]=='edit') {?>
<form action="/admin/news/<?echo $Param[0];?>/edit" method="POST">
	<div class="block clearfix">
		<h4>Заголовок новини:</h4>
		<input type="text" name="caption" class="small-textfield" maxlength="100" value="<? echo $news['caption']; ?>">
	</div>
	<h4>Текст новини:</h4>
	<textarea class="max-height" name="text"><? echo str_replace('<br>','',$news['text']); ?></textarea>
	<input type="submit" value="Зберегти зміни" name="save_news">
</form>
<?}
}
if ($Module == 'tours') {
	head('Тури');
	if ($Param[0] == 'create')?>
	<h1>Тури</h1>
	<div class="tour-block">
		<form action="/admin" method="POST">
			<div class="clearfix">
				<div class="column-3">
					<h4>Назва туру:</h4>
					<input type="text" name="name" maxlength="100" value="<? echo $_SESSION['tour_name']; ?>">
					<h4>Тривалість туру:</h4>
					<input type="text" name="duration" maxlength="100" value="<? echo $_SESSION['tour_duration']; ?>">
				</div>
				<div class="column-3 list-block itinerary-block">
					<h4>Маршрут туру:</h4>
					<div class="field-list">
						<div class="field-block">
							<input type="text" name="itinerary[]" maxlength="100" value="<? echo $_SESSION['tour_itinerary']; ?>">
							<div class="btn-block">
								<div class="icon">add</div>
								<div class="icon">close</div>
							</div>
						</div>
					</div>
				</div>
				<div class="column-3 list-block documents-block">
					<h4>Документи необхідні для туру:</h4>
					<div class="field-list">
						<div class="field-block">
							<input type="text" name="documents[]" maxlength="100" value="<? echo $_SESSION['tour_documents']; ?>">
							<div class="btn-block">
								<div class="icon">add</div>
								<div class="icon">close</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<h4>Опис туру:</h4>
			<textarea class="max-height" name="about_us"></textarea>
			<input type="submit" value="Додати тур" name="add_tour">
		</form>
	</div>
<?}

if ($Module == 'countries') {
	head('Країни');?>
	<h1>Країни</h1>
	<form action="admin.php" method="POST">
		<div class="block clearfix">
			<h4>Назва країни:</h4>
			<input type="text" name="caption" class="small-textfield" maxlength="100">
		</div>
		<h4>Опис країни:</h4>
		<textarea class="max-height" name="about_us"></textarea>
		<input type="submit" value="Додати країни" name="add_country">
	</form>
<?}

if ($Module == 'currency') {
	head('Валюти');?>
	<h1>Валюти</h1>
	<?
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
			$j++;
			if (!strpos($data, ';')) $data = '';
		}
		mysqli_query($CONNECT, "UPDATE `other` SET `date_update_currency` = '".date('Y-m-d')."';");
		// %progdir%\modules\php\%phpdriver%\php-win.exe -c %progdir%\modules\php\%phpdriver%\php.ini -q -f %sitedir%\ch.ua\update_currency.php
		$currency_query = mysqli_query($CONNECT,"SELECT `id`, `currency_code`, `name`, `symbol`, `exchange_rate` FROM `currency`");
	?>
	<table>
		<tr>
			<th>Код</th>
			<th>Назва</th>
			<th>Символ</th>
			<th>Курс</th>
			<th></th>
			<th></th>
		</tr>
		<?while ($currency = mysqli_fetch_assoc($currency_query)) {?>
		<tr>
			<td><?echo $currency['currency_code']; ?></td>
			<td><?echo $currency['name']; ?></td>
			<td><?echo $currency['symbol']; ?></td>
			<td><?echo $currency['exchange_rate']; ?></td>
			<td class="icon">edit</td>
			<td class="icon">delete</td>
		</td>
		<?}?>
	</table>
<?}

if ($Module == 'about') {
	head('Про нас');
	$about = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `about` FROM `other`;"));
	?>
	<h1>Про нас</h1>
	<form action="/admin/about" method="POST">
		<h4>Текст:</h4>
		<textarea class="max-height" name="about_us"><? echo str_replace('<br>','',$about['about']); ?></textarea>
		<input type="submit" value="Зберегти" name="save_about_us">
	</form>
<?}
	foot(); ?>
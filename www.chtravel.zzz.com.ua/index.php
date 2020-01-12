<?php
require 'mysql_connect.php';
session_start();


if ($_SESSION['USER_ACTIVE'] != 1 and $_COOKIE['user']) {
	$row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `login`, `pib`, `email`, `password`, `phone`, `id_start_city`, `id_currency` FROM `users` WHERE `password` = '$_COOKIE[user]';"));
	$_SESSION['USER_ID'] = $row['id'];
	$_SESSION['USER_LOGIN'] = $row['login'];
	$_SESSION['USER_EMAIL'] = $row['email'];
	$_SESSION['USER_PIB'] = $row['pib'];
	$_SESSION['USER_PHONE'] = $row['phone'];
	$_SESSION['USER_CITY'] = $row['id_start_city'];
	$_SESSION['USER_CURRENCY'] = $row['id_currency'];
	$_SESSION['USER_FORMAT'] = $row ['avatar_format'];
	$_SESSION['USER_ACTIVE'] = 1;
}

if ($_SERVER['REQUEST_URI'] == '/') {
	$Page = 'index';
	$Module = 'index';
}
else {
	$URL_Path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$URL_Parts = explode('/', trim($URL_Path, ' /'));
	$Page = array_shift($URL_Parts);
	$Module = array_shift($URL_Parts);

	if (!empty($Module)) {
		$Param = array();
		for ($i = 0; $i < count($URL_Parts); $i++) {
			$Param[$i] = $URL_Parts[$i];
		}
	}
}

if ($Page != 'captcha') include ('page/' . $Page . '.php');

	function head($title)
	{?>
<!DOCTYPE html>
<html lang="uk">
	<head>
		<meta charset="utf-8">
		<link rel="icon" href="/resource/img/ch.ico">
		<link rel="stylesheet" href="/resource/main.css">
		<title><? echo $title ?> | Чумацький шлях</title>
	</head>

	<body <? if ($_SESSION['USER_ACTIVE'] == 2) echo 'class="admin"' ?> >

		<div class="header clearfix">
			<div class="box">
				<div class="clearfix">
					<div id="logo">
						<a href="/" class="whole"><div class="logo-img"></div>
							<span>Чумацький шлях</span></a>
					</div>
					<? ($_SESSION['USER_ACTIVE'] == 2) ? admin_menu() : menu();?>
				</div>
			</div>
		</div>
		<div class="content clearfix">
		<? echo message_show(); ?>
	<?}

	function login_box() { ?>
		<li><div class="login">Увійти</div></li>
	<?}

	function user_box() { ?>
		<li class="user-box"><a class="whole" href="/profile"><img class="user-icon" src="/resource/img/avatar/icons/<? echo (($_SESSION['USER_FORMAT'])?$_SESSION['USER_ID'].$_SESSION['USER_FORMAT']:'0.jpg'); ?>"><? echo $_SESSION['USER_LOGIN'] ?></a></li>
		<li><a class="whole" href="/account/logout">Вийти</a></li>
	<?}

	function menu() { ?>
		<ul class="menu">
			<li><a class="whole" href="/tours">Тури</a></li>
			<li><a class="whole" href="/countries">Країни</a></li>
			<li><a class="whole" href="/about">Про нас</a></li>
			<? ($_SESSION['USER_ACTIVE'] == 1) ? user_box() : login_box(); ?>
			<li><div class="currency"><? require 'mysql_connect.php'; if (!$_SESSION['USER_CURRENCY']) {$_SESSION['USER_CURRENCY'] = 1;} $currency = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `currency_code` FROM `currency` WHERE `id` = '$_SESSION[USER_CURRENCY]';")); echo $currency['currency_code']; ?></div></li>
			<li><div class="icon search">search</div></li>
			<div class="nav-line"></div>
		</ul>
	<?}

	function admin_menu() { ?>
		<ul class="menu">
			<li><a class="whole" href="/admin/orders">Замовлення</a></li>
			<li><a class="whole" href="/admin/news">Новини</a></li>
			<li><a class="whole" href="/admin/tours">Тури</a></li>
			<li><a class="whole" href="/admin/countries">Країни</a></li>
			<li><a class="whole" href="/admin/currency">Валюти</a></li>
			<li><a class="whole" href="/admin/about">Про нас</a></li>
			<? user_box();?>
			<li><div class="icon search">search</div></li>
			<div class="nav-line"></div>
		</ul>
	<?}

	function foot($js = array())
	{?>
		</div>
		<div class="footer">
			© 2015 "Чумацький шлях" Всі права захищені.
		</div>
		<script type="text/javascript" src="/resource/jquery-2.1.3.min.js"></script>
		<script type="text/javascript" src="/resource/script.js"></script>
		<?
		foreach ($js as $name) {
			echo '<script src="resource/' . $name . '.js">';
		}
		?>
	</body>
</html>
<?}

function form_chars($p1) {
	return nl2br(htmlspecialchars(trim($p1), ENT_QUOTES), false);
}

function gen_pass($p1, $p2 = '') {
	return md5('2E0' . md5($p1 . '0V7') . md5('20' . $p2 . '1A4'));
}

function active_account($p1) {
	if ($p1 <= 0 && $_SESSION['USER_ACTIVE'] != $p1) message_send(3, 'Дана сторінка доступна тільки для гостей!', '/');
	else if ($_SESSION['USER_ACTIVE'] != $p1) message_send(3, 'Дана сторінка доступна тільки для користувачів!', '/');
}

function message_send($p1, $p2, $p3 = '') {
	if ($p1 == 1) {$p1 = 'Помилка'; $col = 'red-bg'; $icon = 'ban';}
	elseif ($p1 == 2) {$p1 = 'Підсказка'; $col = 'blue-bg'; $icon = 'info';}
	elseif ($p1 == 3) {$p1 = 'Повідомлення'; $col = 'green-bg'; $icon = 'ok';}
	$_SESSION['message'] = '<div class="message-box '.$col.'"><div class="icon">'.$icon.'</div><h2>'.$p1.'</h2><p>'.$p2.'</p><div class="close-message icon">close</div></div>';
	if (!$p3) $p3 = $_SERVER['HTTP_REFERER'];
	exit(header('Location: ' . $p3));
}

function message_send_ajax($p1, $p2) {
	if ($p1 == 1) {$p1 = 'Помилка'; $col = 'red-bg'; $icon = 'ban';}
	elseif ($p1 == 2) {$p1 = 'Підсказка'; $col = 'blue-bg'; $icon = 'info';}
	elseif ($p1 == 3) {$p1 = 'Повідомлення'; $col = 'green-bg'; $icon = 'ok';}
	return '<div class="message-box '.$col.'"><div class="icon">'.$icon.'</div><h2>'.$p1.'</h2><p>'.$p2.'</p><div class="close-message icon">close</div></div>';
}

function message_show() {
	if ($_SESSION['message']) $message = $_SESSION['message'];
	return $message;
}

function seats_color($seats) {
	if ($seats >= 10) return 'green';
	else if ($seats >=5) return 'orange';
	else if ($seats > 0) return 'red';
	else return 'grey';
}

function seats_description($seats) {
	if ($seats > 0) return 'Є ' . $seats . ' ' . 
		((($seats % 10 == 0) || ($seats % 10 >= 5) || (($seats>=11) && ($seats <=15)))?'місць':(($seats % 10 >=2)?'місця':'місце'));
	else return 'Немає місць';
}

function tel($num) {
	$num = substr_replace($num, ' ', 3, 0);
	$num = substr_replace($num, ' ', 7, 0);
	$num = substr_replace($num, ' ', 11, 0);
	$num = substr_replace($num, ' ', 14, 0);
	return $num;
}

function uk_month($month) {
	
	switch ($month) {
		case '01': {$month = 'Січ'; break;}
		case '02': {$month = 'Лют'; break;}
		case '03': {$month = 'Бер'; break;}
		case '04': {$month = 'Квіт'; break;}
		case '05': {$month = 'Трав'; break;}
		case '06': {$month = 'Чер'; break;}
		case '07': {$month = 'Лип'; break;}
		case '08': {$month = 'Сер'; break;}
		case '09': {$month = 'Вер'; break;}
		case '10': {$month = 'Жов'; break;}
		case '11': {$month = 'Лист'; break;}
		case '12': {$month = 'Груд'; break;}
	}
	return $month;
}

function days($days) {
	echo  $days . ' ' . ((($days % 10 == 0) || ($days % 10 >= 5) || (($days>=11) && ($days <=15)))?'днів':(($days % 10 >=2)?'дня':'день'));
}

function price($price, $currency1, $currency2, $without = 0) {
	include 'mysql_connect.php';
	$currency1 = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `exchange_rate` FROM `currency` WHERE `id` = '$currency1';"));
	$currency2 = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `symbol`, `currency_code`, `exchange_rate` FROM `currency` WHERE `id` = '$currency2';"));
	$price = $price * $currency1['exchange_rate'] / $currency2['exchange_rate'];
	if (strpos($price, '.') > 0) $price = substr($price, 0, strpos($price, '.')+3);
	return $price . ' ' . (($without)?'':(($currency2['symbol'])?$currency2['symbol']:$currency2['currency_code']));
}

function tours($tours_query) {?>
		<? while ($tours = mysqli_fetch_assoc($tours_query)) {?>
		<div class="wrapper-preview-tour-box">
			<div class="preview-tour-box clearfix">
				<a href="/tour/<? echo $tours['id']; ?>" class="block">
					<div class="image-box">
						<img src="/resource/img/galery/preview/<?echo (($tours['photo'])?substr($tours['photo'],0,strpos($tours['photo'],'|')):'0');?>.jpg" alt="Фото">
						<div class="price-duration-box">
							<div class="price"><? require 'mysql_connect.php'; $price = mysqli_fetch_array(mysqli_query($CONNECT, "SELECT MIN(`price`), `id_currency` FROM `one_tour` WHERE `id_tour` = '$tours[id]';")); echo price($price[0], $price[1], $_SESSION['USER_CURRENCY']);?></div>
							<div class="duration"><? days($tours['duration']) ?></div>
						</div>
					</div>
				<h3><? echo $tours['name']; ?></h3>
				</a>
				<div><strong>Маршрут:</strong> <? echo $tours['itinerary']; ?></div>
				<div class="short-description"><? echo $tours['short_description']; ?></div>
			</div>
		</div>
		<?}?>
<?}

function order_state($state) {
	switch ($state) {
		case '1':{return 'Тур відбувся'; break; }
		case '2':{return 'В подорожі'; break; }
		case '3':{return 'Скасовано'; break; }
		case '4':{return 'Опрацьовано'; break; }
		case '5':{return 'Неопрацьовано'; break; }
		case '6':{return 'Зміни в турі'; break; }
		case '7':{return 'Скасовано тур'; break; }
	}
}

function order_state_color($state) {
	switch ($state) {
		case '1':{return 'grey'; break; }
		case '2':{return 'blue'; break; }
		case '3':{return 'red'; break; }
		case '4':{return 'green'; break; }
		case '5':{return 'orange'; break; }
		case '6':{return 'purple'; break; }
		case '7':{return 'red'; break; }
	}
}

function comments($location) {
	require 'mysql_connect.php';
	$comment_query = mysqli_query($CONNECT, "SELECT `id`, `id_user`, `text`, `datetime` FROM `comments` WHERE `location` = '$location' ORDER BY `datetime` DESC");
	while ($comment = mysqli_fetch_assoc($comment_query)) {
		$user = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `avatar_format`, `login` FROM `users` WHERE `id` = '$comment[id_user]' LIMIT 1"));?>
		<div class="comment-box" rel="<?echo $comment['id'];?>">
			<?if ($_SESSION['USER_ID'] == $comment['id_user']) {?> <div class="del-comment">Видалити коментар</div> <?}?>
			<table>
				<tr>
					<td>
						<img src="/resource/img/avatar/icons/<? echo (($user['avatar_format'])?$user['id'].$user['avatar_format']:'0.jpg'); ?>" class="comment-icon">
					</td>
					<td class="comment-author"><?echo $user['login']?></td>
				</tr>
			</table>
			<p> <span class="icon">calendar</span> <?echo date_format(date_create($comment['datetime']),'d.m.Y H:i');?></p>
			<p><?echo $comment['text'];?></p>
		</div>
	<?}
}

function get_countries($param) {
	require 'mysql_connect.php';
	$countries_query = mysqli_query($CONNECT, "SELECT `name`, `code` FROM `countries` WHERE `name` LIKE '%$param%' LIMIT 10");
	while ($countries = mysqli_fetch_assoc($countries_query)) {?>
		<a href="/country/<?echo $countries['code'];?>"><?echo $countries['name'];?></a>
	<?}
}

?>
<?php

	if ($_POST['set_avatar']) {
		if(empty($_FILES['avatar']['name'])) message_send(1,'Ви не обрали зображення','/profile');
		if (filesize($_FILES['avatar']['tmp_name']) > 2097152) message_send(1, 'Ви маєте обрати файл розміром не більше 2Мб');
		if(preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)|(png)|(PNG)$/',$_FILES['avatar']['name'])) {

			if(preg_match('/[.](PNG)|(png)$/', $_FILES['avatar']['name'])) {
				$im = imagecreatefrompng($_FILES['avatar']['tmp_name']);
				$format = '.png';
			}
			if(preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/', $_FILES['avatar']['name'])) {
				$im = imagecreatefromjpeg($_FILES['avatar']['tmp_name']);
				$format = '.jpg';
			}

			move_uploaded_file($_FILES['avatar']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/resource/img/avatar/original/' . $_SESSION['USER_ID'] . $format);

			$w_src = imagesx($im);
			$h_src = imagesy($im);
			$ratio = $w_src / $h_src;
			if ($w_src > $h_src) {
				$w = 30; $h = $w / $ratio;
			}
			else {
				$h = 30; $w = $h * $ratio;
			}
			$icon = imagecreatetruecolor($w, $h);
			imagecopyresampled($icon, $im, 0,0 , 0,0 , $w,$h , $w_src,$h_src);
			if ($format == '.jpg') imagejpeg($icon, $_SERVER['DOCUMENT_ROOT'].'/resource/img/avatar/icons/' . $_SESSION['USER_ID'] . $format);
			elseif ($format == '.png') imagepng($icon, $_SERVER['DOCUMENT_ROOT'].'/resource/img/avatar/icons/' . $_SESSION['USER_ID'] . $format);
			$_SESSION['USER_FORMAT'] = $format;
			if (mysqli_query($CONNECT, "UPDATE `users` SET `avatar_format`='$format' WHERE `id` = '$_SESSION[USER_ID]'")) message_send(3, 'Завантаження аватару успішно виконано!');
			else message_send(1, 'Сталася помилка при завантаженні аватару');
		}
		else message_send(1, 'Ви не обрали зображення');
	}
	if ($_POST['del_avatar']) {
		unlink($_SERVER['DOCUMENT_ROOT'].'/resource/img/avatar/original/' . $_SESSION['USER_ID'] . $_SESSION['USER_FORMAT']);
		unlink($_SERVER['DOCUMENT_ROOT'].'/resource/img/avatar/icons/' . $_SESSION['USER_ID'] . $_SESSION['USER_FORMAT']);
		$_SESSION['USER_FORMAT'] = null;
		mysqli_query($CONNECT, "UPDATE `users` SET `avatar_format`='' WHERE `id` = '$_SESSION[USER_ID]'");
	}

	if ($_POST['save']) {
		$_POST['pib'] = form_chars($_POST['pib']);
		$_POST['phone'] = str_replace(' ', '', (form_chars($_POST['phone'])));
		$_SESSION['USER_PIB'] = $_POST['pib'];
		$_SESSION['USER_PHONE'] = $_POST['phone'];
		if (mysqli_query($CONNECT, "UPDATE `users` SET `pib` = '$_POST[pib]', `phone` = '$_POST[phone]' WHERE `id` = '$_SESSION[USER_ID]'")) message_send(3, 'Дані успішно збережені');
		else message_send(1, 'Сталася помилка при збережені даних');

		if ($_POST['password'] || $_POST['password_new'] || $_POST['password_new_r']) {
			if ($_POST['password'] && $_POST['password_new'] && $_POST['password_new_r']) {
				$_POST['password'] = gen_pass(form_chars($_POST['password']), $_SESSION['USER_LOGIN']);
				$password =  mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `password` FROM `users` WHERE `id` = '$_SESSION[USER_ID]'"));
				if ($_POST['password'] === $password['password']) {
					if($_POST['password_new'] === $_POST['password_new_r']) {
						$_POST['password_new'] = gen_pass(form_chars($_POST['password_new']), $_SESSION['USER_LOGIN']);
						if (mysqli_query($CONNECT, "UPDATE `users` SET `password` = '$_POST[password_new]'")) message_send(3, 'Пароль успішно змінено');
						else message_send(1, 'Сталася помилка при зміні паролю');
					} else message_send(1, 'Нові паролі не збігаються');
				} else message_send(1, 'Невірний поточний пароль!');

			} elseif (!$_POST['password']) message_send(1, 'Для зміни паролю введіть поточний пароль');
			elseif (!$_POST['password_new']) message_send(1, 'Для зміни паролю введіть новий пароль');
			elseif (!$_POST['password_new_r']) message_send(1, 'Для зміни паролю повторно введіть новий пароль');
		}
	}

	$city_query = mysqli_query($CONNECT, "SELECT `id`, `name` FROM `start_city`;");
	$currency_query = mysqli_query($CONNECT, "SELECT `id`, `name`, `currency_code` FROM `currency`;");
	head('Профіль');
?>
	<div class="box clearfix">
		<div class="avatar-box">
			<img class="avatar" src="/resource/img/avatar/original/<? echo (($_SESSION['USER_FORMAT'])?$_SESSION['USER_ID'].$_SESSION['USER_FORMAT']:'0.jpg'); ?>">
			<form action="/profile" method="post" enctype="multipart/form-data">
				<input type="file" name="avatar">
				<input type="submit" name="set_avatar" value="Завантажити фото">
				<input type="submit" name="del_avatar" value="Видалити фото">
			</form>
		</div>
		<div class="profile-selector-box">
			<div class="legend"><strong>Логін:</strong> <? echo $_SESSION['USER_LOGIN']; ?></div>
			<div class="legend"><strong>E-mail:</strong> <? echo $_SESSION['USER_EMAIL'] ?></div>
			<div class="legend"><strong>Оберіть місто від'їзду:</strong></div>
			<select name="change_start_city">
				<option value="0">——————————</option>
				<?while ($city = mysqli_fetch_assoc($city_query)) {;?>
					<option value="<? echo $city['id']; ?>" <? if ($city['id'] == $_SESSION['USER_CITY']) echo 'selected'?>><? echo $city['name'] ?></option>
				<?}?>
			</select>
				<div class="legend"><strong>Оберіть валюту, в якій будуть<br> відображатися всі ціни:</strong></div>
			<select name="change_user_currency">
				<option value="0">——————————</option>
				<?while ($currency = mysqli_fetch_assoc($currency_query)) {;?>
					<option value="<? echo $currency['id']; ?>" <? if ($currency['id'] == $_SESSION['USER_CURRENCY']) echo 'selected'?>><? echo $currency['name'] ?></option>
				<?}?>
			</select>
		</div>
			<form action="/profile" class="with-border" method="post">
				<div class="legend">ПІБ:</div>
				<input type="text" name="pib" value="<?echo $_SESSION['USER_PIB']?>" required>
				<div class="legend">Телефон:</div>
				<input type="text" name="phone" value="<?echo tel($_SESSION['USER_PHONE'])?>" required>
				<div class="legend">Змінити пароль:</div>
				<input type="password" name="password" placeholder="Поточний пароль">
				<input type="password" name="password_new" placeholder="Новий пароль">
				<input type="password" name="password_new_r" placeholder="Підтвердіть новий пароль">
				<input type="submit" name="save" class="profile-button" value="Зберегти">
			</form>
	</div>
<?php foot(); ?>
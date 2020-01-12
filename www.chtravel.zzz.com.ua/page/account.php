<?php
	if ($Module == 'register' && $_POST['enter']) {
		if (!$_POST['login'] || !$_POST['email'] || !$_POST['password'] || !$_POST['password_r'] || !$_POST['pib'] || !$_POST['phone'] || !$_POST['captcha']) message_send(1, 'Не всі поля заповнені!');
		$_POST['login'] = form_chars($_POST['login']);
		$_POST['email'] = form_chars($_POST['email']);
		$_POST['pib'] = form_chars($_POST['pib']);
		$_POST['phone'] = str_replace(' ', '', (form_chars($_POST['phone'])));
		if ($_POST['password'] != $_POST['password_r']) message_send(1, 'Паролі не збігаються!');
		$_POST['password'] = gen_pass(form_chars($_POST['password']), $_POST['login']);

		$row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `login` = '$_POST[login]';"));
		if ($row['login']) message_send(1, 'Логін <b>' . $_POST['login'] . '</b> вже використовується');

		$row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `email` FROM `users` WHERE `email` = '$_POST[email]';"));
		if ($row['email']) message_send(1, 'E-mail <b>' . $_POST['email'] . '</b> вже використовується');

		if ($_SESSION['captcha'] != gen_pass($_POST['captcha'])) message_send(1, 'Неправильно введені цифри!');

		mysqli_query($CONNECT, "INSERT INTO `users` (`login`, `pib`, `email`, `password`, `phone`) VALUES ('$_POST[login]', '$_POST[pib]', '$_POST[email]', '$_POST[password]', '$_POST[phone]');");

		message_send(3, 'Реєстрація аккаунта успішно завершена.', $_SERVER['HTTP_REFERER']);
	}

	if ($Module == 'login' && $_POST['enter']) {

		if (!$_POST['login'] || !$_POST['password'] || !$_POST['captcha']) message_send(1, 'Не всі поля заповнені!');

		$_POST['login'] = form_chars($_POST['login']);
		$_POST['password'] = gen_pass(form_chars($_POST['password']), $_POST['login']);
		if ($_SESSION['captcha'] != gen_pass($_POST['captcha'])) message_send(1, 'Неправильно введені цифри!');
		$row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `password` FROM `users` WHERE `login` = '$_POST[login]';"));
		if (!$row['password']) message_send(1, 'Неправильний логін!');
		if ($row['password'] !== $_POST['password']) message_send(1, 'Неправильний пароль!');

		$row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `login`, `pib`, `email`, `password`, `phone`, `id_start_city`, `id_currency`, `admin`, `avatar_format` FROM `users` WHERE `password` = '$row[password]';"));
		$_SESSION['USER_ID'] = $row['id'];
		$_SESSION['USER_LOGIN'] = $row['login'];
		$_SESSION['USER_EMAIL'] = $row['email'];
		$_SESSION['USER_PIB'] = $row['pib'];
		$_SESSION['USER_PHONE'] = $row['phone'];
		$_SESSION['USER_CITY'] = $row['id_start_city'];
		$_SESSION['USER_CURRENCY'] = $row ['id_currency'];
		$_SESSION['USER_FORMAT'] = $row ['avatar_format'];

		if ($row['admin'] != 1) {
			$_SESSION['USER_ACTIVE'] = 1;
			if ($_REQUEST['rememberme']) {setcookie('user', $_POST['password'], strtotime('+30 days'), '/');}
			exit(header('Location: /profile'));
		}
		else {
			$_SESSION['USER_ACTIVE'] = 2;
			if ($_REQUEST['rememberme']) {setcookie('admin', $_POST['password'], strtotime('+30 days'), '/');}
			exit(header('Location: /admin'));
		}
	}

	if ($Module == 'logout' && (($_SESSION['USER_ACTIVE'] == 1) or ($_SESSION['USER_ACTIVE'] == 2))) {
		if ($_COOKIE['user']) {setcookie('user', '', strtotime('-30 days'), '/'); unset($_COOKIE['user']);}
		if ($_COOKIE['admin']) {setcookie('admin', '', strtotime('-30 days'), '/'); unset($_COOKIE['admin']);}
		session_unset();
		exit(header('Location: /'));
	}
?>
<?
	switch($_POST['func']) {
		case "change_start_city": {
			mysqli_query($CONNECT, "UPDATE `users` SET `id_start_city`= '$_POST[param]' WHERE `id` = '$_SESSION[USER_ID]';");
			$_SESSION['USER_CITY'] = $_POST['param'];
			break;
		}
		case "change_user_currency": {
			mysqli_query($CONNECT, "UPDATE `users` SET `id_currency`= '$_POST[param]' WHERE `id` = '$_SESSION[USER_ID]';");
			$_SESSION['USER_CURRENCY'] = $_POST['param'];
			echo 'hello;';
			break;
		}
		case "close_message": {
			$_SESSION['message'] = array();
			break;
		}
		// case "delete_news": {
		// 	mysqli_query($CONNECT, "DELETE FROM `news` WHERE `id` = '$_POST[param]';");
		// 	break;
		// }
		case "check_login": {
			$row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `login` = '$_POST[param]';"));
			if ($row) echo '1';
			break;
		}
		case "add_tours": {
			$tours_result = mysqli_query($CONNECT, $_SESSION['tour_query'] ." LIMIT ".$_POST['param'].", 10");
			while ($tours = mysqli_fetch_assoc($tours_result)) {?>
			<div class="wrapper-preview-tour-box">
				<div class="preview-tour-box clearfix">
					<a href="/tour/<? echo $tours['id']; ?>" class="block">
						<div class="image-box">
							<img src="/resource/img/galery/preview/<?echo (($tours['photo'])?substr($tours['photo'],0,strpos($tours['photo'],'|')):'0');?>.jpg" alt="Фото">
							<div class="price-duration-box">
								<div class="price"><? $price = mysqli_fetch_array(mysqli_query($CONNECT, "SELECT MIN(`price`), `id_currency` FROM `one_tour` WHERE `id_tour` = '$tours[id]';")); echo price($price['0'], $price['id_currency'], $_SESSION['USER_CURRENCY']);?></div>
								<div class="duration"><? days($tours['duration']) ?></div>
							</div>
						</div>
					<h3><? echo $tours['name']; ?></h3>
					</a>
					<div><strong>Маршрут:</strong> <? echo $tours['itinerary']; ?></div>
					<div class="short-description"><? echo $tours['short_description']; ?></div>
				</div>
			</div>
			<?}
			break;
		}
		case 'add_currency_box': {
			?>
				<div class="currency-box">
					<h4>Оберіть валютy для відображення вартості:</h4>
					<select name="change_user_currency">
						<? $currency_query = mysqli_query($CONNECT, "SELECT `id`, `name`, `currency_code` FROM `currency`;");
						while ($currency = mysqli_fetch_assoc($currency_query)) {;?>
							<option value="<? echo $currency['id']; ?>" <? if ($currency['id'] == $_SESSION['USER_CURRENCY']) echo 'selected'?>><? echo $currency['name'].', '.$currency['currency_code']; ?></option>
						<?}?>
					</select>
				</div>
			<?
			break;
		}
		case 'get_active_countries': {
			$countries_query = mysqli_query($CONNECT, "SELECT `code` FROM `countries`;");
			$array = array();
			$i = 0;
			while ($countries = mysqli_fetch_assoc($countries_query)) {
				$array[$i++] = $countries['code'];
			}
			echo json_encode($array);
			break;
		}
		case 'search_tours': {
			$query = "SELECT DISTINCT `tour`.`id`, `name`, `itinerary`, `duration`, `short_description`, `photo` FROM `tour`";
			$start = true;
			if (($_POST['min_price']) or ($_POST['max_price']) or ($_POST['min_date']) or ($_POST['max_date'])) $query .= ' INNER JOIN `one_tour` ON `tour`.`id`=`one_tour`.`id_tour`';
			if ($_POST['type_of_tour']) {
				$query .= " WHERE (`type_of_tour` = '$_POST[type_of_tour]')";
				$start = false;
			}
			if ($_POST['country']) {
				$query .= " ".(($start)?"WHERE":"and")." (`countries` = '$_POST[country]')";
				$start = false;
			}
			if ($_POST['min_date'] and $_POST['max_date']) {
				$_POST['min_date'] = date('Y-m-d', strtotime($_POST['min_date']));
				$_POST['max_date'] = date('Y-m-d', strtotime($_POST['max_date']));
				$query .= " ".(($start)?"WHERE":"and")." (departure_date BETWEEN '$_POST[min_date]' AND '$_POST[max_date]')";
				$start = false;
			}
			else if ($_POST['min_date']) {
				$_POST['min_date'] = date('Y-m-d', strtotime($_POST['min_date']));
				$query .= " ".(($start)?"WHERE":"and")." (`departure_date` >= '$_POST[min_date]')";
				$start = false;
			}
			else if ($_POST['max_date']) {
				$_POST['max_date'] = date('Y-m-d', strtotime($_POST['max_date']));
				$query .= " ".(($start)?"WHERE":"and")." (departure_date <= '$_POST[max_date]')";
				$start = false;
			}

			if ($_POST['min_price'] and $_POST['max_price']) {
				$query .= " and (h_price BETWEEN '".price($_POST['min_price'],$_SESSION['USER_CURRENCY'],1)."' AND '".price($_POST['max_price'],$_SESSION['USER_CURRENCY'],1)."')";
				$start = false;
			}
			else if ($_POST['min_price']) {
				$query .= " ".(($start)?"WHERE":"and")." (h_price >= '".price($_POST['min_price'],$_SESSION['USER_CURRENCY'],1)."')";
				$start = false;
			}
			else if ($_POST['max_price']) {
				$query .= " ".(($start)?"WHERE":"and")." (h_price <= '".price($_POST['max_price'],$_SESSION['USER_CURRENCY'],1)."')";
				$start = false;
			}

			$_SESSION['tour_query'] = $query;
			$tours_query = mysqli_query($CONNECT, $_SESSION['tour_query']." LIMIT 10;");
			tours($tours_query);
			break;
		}

		case 'load_login_block': {?>
			<div class="modal-bg modal-close"></div>
			<div class="modal-box login-box">
				<form action="/account/login" method="POST" class="clearfix">
					<div class="input-box">
						<input type="text" name="login" placeholder="Логін або e-mail" required>
						<input type="password" name="password" placeholder="Пароль" required>
						</div> <div class="legend">Доведіть, що ви людина</div>
						<img class="captcha" src="/resource/captcha.php" alt="captcha">
						<div class="captcha-input-box"> <input type="text" name="captcha" placeholder="Запишіть цифри праворуч" required> </div>
						<div class="checkbox-box">
							<input type="checkbox" name="rememberme" id="rememberme" checked>
							<label for="rememberme"><div class="icon"></div>Запам’ятати мене</label>
					</div>
					<input type="submit" name="enter" value="Увійти">
					<br>
				</form>
				<div class="register"> Створити обліковий запис </div>
				<div class="icon close-icon modal-close">close</div>
			</div>
		<? break;
		}
		case 'load_register_block': {?>
			<div class="modal-box register-box">
				<form action="/account/register" method="POST">
					<div class="legend">Виберіть логін</div>
						<input type="text" name="login" placeholder="Логін" required>
						<div class="red"></div>
						<div class="legend">Введіть ваш e-mail</div>
						<input type="email" name="email" placeholder="E-mail" required>
						<div class="legend">Введіть ваші ПІБ</div>
						<input type="text" name="pib" placeholder="ПІБ" required>
						<div class="legend">Введіть ваш телефонний номер</div>
						<input type="text" name="phone" placeholder="Телефонний номер" required>
						<div class="red"></div> <div class="legend">Створіть пароль</div>
						<div class="input-box">
							<input type="password" name="password" placeholder="Пароль" maxlength="15" required>
							<input type="password" name="password_r" placeholder="Підтвердіть пароль" required>
						</div>
					<div class="legend">Доведіть, що ви людина</div>
					<img class="captcha" src="/resource/captcha.php" alt="captcha">
					<div class="captcha-input-box">
						<input type="text" name="captcha" placeholder="Запишіть цифри праворуч" required>
					</div>
					<input type="submit" name="enter" value="Створити обліковий запис">
				</form>
				<div class="icon close-icon modal-close">close</div>
			</div>
		<? break;
		}
		case 'user_order_tour': {
			$seats = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `seats` FROM `one_tour` WHERE `id` = '$_POST[param]' LIMIT 1"));
			if (!($_SESSION['USER_ACTIVE'])) {
				echo message_send_ajax(2,"Замовлення турів тільки для зареєстрованих користувачів");
			}
			else 
			if ($seats['seats'] < 1) {
				echo message_send_ajax(2,"Місць немає");
			}
			else {
			?>
				<div class="modal-bg modal-close"></div>
				<div class="modal-box order-tour-box">
					<div class="legend">Переконайтеся у правильності введених даних:</div>
					<div class="legend">ПІБ:</div>
					<input type="text" name="pib" value="<?echo $_SESSION['USER_PIB']?>" required>
					<div class="legend">Телефон:</div>
					<input type="text" name="phone" value="<?echo tel($_SESSION['USER_PHONE'])?>" required>
					<div class="legend">Кількість місць:</div>
					<select name="seats_number">
						<? for ($i = 1; $i <= $seats['seats']; $i++) {?>
						<option value="<?echo $i;?>"><?echo $i;?></option>
						<?}?>
					</select>
					<div class="button confirm modal-close" rel="<? echo $_POST['param'];?>">Підтвердити</div>
					<div class="button cancel modal-close">Скасувати</div>
					<div class="icon close-icon modal-close">close</div>
				</div>
			<?}
			break;
		}
		case "confirm_order_tour": {
			$_POST['pib'] = form_chars($_POST['pib']);
			$_POST['phone'] = str_replace(' ', '', (form_chars($_POST['phone'])));;
			$_POST['seats'] = form_chars($_POST['seats']);
			mysqli_query($CONNECT,"UPDATE `users` SET `pib` = '$_POST[pib]', `phone` = '$_POST[phone]' WHERE `id` = '$_SESSION[USER_ID]'");
			$_SESSION['USER_PIB'] = $_POST['pib'];
			$_SESSION['USER_PHONE'] = $_POST['phone'];
			$seats = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `seats` FROM `one_tour` WHERE `id` = '$_POST[param]' LIMIT 1"));
			$seats['seats'] = $seats['seats'] - $_POST['seats'];
			mysqli_query($CONNECT, "UPDATE `one_tour` SET `seats` = '$seats[seats]' WHERE `id` = '$_POST[param]' LIMIT 1");
			if (mysqli_query($CONNECT,"INSERT INTO `orders`(`state`, `date`, `id_one_tour`, `id_user`, `seats`) VALUES ('5', '".date('Y-m-d')."', '$_POST[param]', '$_SESSION[USER_ID]', '$_POST[seats]');")) echo message_send_ajax(3, 'Замовлення успішно відправлене');
			else echo message_send_ajax(1, "Сталася помилка при відправленні замовлення");
			break;
		}
		case "change_order_state": {
			mysqli_query($CONNECT, "UPDATE `orders` SET `state` = '$_POST[param]' WHERE `id` = '$_POST[id]'");
			break;
		}
		case "add_comment": {
			if (mysqli_query($CONNECT, "INSERT INTO `comments` (`id_user`, `text`, `datetime`, `location`) VALUES ('$_SESSION[USER_ID]', '$_POST[param]', NOW(), '$_POST[location]') ")) echo message_send_ajax(3, 'Коментар успішно відправлений');
			else echo message_send_ajax(1, "Сталася помилка при відправленні коментаря");
			break;
		}
		case "del_comment": {
			if (mysqli_query($CONNECT, "DELETE FROM `comments` WHERE `id` = '$_POST[param]'")) echo message_send_ajax(3, 'Коментар успішно видалений');
			else echo message_send_ajax(1, "Сталася помилка при видаленні коментаря");
			break;
		}
		case "update_comments": {
			comments($_POST['param']);
			break;
		}
		case "get_countries": {
			get_countries($_POST['param']);
			break;
		}
		case "search_field": {
			$tour_query = mysqli_query($CONNECT, "SELECT `id`, `name` FROM `tour` WHERE `name` LIKE '%$_POST[param]%' LIMIT 5");
			$country_query = mysqli_query($CONNECT, "SELECT `code`, `name` FROM `countries` WHERE `name` LIKE '%$_POST[param]%' LIMIT 5");
			$array = array();
			$i = 0;
			while ($tour = mysqli_fetch_assoc($tour_query)) {
				$array[$i]['name'] = $tour['name'];
				$array[$i]['id'] = $tour['id'];
				similar_text($tour['name'], $_POST['param'], $array[$i]['similar']);
				$i++;
			}
			while ($country = mysqli_fetch_assoc($country_query)) {
				$array[$i]['name'] = $country['name'];
				$array[$i]['code'] = $country['code'];
				similar_text($country['name'], $_POST['param'], $array[$i]['similar']);
				$i++;
			}
			
			for ($j = 1; $j < $i; $j++) {
				$temp = $array[$j];
				$m = $j - 1;
				for ( ;$m >= 0 and $array[$m]['similar'] < $temp['similar']; $m--) {
					$array[$m + 1] = $array[$m];
				}
				$array[$m + 1] = $temp;
			}
			if ($i == 0) echo "Пошук не дав результатів";
			else {
				if ($i > 5) $i = 5;
				$j = 0;
				while(($j < $i)) {?>
				<a href="<?echo (($array[$j]['id'])?'/tour/'.$array[$j]['id']:'/country/'.$array[$j]['code']);?>"><?echo $array[$j]['name'];?></a>
				<?
				$j++;
				}
			}
			break;
		}
	}
?>
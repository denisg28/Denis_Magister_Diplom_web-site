<?php
	active_account(0);
	head('Вхід', array('login'), 1);
?>
	<form action="/account/login" method="POST" class="clearfix">
		<input type="text" name="login" placeholder="Логін або e-mail" required>
		<input type="password" name="password" placeholder="Пароль" required>
		<div class="legend">Доведіть, що ви людина</div>
		<input type="text" name="captcha" placeholder="Запишіть цифри праворуч" pattern="[0-9]{1,5}" title="Тільки цифри" required>
		<img src="/resource/captcha.php" alt="captcha">
		<div class="checkbox">
			<input type="checkbox" name="rememberme" id="rememberme">
			<label for="rememberme"><div class="icon"></div>Запам’ятати мене</label>
		</div>
		<input type="submit" name="enter" value="Увійти"><br>
	</form>
	<p> <a href="/register">Створити обліковий запис</a> </p>
<?php foot(array('jquery-2.1.3.min')); ?>
<?php
	active_account(0);
	head('Реєстрація', 'register', 1);
?>
<form action="/account/register" method="POST">
	<div class="legend">Виберіть логін</div>
	<input type="text" name="login" placeholder="Логін" maxlength="10" pattern="[A-Za-z-0-9]{3,10}" title="Не менше 3 i не більше 10 латинських символів або цифр" required>
	<div class="legend">Введіть ваш логін</div>
	<input type="email" name="email" placeholder="E-mail" required>
	<div class="legend">Створіть пароль</div>
	<input type="password" name="password" placeholder="Пароль" maxlength="15" pattern="[A-Za-z-0-9]{6,15}" title="Не менше 6 i не більше 15 латинських символів або цифр" required>
	<input type="password" name="password_r" placeholder="Підтвердіть пароль" required>
	<div class="legend">Доведіть, що ви людина</div>
	<input type="text" name="captcha" placeholder="Запишіть цифри праворуч" pattern="[0-9]{1,5}" title="Тільки цифри" required>
	<img src="/resource/captcha.php" alt="captcha">
	<input type="submit" name="enter" value="Створити обліковий запис">
</form>
<?php foot(); ?>
<?php
	$tour = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `name`, `type_of_tour`, `countries`, `itinerary`, `duration`, `description`, `photo` FROM `tour` WHERE `id` = '$Module' LIMIT 1;"));
	$dates_query = mysqli_query($CONNECT, "SELECT `id`, `departure_date`, `price`, `id_currency`, `seats` FROM `one_tour` WHERE `id_tour` = '$Module';");
	$price = mysqli_fetch_array(mysqli_query($CONNECT, "SELECT MIN(`price`), `id_currency` FROM `one_tour` WHERE `id_tour` = '$Module';"));
?>

<?php head('Тури');?>
<div class="box">
	<h1><? echo $tour['name']; ?></h1>
	<div class="table">
		<div class="price-duration-box left-big-size-wrapper">
			<div class="price"><? echo price($price[0], $price[1], $_SESSION['USER_CURRENCY'])?></div>
			<div class="duration"><? days($tour['duration']); ?></div>
		</div>
		<div class=" itinerary"><? echo $tour['itinerary']; ?></div>
	</div>
	<?if ($tour['photo']) {?>
		<ul class="slider">
			<?
				$photo = explode('|',$tour['photo']);
				foreach ($photo as $num => $file_name) {?>
				<li<?if ($num == 0) echo ' class="active"'?>><img src="/resource/img/galery/original/<?echo $file_name;?>.jpg"></li>
				<?}
			?>
			<div class="prev icon">left</div>
			<div class="next icon">right</div>
			<div class="pause icon">pause</div>
		</ul>
<?}
if ($tour['countries']) {
?>
<div style="margin-top: 1em;"><strong>Країни:</strong>
	<?
		$array = explode('|', $tour['countries']);
		$query = "SELECT `name`, `code` FROM `countries` WHERE";
		foreach ($array as $value) {
			$query .= " `id` = '$value' or";
		}
		$query = substr($query, 0,-2).';';
		$countries_query = mysqli_query($CONNECT, $query);
		$countries_result = '';
		while ($countries = mysqli_fetch_assoc($countries_query)) {
			$countries_result .= ' <a href="/country/'.$countries['code'].'">'.$countries['name'].'</a>,';
		}
		echo substr($countries_result, 0, -1);
	?>
</div>
<?}?>
<div><strong>Тип туру:</strong>
	<?
		$array = explode('|', $tour['type_of_tour']);
		$query = "SELECT `name` FROM `type_of_tour` WHERE";
		foreach ($array as $value) {
			$query .= " `id` = '$value' or";
		}
		$query = substr($query, 0,-2).';';
		$tour_type_query = mysqli_query($CONNECT, $query);
		$tour_type_result = '';
		while ($tour_type = mysqli_fetch_assoc($tour_type_query)) {
			$tour_type_result .= $tour_type['name'].',';
		}
		echo substr($tour_type_result, 0, -1);
	?>
</div>
<div class="description"><strong>Опис туру:</strong> <? echo $tour['description']; ?></div>
<h5>Щоб забронювати тур натисніть вкладку "Дати", оберіть дату від'їзду та натисніть на неї</h5>
<div class="tabbed clearfix">
	<input type="radio" name="tabs" id="tab-nav-1" checked>
	<label for="tab-nav-1">Дати</label>
	<input type="radio" name="tabs" id="tab-nav-2">
	<label for="tab-nav-2">Відгуки і коментарі</label>
	<div class="tabs">
		<div class="dates">
		<?if (mysqli_num_rows($dates_query) > 0) {?>
			<table>
				<thead>
					<tr>
						<th>Дата</th><th>Вартість</th>
					</tr>
				</thead>
				<tbody>
					<?while ($dates = mysqli_fetch_assoc($dates_query)) {;?>
						<tr class="order-tour <? echo seats_color($dates['seats']); ?>-bg" title="<? echo seats_description($dates['seats']); ?>" rel="<? echo $dates['id'];?>"><td><? echo date_format(date_create($dates['departure_date']),'d.m.Y'); ?></td><td><? echo price($dates['price'], $dates['id_currency'], $_SESSION['USER_CURRENCY']); ?></td></tr>
					<?}?>
				</tbody>
			</table>
			<?}
			else echo 'На сьогодні немає запланованих дат';?>
		</div>
		<div>Тут будуть відгуки і коментарі</div>
	</div>
</div>
<div></div>
</div>
<?php foot(); ?>
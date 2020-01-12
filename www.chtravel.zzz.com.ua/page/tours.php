<?php
	$_SESSION['tour_query'] = "SELECT `id`, `name`, `itinerary`, `duration`, `short_description`, `photo` FROM `tour`";
	$tours_query = mysqli_query($CONNECT, $_SESSION['tour_query']. " LIMIT 10");
	head('Тури');?>
<div class="box clearfix">
	<h2>Найкращі пропозиції</h2>
	<table class="search-tour">
		<tr>
			<td>
				<h3>Тип туру</h3>
				<select class="small" name="type_of_tour">
					<option value=""></option>
					<?$type_of_tour_query = mysqli_query($CONNECT, "SELECT `id`, `name` FROM `type_of_tour`");
						while ($type_of_tour = mysqli_fetch_assoc($type_of_tour_query)) {?>
							<option value="<?echo $type_of_tour['id']?>"><?echo $type_of_tour['name'];?></option>
						<?}
					?>
				</select>
			</td>
			<td>
				<h3>Країна подорожі</h3>
				<select class="small" name="country">
					<option value=""></option>
					<?$country_query = mysqli_query($CONNECT, "SELECT `id`, `name` FROM `countries`");
						while ($country = mysqli_fetch_assoc($country_query)) {?>
							<option value="<?echo $country['id']?>"><?echo $country['name'];?></option>
						<?}
					?>
				</select>
			</td>
			<td>
				<h3>Вартість</h3>
				<div class="left-float">Від</div>
				<input type="text" name="min_price" class="small">
				<div class="left-float">До</div>
				<input type="text" name="max_price" class="small">
			</td>
			<td>
				<h3>Дата від'їзду</h3>
				<div class="left-float">Від</div>
				<input type="text" name="min_date" class="small">
				<div class="left-float">До</div>
				<input type="text" name="max_date" class="small">
			</td>
			<td>
				<div class="search-button button">Шукати</div>
			</td>
		</tr>
	</table>
	<div class="tour-list">
		<? tours($tours_query);?>
	</div>
	<div class="center">
		<div class="button add-tours">Завантажити ще</div>
	</div>
</div>
<?php foot(); ?>
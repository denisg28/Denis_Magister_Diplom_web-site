<?head('Країни');?>
<div class="box">
	<h4>Оберіть країну на карті або надрукуйте назу країни у текстовому полі:</h4>
	<input type="text" class="search-country">
	<div style="position: relative;">
		<div class="countries"></div>
	</div>
	<? include 'world.php'; ?>
</div>
<?foot();?>
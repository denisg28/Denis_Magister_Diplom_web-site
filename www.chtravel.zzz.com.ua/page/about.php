<? head('Про нас'); ?>
<div class="box clearfix">
	<h2>Про нас</h2>
	<?
		$about = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `about` FROM `other`;"));
		echo $about['about'];
	?>
</div>
<? foot(); ?>
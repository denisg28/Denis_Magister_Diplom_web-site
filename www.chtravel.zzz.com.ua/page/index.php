<?php
	$_SESSION['tour_query'] = 
	$tours_query = mysqli_query($CONNECT, "SELECT `id`, `name`, `itinerary`, `duration`, `short_description`, `photo` FROM `tour` ORDER BY `date_regestration` LIMIT 6");
	$tours_query_2 = mysqli_query($CONNECT, "SELECT `id`, `name`, `itinerary`, `duration`, `short_description`, `photo` FROM `tour` ORDER BY `popularity` LIMIT 6");
	head('Головна');
?>
	<ul class="slider">
		<?
			$slider = mysqli_fetch_array(mysqli_query($CONNECT, "SELECT `main_slider` FROM `other`;"));
			$slider = explode('|',$slider[0]);
			foreach ($slider as $num => $file_name) {?>
			<li<?if ($num == 0) echo ' class="active"'?>><img src="/resource/img/galery/original/<?echo $file_name;?>.jpg"></li>
			<?}
		?>
		<div class="prev icon">left</div>
		<div class="next icon">right</div>
		<div class="pause icon">pause</div>
	</ul>
<div class="box clearfix">
	<div class="main-left-column">
		<h2>Новини</h2>
		<div class="news-list">
		<?$result_news = mysqli_query($CONNECT, "SELECT DISTINCT `id`, `date`, `caption`, `text` FROM `news` ORDER BY `date` DESC LIMIT 3;");
			while($news = mysqli_fetch_array($result_news)) {?>
				<div class="news-box">
					<div class="caption-box clearfix">
						<div class="date-box">
							<div class="month"><? echo uk_month(date('m', strtotime($news['date']))); ?></div>
							<div class="day"><? echo date('j', strtotime($news['date'])); ?></div>
							<div class="year"><? echo date('Y', strtotime($news['date'])); ?></div>
						</div>
						<h3><a href="/news/<?echo $news[id];?>"><? echo $news['caption']; ?></a></h3>
					</div>
					<p><? echo $news['text']; ?></p>
				</div>
			<?}?>
			<div class="center">
				<a href="/news">Більше новин &gt;&gt;</a>
			</div>
			</div>
		<h2>Відгуки та коментарі</h2>
		<? if ($_SESSION['USER_ID']) {?>
		<div class="comment-form clearfix">
			<textarea class="comment-field"></textarea>
			<div class="button add-comment">Додати коментар</div>
		</div>
		<?} else {?>
		<p>Тільки зареєстровані користувачі можуть залишати коментарі</p>
		<?}?>
		<div class="comment-list">
			<?comments($_SERVER['REQUEST_URI']);?>
		</div>
	</div>
	<div class="main-list-tours">
		<h2>Нові пропозиції</h2>
		<div class="tour-list">
			<? tours($tours_query);?>
		</div>
		<h2>Найпопулярніші пропозиції</h2>
		<div class="tour-list">
			<? tours($tours_query_2);?>
		</div>
	</div>
</div>
<?php foot(); ?>
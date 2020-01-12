<?
if (!$Module) {
	head('Новини');?>
<div class="box">
	<h2>Новини</h2>
	<div class="news-list">
	<?$result_news = mysqli_query($CONNECT, "SELECT DISTINCT `id`, `date`, `caption`, `text` FROM `news` ORDER BY `date` DESC LIMIT 0 , 10;");
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
	</div>
</div>
<?}
else {
	$news = mysqli_fetch_array(mysqli_query($CONNECT, "SELECT `id`, `date`, `caption`, `text` FROM `news` WHERE `id` = $Module;"));
	head("$news[caption] | Новини");?>
<div class="box">
	<div class="news-box">
		<div class="caption-box clearfix">
			<div class="date-box">
				<div class="month"><? echo uk_month(date('m', strtotime($news['date']))); ?></div>
				<div class="day"><? echo date('j', strtotime($news['date'])); ?></div>
				<div class="year"><? echo date('Y', strtotime($news['date'])); ?></div>
			</div>
			<h3><? echo $news['caption']; ?></h3>
		</div>
		<p><? echo $news['text']; ?></p>
	</div>
</div>
<?}foot();?>
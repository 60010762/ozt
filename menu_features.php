<!--раздел особенности продаж-->
<html>
	<style>
	.fixedbutnext { opacity: .4; height: 40px; border-radius: 5px; position: fixed; bottom: 25px; right: 20px; display: block; background: #2db700; color: #fff; text-decoration: none; padding: 6px 23px; font-size: 17px;}
	.fixedbutprev { opacity: .4; height: 40px; border-radius: 5px; position: fixed; bottom: 25px; left: 20px; display: block; background: #2db700; color: #fff; text-decoration: none; padding: 6px 23px; font-size: 17px;}
	.fixedbuthome { opacity: .4; height: 40px; border-radius: 5px; position: fixed; bottom: 25px; position: fixed; left: 30%; background: #2db700; color: #fff; text-decoration: none; padding: 6px 23px; font-size: 17px;}
	</style>
	<?
	if ($_GET["features"]>1){
		$features = mysqli_real_escape_string($db,htmlspecialchars(trim($_GET["features"])));
	} else {
		if ($_POST["features"]>1){
			$features = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST["features"])));
		} else {
			$features = 1;
		}
	}
	if ($features>50) {$features=50;}
	if ($features<1) {$features=1;}
	?>
	<center>
		<div class="col-sm-4 col-sm-offset-4">
			</br>
			<?
			if ($otdel==0) {
				?>
				<h4>Выбор отдела</h4>
				<table>				
					<tr><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=1"><img class="img-thumbnail" src="<?=$path?>/72/1.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=2"><img class="img-thumbnail" src="<?=$path?>/72/2.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=3"><img class="img-thumbnail" src="<?=$path?>/72/3.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=4"><img class="img-thumbnail" src="<?=$path?>/72/4.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=5"><img class="img-thumbnail" src="<?=$path?>/72/5.png"></a></td></tr>
					<tr><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=6"><img class="img-thumbnail" src="<?=$path?>/72/6.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=7"><img class="img-thumbnail" src="<?=$path?>/72/7.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=8"><img class="img-thumbnail" src="<?=$path?>/72/8.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=9"><img class="img-thumbnail" src="<?=$path?>/72/9.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=10"><img class="img-thumbnail" src="<?=$path?>/72/10.png"></a></td></tr>
					<tr><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=11"><img class="img-thumbnail" src="<?=$path?>/72/11.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=12"><img class="img-thumbnail" src="<?=$path?>/72/12.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=13"><img class="img-thumbnail" src="<?=$path?>/72/13.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=14"><img class="img-thumbnail" src="<?=$path?>/72/14.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=15"><img class="img-thumbnail" src="<?=$path?>/72/15.png"></a></td></tr>
				</table>
				</br>

				<a class="btn btn-lg btn-success btn-block" href="index.php?select_menu=3"/>Назад</a>
				<?
			} else { //если выбран отдел, покажем доступные ОП
				$sql_ozt_features = mysqli_query($db,"SELECT sf.*, (SELECT COUNT(*) FROM ozt.ozt_sales_features WHERE mag = sf.mag AND otdel = sf.otdel) FROM ozt.ozt_sales_features AS sf WHERE sf.mag = '".$_SESSION['postofficebox']."' AND sf.otdel = '".$otdel."' AND sf.feature_numb = '".$features."'");
				$rows_ozt_features = mysqli_fetch_row($sql_ozt_features);
				$features_count = $rows_ozt_features[8];
				?>
				<h3><?=$otdel_name[$otdel]?> <?=($features)?>/<?=($features_count)?></h3>						
					
				<?
				if ($info_text<>""){
					echo '<hr><div class="alert alert-success" role="alert">'.$info_text.'</div>';
				}
				?>
				<hr/>
				<?
				echo '<input type="hidden" name="select_menu" value="'.$select_menu.'">';
				echo '<input type="hidden" name="otdel" value="'.$otdel.'">';
				echo '<input type="hidden" name="features" value="'.$features.'">';
				if ($rows_ozt_features[4]<>''){
					echo '<center><img src="adm/'.$rows_ozt_features[4].'" class="img-thumbnail" style="height: 200px"></center><br/>';
				}
					
				?>
				<div  align="left" style="padding-left: 20px">
					<label><b>Особенности:</b></label>
					</br>
					<label><?=$rows_ozt_features[5]?></label>
					<?
					if ($rows_ozt_features[6] != "") {
						?>
						<hr/>
						<label><b>Послепродажное обслуживание:</b></label>
						</br>
						<label><?=$rows_ozt_features[6]?></label>
					<?
					}
					if ($rows_ozt_features[7] != "") {
						?>
						<hr/>
						<label><b>Рекомендации ОПВС:</b></label>
						</br>
						<label><?=$rows_ozt_features[7]?></label>
					<?
					}
				?>
				</div>						

				<table>
					<tr>
						<td width="100%">
							<?
							if ($features>1){
								?>
								<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&features=<?=($features-1)?>" Class="fixedbutprev" style="font-size: 20px">&#9668; </a> 
								<?
							}
							?>
						</td>
						<td>
							<?
							if ($features<$features_count){
								?>
								<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&features=<?=($features+1)?>" Class="fixedbutnext" style="font-size: 20px">&#9658;</a>  
								<?
							}
							?>
						</td>
					</tr>
				</table>
					
				</br>
				<a class="fixedbuthome" href="index.php?select_menu=2$otdel=0"/>Другой отдел</a>				
			<?		
			}
			?>	
		</div>
	</center>
</html>

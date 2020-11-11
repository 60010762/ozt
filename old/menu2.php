<!--раздел особенности продаж-->
<html>
	<style>
	.fixedbutnext { opacity: .4; position: fixed; top: 250px; right: 20px; display: block; background: #2db700; color: #fff; text-decoration: none; padding: 6px 23px; font-size: 17px;}
	.fixedbutprev { opacity: .4; position: fixed; top: 250px; left: 20px; display: block; background: #2db700; color: #fff; text-decoration: none; padding: 6px 23px; font-size: 17px;}
	.fixedbuthome { opacity: .4; position: fixed; top: 80px; position: fixed; left: 30%; background: #2db700; color: #fff; text-decoration: none; padding: 6px 23px; font-size: 17px;}
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
	if ($features>20) {$features=20;}
	if ($features<1) {$features=1;}
	
	
	if ($otdel==0) { 
		?>
		<form action="index.php" method="get">
			<center>
			<h4>Выбор отдела</h4>
			<table>				
				<tr><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=1"><img class="img-thumbnail" src="<?=$path?>/72/1.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=2"><img class="img-thumbnail" src="<?=$path?>/72/2.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=3"><img class="img-thumbnail" src="<?=$path?>/72/3.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=4"><img class="img-thumbnail" src="<?=$path?>/72/4.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=5"><img class="img-thumbnail" src="<?=$path?>/72/5.png"></a></td></tr>
				<tr><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=6"><img class="img-thumbnail" src="<?=$path?>/72/6.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=7"><img class="img-thumbnail" src="<?=$path?>/72/7.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=8"><img class="img-thumbnail" src="<?=$path?>/72/8.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=9"><img class="img-thumbnail" src="<?=$path?>/72/9.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=10"><img class="img-thumbnail" src="<?=$path?>/72/10.png"></a></td></tr>
				<tr><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=11"><img class="img-thumbnail" src="<?=$path?>/72/11.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=12"><img class="img-thumbnail" src="<?=$path?>/72/12.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=13"><img class="img-thumbnail" src="<?=$path?>/72/13.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=14"><img class="img-thumbnail" src="<?=$path?>/72/14.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=15"><img class="img-thumbnail" src="<?=$path?>/72/15.png"></a></td></tr>
			</table>
		</form>
		</br>
		<div class="col-sm-4 col-sm-offset-4">
		
			<center><a class="btn btn-lg btn-success btn-block" href="index.php?select_menu=3"/>Назад</a></center>
		</div>
		<?
	} else { //если выбран отдел, покажем доступные ОП
		$sql_ozt_features = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_features` WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."'");
		$rows_ozt_features = mysqli_fetch_row($sql_ozt_features);
		?>
		
		
		<div class="col-sm-4 col-sm-offset-4">
			<h3><?=$otdel_name[$otdel]?> <?=($features)?>/20</h3>						
			<h4></h4>
 			<!--<nav aria-label="Page navigation example">
				<ul class="pagination">
					<?
					 for ($num_features=1;$num_features<=20;$num_features++){
						if ($num_features==$features){$style_features="active";}else{$style_features="";}
						echo '<li class="page-item '.$style_features.'"><a class="page-link" href="index.php?select_menu='.$select_menu.'&otdel='.$otdel.'&features='.$num_features.'">'.($num_features).'</a></li>';
					}
					?>
				</ul>
			</nav>-->
			
			<?
			if ($info_text<>""){
				echo '<div class="alert alert-success" role="alert">'.$info_text.'</div>';
			}
			?>
			<hr/>
			
			<form name="formalogin" action="index.php" method="post" ENCTYPE="multipart/form-data">
				<?
				echo '<input type="hidden" name="select_menu" value="'.$select_menu.'">';
				echo '<input type="hidden" name="otdel" value="'.$otdel.'">';
				echo '<input type="hidden" name="features" value="'.$features.'">';
				if ($rows_ozt_features[($features*2)]<>''){
					echo '<center><img src="adm/'.$rows_ozt_features[($features*2)].'" class="img-thumbnail" style="height: 200px"></center><br/>';
				}
				
				?>
				<label>Описание:</label>
				</br>
				<label><?=$rows_ozt_features[(1+($features*2))]?></label><br/>	
				
				

				
			</form>
			<table>
					<tr>
						<td width="100%">
							<?
							if ($features>1){
								?>
								<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&features=<?=($features-1)?>" Class="fixedbutprev">&#9668;&#9668;         </a> 
								<?
							}
							?>
						</td>
						<td>
							<?
							if ($features<20){
								?>
								<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&features=<?=($features+1)?>" Class="fixedbutnext">&#9658;&#9658;</a>  
								<?
							}
							?>
						</td>
					</tr>
				</table>
				
				
				 <!--<div class="fixedbut" onclick="location.href=adm/index.php">Бонус!</div>temp-->
				
			</br>
			<a class="fixedbuthome" href="index.php?select_menu=2$otdel=0"/>Другой отдел</a>
		</div>
	<?		
	}?>	

	</center>
</html>

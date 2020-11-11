<!--раздел создания/редактирования особенностей-->
<html>
	<?
	
//Описание features. Добавление/редактирование особенностей features
if(@$_POST['submit_edit_select2']) {
	$about_op = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['about_op']))); 
	$about_ppo = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['about_ppo'])));
	$about_opvs = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['about_opvs'])));
	$img_features = $features;
	$about_features = $features;
	if ($_FILES['filename'.$zo]['name']<>''){
		$max_file_size = 10; // Максимальный размер файла в МегаБайтах
		$path = 'upload/';
		$blacklist = array(".php", ".phtml", ".php3", ".php4", ".html", ".htm", ".js", ".css", ".tmp", ".xls", ".xlsx");
		foreach ($blacklist as $item)
		if(preg_match("/$item\$/i", $_FILES['filename']['name'])) exit;
		// СТАРТ Загрузка файла на сервер
		if($_FILES["filename"]["size"] > $max_file_size*256*256){
			$text =  'Размер файла превышает '.$max_file_size.' Мб!';
		}
		if(copy($_FILES["filename"]["tmp_name"],$path.$_SESSION['postofficebox']." - ".$_FILES["filename"]["name"])){
			$img = $path.$_SESSION['postofficebox']." - ".$_FILES["filename"]["name"];
		}
		
		$sql2 = mysqli_query($db,"SELECT * FROM ozt.ozt_sales_features WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."' AND feature_numb = '".$features."'");
		if (mysqli_num_rows($sql2)>0){
			$result = mysqli_query($db,"UPDATE ozt.ozt_sales_features SET img='".$img."', op='".$about_op."', ppo='".$about_ppo."', opvs='".$about_opvs."' WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."' AND feature_numb = '".$features."'");
		} else {
			$result = mysqli_query($db,"INSERT INTO ozt.ozt_sales_features (mag, otdel, feature_numb, img, op, ppo, opvs) VALUES ('".$_SESSION['postofficebox']."', '".$otdel."', '".$features."', '".$img."', '".$about_op."', '".$about_ppo."', '".$about_opvs."')");
		}
	} else {
		$sql2 = mysqli_query($db,"SELECT * FROM ozt.ozt_sales_features WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."' AND feature_numb = '".$features."'");
		if (mysqli_num_rows($sql2)>0){
			$result = mysqli_query($db,"UPDATE ozt.ozt_sales_features SET op='".$about_op."', ppo='".$about_ppo."', opvs='".$about_opvs."' WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."' AND feature_numb = '".$features."'");
		} else {
			$result = mysqli_query($db,"INSERT INTO ozt.ozt_sales_features (mag, otdel, feature_numb, op, ppo, opvs) VALUES ('".$_SESSION['postofficebox']."', '".$otdel."', '".$features."', '".$about_op."', '".$about_ppo."', '".$about_opvs."')");
		}
	}
	$info_text = "Успешно сохранено";
	//$info_text = "INSERT INTO ozt.ozt_sales_features (mag, otdel, feature_numb, op, ppo, opvs) VALUES ('".$_SESSION['postofficebox']."', '".$otdel."', '".$features."', '".$about_op."', '".$about_ppo."', '".$about_opvs."')";
}
//удаление картинки
if($_GET['submit_del_select2']=='del') {
	$img_features = $features;
	$about_features = $features;
	$sql2 = mysqli_query($db,"SELECT * FROM ozt.ozt_sales_features WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."' AND feature_numb = '".$features."'");
	if (mysqli_num_rows($sql2)>0){
		$result = mysqli_query($db,"UPDATE ozt.ozt_sales_features SET img='' WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."' AND feature_numb = '".$features."'");
	}
}
	
if ($otdel>0){
	//$sql_ozt_features = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_features` WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."'");
	$sql_ozt_features = mysqli_query($db,"SELECT * FROM ozt.ozt_sales_features WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."' AND feature_numb = '".$features."'");
	$rows_ozt_features = mysqli_fetch_row($sql_ozt_features);
	?>
	<!--<h3><?=$otdel_name[$otdel]?> - страница <?=($features)?></h3>-->						
	<h4></h4>
	<nav aria-label="Page navigation example">
		<ul class="pagination">
			<?
			for ($num_features=1;$num_features<=20;$num_features++){
				if ($num_features==$features){$style_features="active";}else{$style_features="";}
				echo '<li class="page-item '.$style_features.'"><a class="page-link" href="index.php?select_menu='.$select_menu.'&otdel='.$otdel.'&features='.$num_features.'">'.($num_features).'</a></li>';
			}
			?>
		</ul>
	</nav>
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
		if ($rows_ozt_features[4]<>''){
			echo '<img src="'.$rows_ozt_features[4].'" class="img-thumbnail" style="width: 30%"><br/>';
		}
		?>
		<label for="exampleFormControlFile1">Выбрать картинку для описания</label><br/>
		<?
		if ($rows_ozt_features[4]<>''){
			echo '<a href="index.php?select_menu='.$select_menu.'&submit_del_select2=del&otdel='.$otdel.'&features='.$features.'" Class="btn btn-danger btn-sm">Удалить изображение</a> или ';
		}
		?>
		<input type="file" name="filename"  /><hr>
		<label>Особенности:</label>
		<textarea class="form-control" name="about_op"><?=$rows_ozt_features[5]?></textarea><br/>
		<label>Послепродажное обслуживание:</label>
		<textarea class="form-control" name="about_ppo"><?=$rows_ozt_features[6]?></textarea><br/>
		<label>Рекомендации ОПВС:</label>
		<textarea class="form-control" name="about_opvs"><?=$rows_ozt_features[7]?></textarea><br/>
		<input type="submit" name="submit_edit_select2" value="Сохранить" class="btn btn-success"> <br/><br/>
		<?
		if ($features>1){
			?>
			<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&features=<?=($features-1)?>" Class="btn btn-info">Назад</a> 
			<?
		}
		?>
		<?
		if ($features<20){
			?>
			<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&features=<?=($features+1)?>" Class="btn btn-info">Далее</a>  
			<?
		}
		?>
	</form>
	<?
} else {
	?>
	<table>
		<tr><h5><font color="grey">Выбор отдела</font></h5></tr>
		<tr><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=1"><img class="img-thumbnail" src="img/72/1.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=2"><img class="img-thumbnail" src="img/72/2.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=3"><img class="img-thumbnail" src="img/72/3.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=4"><img class="img-thumbnail" src="img/72/4.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=5"><img class="img-thumbnail" src="img/72/5.png"></a></td></tr>
		<tr><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=6"><img class="img-thumbnail" src="img/72/6.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=7"><img class="img-thumbnail" src="img/72/7.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=8"><img class="img-thumbnail" src="img/72/8.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=9"><img class="img-thumbnail" src="img/72/9.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=10"><img class="img-thumbnail" src="img/72/10.png"></a></td></tr>
		<tr><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=11"><img class="img-thumbnail" src="img/72/11.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=12"><img class="img-thumbnail" src="img/72/12.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=13"><img class="img-thumbnail" src="img/72/13.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=14"><img class="img-thumbnail" src="img/72/14.png"></a></td><td><a href="index.php?select_menu=<?=$select_menu?>&otdel=15"><img class="img-thumbnail" src="img/72/15.png"></a></td></tr>
	</table>
	<?
}
	?>
	
</html>

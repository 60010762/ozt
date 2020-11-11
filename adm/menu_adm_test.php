<!--создание теста-->
<html>
	<?
	if(@$_POST['submit_edit_select4']) {
		$name_test = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['name_test']))); 	
		$result = mysqli_query($db,"INSERT INTO `ozt`.`ozt_question` (`name`, `otdel`, `mag`, `is_actual`) VALUES ('".$name_test."','".$otdel."','".$_SESSION['postofficebox']."', 1)");
		$sql_ozt_create_test = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_question` WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."' AND name = '".$name_test."'");
		$rows_ozt_create_test = mysqli_fetch_row($sql_ozt_create_test);
		//$cmd = 'create';
		$create_next = $rows_ozt_create_test[0];
		$_SESSION['name_test'] = $name_test;
	}
	
	//сохранение воапроса
	if(@$_POST['submit_edit_select3']) {
		$name_test = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['name_test'])));
		$question = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['question']))); 
		$answer1 = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['answer1']))); 
		$answer2 = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['answer2']))); 
		$answer3 = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['answer3']))); 
		$answer4 = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['answer4']))); 
		$answer_true = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['answer_true'])));
		$answer_comment = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['answer_comment']))); 
		
		if (is_numeric($answer_true) && $answer_true>0 && $answer_true<=4) {
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
				
				$sql2 = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_questions` WHERE id_question = '".$create_next."' AND question_numb = '".$features."'");
				if (mysqli_num_rows($sql2)>0){
					$result = mysqli_query($db,"UPDATE `ozt`.`ozt_questions` SET img='".$img."',question='".$question."',answer1='".$answer1."',answer2='".$answer2."',answer3='".$answer3."',answer4='".$answer4."',answer_true='".$answer_true."',answer_comment='".$answer_comment."'  WHERE id_question = '".$create_next."' AND question_numb = '".$features."'");
				} else {
					$result = mysqli_query($db,"INSERT INTO `ozt`.`ozt_questions` (`id_question`,`question_numb`,`img`,`question`, `answer1`, `answer2`, `answer3`, `answer4`, `answer_true`, `answer_comment`) 
					VALUES ('".$create_next."','".$features."','".$img."','".$question."','".$answer1."','".$answer2."','".$answer3."','".$answer4."','".$answer_true."','".$answer_comment."')");
				}
			} else {
				$sql2 = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_questions` WHERE id_question = '".$create_next."' AND question_numb = '".$features."'");
				if (mysqli_num_rows($sql2)>0){
					$result = mysqli_query($db,"UPDATE `ozt`.`ozt_questions` SET question='".$question."',answer1='".$answer1."',answer2='".$answer2."',answer3='".$answer3."',answer4='".$answer4."',answer_true='".$answer_true."',answer_comment='".$answer_comment."'  WHERE id_question = '".$create_next."' AND question_numb = '".$features."'");
				} else {
					$img ="";
					$result = mysqli_query($db,"INSERT INTO `ozt`.`ozt_questions` (`id_question`,`question_numb`,`img`,`question`, `answer1`, `answer2`, `answer3`, `answer4`, `answer_true`, `answer_comment`) 
					VALUES ('".$create_next."','".$features."','".$img."','".$question."','".$answer1."','".$answer2."','".$answer3."','".$answer4."','".$answer_true."','".$answer_comment."')");
				} 
			}
			//обновим название теста в таблице ozt_question и поставим акуальность 1
			mysqli_query($db,"UPDATE ozt.ozt_question SET name ='".$name_test."', is_actual = 1 WHERE id = '".$create_next."'");
			$info_text = "Успешно сохранено";
		} else {
			$info_text = "<font color='red'>Ошибка!</font> Не указан верный ответ";
		}		
	}
	
	if(isset($_GET['delconfirm'])) {
		$confirm = true;
		$id_test=$_GET['delconfirm'];
		$select_menu = 2;
	}
	
	//Удаление теста
	if(isset($_GET['del'])) {
		$id_test=$_GET['del'];
		//Получаем путь и имя к картинкам и удаляем 
		 $getfile = mysqli_query($db,"SELECT `img`  FROM `ozt_questions` WHERE `id_question`= '".$id_test."'");
			if (mysqli_num_rows($getfile)>0){ 
			 while ($row = mysqli_fetch_array($getfile)) {      
					$file = $row["img"];
					if ($file <>'img/unnamed.gif') {
						if (file_exists($file)) {
					unlink($file);
						}				
					}
				} 
			}
		
		$result = mysqli_query($db,"DELETE FROM `ozt`.`ozt_question` WHERE `mag`='".$_SESSION['postofficebox']."' AND `id`= '".$id_test."'");
		$sql_ozt_create_test = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_question` WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."'");
		$rows_ozt_create_test = mysqli_fetch_row($sql_ozt_create_test);
		$create_next = $rows_ozt_create_test[0];
		 
		 $result = mysqli_query($db,"DELETE FROM `ozt_questions` WHERE `id_question`= '".$id_test."'");
		 //header('Location: index.php?select_menu=2');
		 $select_menu = 2;
		//exit;
		
	}
	
	if($_GET['submit_del_select2']=='del') {
		//$img_features = $features + 1;
		//$about_features = $features + 1;
		$sql = mysqli_query($db,"UPDATE ozt.ozt_questions SET img = '' WHERE id_question = '".$create_next."' AND question_numb = '".$features."'");
		 mysqli_query($db,$sql);
		//if (mysqli_num_rows($sql2)>0){
			//$result = mysqli_query($db,"UPDATE `ozt`.`ozt_features` SET img$img_features='' WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."'");
		//}
	}

	if ($otdel>0){

		//echo '<h5><font color="grey">Отдел: </font>'.$otdel_name[$otdel].'</h5>';
		if ($cmd=="create"){							
				?>
				<form name="formalogin" action="index.php" method="post" ENCTYPE="multipart/form-data">
					<?
					//Запрос на получение имени теста
					$sql = "SELECT name, (SELECT COUNT(id_question) FROM ozt.ozt_questions WHERE id_question = '".$create_next."') t1 FROM ozt.ozt_question WHERE id = ".$create_next;
					$sql_ozt_questions = mysqli_query($db, $sql);
					$rows_ozt_questions = mysqli_fetch_row($sql_ozt_questions);
					$_SESSION['name_test'] = $rows_ozt_questions[0];
					$features_count=$rows_ozt_questions[1];
					echo '<b>Название теста:</b><font color="red">*</font>';
					echo '<input class="form-control" name="name_test" required value="'.$_SESSION['name_test'].'">';
					echo '<b>Вопрос №:</b>';
					?>
					<nav aria-label="Page navigation example">
						<ul class="pagination">
							<?
							for ($num_features=1;$num_features<=$features_count;$num_features++){
								if ($num_features==$features){$style_features="active";}else{$style_features="";}
								echo '<li class="page-item '.$style_features.'"><a class="page-link" href="index.php?select_menu='.$select_menu.'&otdel='.$otdel.'&features='.$num_features.'&cmd=create&create_next='.$create_next.'">'.($num_features).'</a></li>';
							}
							if ($features==$features_count+1 && $features<=20) {
								echo '<li class="page-item active"><a class="page-link" href="index.php?select_menu='.$select_menu.'&otdel='.$otdel.'&features='.$features.'&cmd=create&create_next='.$create_next.'">'.($features).'</a></li>';
							}
							?>
						</ul>
						<?
					if ($features>1){
						?>
						<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&features=<?=($features-1)?>&featuress=<?=$num_features?>&cmd=create&create_next=<?=$create_next?>" Class="btn btn-info">Предыдущий вопрос</a> 
						<?
					}
					if ($features<$features_count){
						?>
						<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&features=<?=($features+1)?>&featuress=<?=$num_features?>&cmd=create&create_next=<?=$create_next?>" Class="btn btn-info">Следующий вопрос</a>  
						<?
					}
					if ($features==$features_count && $features<20){
						?>
						<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&features=<?=($features+1)?>&featuress=<?=$num_features?>&cmd=create&create_next=<?=$create_next?>" Class="btn btn-info">Добавить новый вопрос</a>  
						<?
					}
					?>
					</nav><hr>
					
					<?
					//Запрос на получение вопросов теста
					$sql = "SELECT qh.name, q.id_question, q.question_numb, q.img, q.question, q.answer1, 
									q.answer2, q.answer3, q.answer4, q.answer_true, q.answer_comment 
							FROM ozt.ozt_questions as q INNER JOIN
							ozt.ozt_question as qh ON qh.id = q.id_question
							WHERE id_question = '".$create_next."' AND question_numb = '".$features."'";
					//echo $sql;
					$sql_ozt_questions = mysqli_query($db, $sql);
					$rows_ozt_questions = mysqli_fetch_row($sql_ozt_questions);
					
					if ($info_text<>""){
						echo '<div class="alert alert-success" role="alert">'.$info_text.'</div>';
					}
						echo '<input type="hidden" name="select_menu" value="'.$select_menu.'">';
						echo '<input type="hidden" name="otdel" value="'.$otdel.'">';
						echo '<input type="hidden" name="features" value="'.$features.'">';
						echo '<input type="hidden" name="cmd" value="create">';
						echo '<input type="hidden" name="create_next" value="'.$create_next.'">';
						
						if ($rows_ozt_questions[3]<>''){
							echo '<img src="'.$rows_ozt_questions[3].'" class="img-thumbnail" style="width: 30%"><br/>';
							$img = $rows_ozt_questions[3];
						}
						
						if ($rows_ozt_questions[3]<>''){
							echo '<a href="index.php?select_menu='.$select_menu.'&submit_del_select2=del&otdel='.$otdel.'&features='.$features.'&cmd=create&create_next='.$create_next.'" Class="btn btn-danger btn-sm">Удалить изображение</a> или ';
						} else {
							?>
							<label for="exampleFormControlFile1">&#128206; Прикрепить изображение</label><br/>
						<?
						}
						?>						
						<input type="file" name="filename"  /><hr>
						
						<b>Вопрос:</b><font color="red">*</font>
						<textarea class="form-control" name="question" required><?=$rows_ozt_questions[4]?></textarea><br/>
						
						<b>Ответ:</b><font color="red">*</font><br/>
						<?
						for ($inum=1;$inum<=4;$inum++){
							if ($inum == $rows_ozt_questions[9]) {
								$checked = 'checked';
							} else $checked = '';
							?>							
							<Label><input name="answer_true" type="radio" value="<?=$inum?>" <?=$checked?> required>&emsp;<input style="width: 300px" required name="answer<?=$inum?>" value="<?=$rows_ozt_questions[$inum+4]?>"></label></br>
							<?
						}
						?>
						<!--<Label><input name="answer_true" type="radio" value="1">&emsp;<input style="width: 300px" name="answer1" required value="<?=$rows_ozt_questions[5]?>"></label></br>
						<Label><input name="answer_true" type="radio" value="2">&emsp;<input style="width: 300px" name="answer2" required value="<?=$rows_ozt_questions[6]?>"></label></br>
						<Label><input name="answer_true" type="radio" value="3">&emsp;<input style="width: 300px" name="answer3" required value="<?=$rows_ozt_questions[7]?>"></label></br>
						<Label><input name="answer_true" type="radio" value="4">&emsp;<input style="width: 300px" name="answer4" required value="<?=$rows_ozt_questions[8]?>"></label></br>-->
						
						<b>Комментарий к верному ответу:</b>
						<input class="form-control" name="answer_comment" value="<?=$rows_ozt_questions[10]?>"><br/>
						
						<input type="submit" name="submit_edit_select3" value="Сохранить" class="btn btn-success"> <br/><br/>
						
				</form>
		

				
			</br><a class="btn btn-success" href="index.php?select_menu=2&otdel=<?=$otdel?>"/>К списку тестов</a>
			

				<?
			
		} else {
			?>
			<form name="formalogin" action="index.php" method="post" ENCTYPE="multipart/form-data">
				<?
				echo '<input type="hidden" name="select_menu" value="'.$select_menu.'">';
				echo '<input type="hidden" name="otdel" value="'.$otdel.'">';
				echo '<input type="hidden" name="cmd" value="create">';
				?>
			
				<input class="form-control" name="name_test" required placeholder="Название нового теста">
				<input type="submit" name="submit_edit_select4" value="Создать новый тест" class="btn btn-success">
			</form>
			<?
			$sql_ozt_question = mysqli_query($db,"SELECT id, name, DATE(date) FROM `ozt`.`ozt_question` WHERE mag = '".$_SESSION['postofficebox']."' AND otdel = '".$otdel."'");
			$nums_question = mysqli_num_rows($sql_ozt_question);
			if ($nums_question>0){
				
				echo '<hr/><table class="table table-bordered">';
				echo '<tr><th>#</th><th>Название теста</th><th>Создан</th><th>Редактирование</th><th>Удаление</th></tr>';
				
				while($rows_question = mysqli_fetch_row($sql_ozt_question)){
					$num_table++;
					echo '<tr><td>'.$num_table.'</td><td>'.$rows_question[1].'</td><td>'.$rows_question[2].'</td><td> <a href="index.php?select_menu='.$select_menu.'&otdel='.$otdel.'&features=0&cmd=create&create_next='.$rows_question[0].'">редактирование</td>
						<td>
						'?>
						<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&del=<?=$rows_question[0]?>" onclick="return  confirm('Вы уверены, что хотите удалить тест навсегда? Все ответы так же будут удалены.')">Удаление</a>
						<!--<a href="index.php?select_menu=<?=$select_menu?>&otdel=<?=$otdel?>&delconfirm=<?=$rows_question[0]?>">Удаление</a>-->
						<?'
						</td>
					</tr>';
				}
				echo '</table>';
				
			}
			?>
			</br><a class="btn btn-success" href="index.php?select_menu=2"/>К выбору отдела</a>
			<?
		}

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

<!--прохождение теста-->
<html>
	<?
	if ($_GET["idtest"]>0){
		$idtest = mysqli_real_escape_string($db,htmlspecialchars(trim($_GET["idtest"])));
	} else {
		if ($_POST["idtest"]<>""){
			$idtest = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST["idtest"])));
		} else {
			//$otdel = 0;
		}
	}
	if ($_GET["questionpage"]>1){
		$questionpage = mysqli_real_escape_string($db,htmlspecialchars(trim($_GET["questionpage"])));
	} else {
		if ($_POST["questionpage"]>1){
			$questionpage = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST["questionpage"])));
		} else {
			$questionpage = 1;
		}
	}
	if ($questionpage>20) {$questionpage=20;}
	if ($questionpage<1) {$questionpage=1;}	
	
	//записываем ответ пользователя по текущему вопросу
	if (@$_POST['submit_user_answer']) {
		if ($_POST["answer"]<>'') {
			$ch_for_double = mysqli_query($db,"SELECT `session_id` FROM `ozt`.`ozt_test_answers` WHERE `session_id`= '" 
			.$_SESSION['id']."' AND `id_question`= ".$idtest." AND `question_numb`= ".$questionpage);
			if (mysqli_num_rows($ch_for_double)==0){
				$sql = "INSERT INTO `ozt`.`ozt_test_answers` (`session_id`, `ldap`, `id_question`, `question_numb`, `answer`, `correct`, `dt`) VALUES ('" 
				.$_SESSION['id']."','".$_SESSION['user_id']."','".$idtest."','".$questionpage."','".$_POST["answer"]."', (SELECT answer_true FROM `ozt`.`ozt_questions` WHERE id_question = " 
				.$idtest." AND question_numb = ".$questionpage."), SYSDATE())";				
				mysqli_query($db, $sql);				
				$_SESSION['test_current_question'] = $questionpage + 1;				
			}
			
			//обновим таблицу стауса теста
			$sql = "UPDATE ozt.ozt_user_test_status SET result = ".$_SESSION['test_current_question']." WHERE ldap = '".$_SESSION['user_id']."' and id_question = ".$idtest;
			mysqli_query($db, $sql);

			//если вопрос последний, то сбрасываем флаги, которые отвечают за невозможность выхода из теста
			if ($_SESSION['lastquestion'] == 1) {
				$_SESSION['test_current_question'] = "";
				$_SESSION['test_in_progress'] = "finished";
				$_SESSION['lastquestion'] = "";
			}			

		} else {
			$mytext = 'Выберите один из вариантов';
		}		
					
	}
	?>
	<center>
		<div class="col-sm-4 col-sm-offset-4">
			<?
			if ($idtest=="") { //список доступных тестов
				
				$sql = "SELECT q.id, q.otdel, q.name 
						FROM ozt.ozt_question as q 
						WHERE q.mag = '".$_SESSION['postofficebox']."' 
						and q.is_actual = 1 
						and (SELECT COUNT(id_question) FROM ozt.ozt_questions WHERE id_question = q.id) > 0
						and (SELECT status from ozt.ozt_user_test_status 
							 WHERE ldap = '".$_SESSION['user_id']."' and id_question = q.id) is null				
						ORDER BY q.otdel, q.name";

				$sql_ozt_question = mysqli_query($db, $sql);
				$nums_question = mysqli_num_rows($sql_ozt_question);
				$_SESSION['otdel']=$otdel;
				if ($nums_question>0){
					?>
					</br>
					<label align="left" style="font-size: 20px"><font color="red">Внимание! </font>Если вы начнёте тест, то выйти из него до завершения будет невозможно.</label>
					<?
					echo '<table class="table table-bordered">';
					echo '<tr><th>Отдел</th><th>Название теста</th></tr>';
					while($rows_question = mysqli_fetch_row($sql_ozt_question)){
						$num_table++;
						echo '<tr><td>'.$rows_question[1].'</td><td>','<a href="index.php?select_menu='.$select_menu.'&idtest='.$rows_question[0].'">'.$rows_question[2].'</td></tr>';					  
					}
					echo '</table>';					
				} else {
					?>
					<h4>Для вас нет новых тестов. Приходите позже.</h4><br/>
					<?
				}
				
				?>
				<a class="btn btn-lg btn-success btn-block" href="index.php?select_menu=3"/>Назад</a>
				<?
				
			} else { //выбран тест
				
				if ($_SESSION['test_in_progress'] != "finished") { //"finished" значит, что был ответ на последний вопрос
					$_SESSION['test_in_progress'] = $idtest; //флаг, что тестирование идет, чтобы нельзя было выйти из теста
					
					//создадим запись, что юзер начал тест. после прохождения поставим статус о завершении и оценку
					$sql = mysqli_query($db,"SELECT * FROM ozt.ozt_user_test_status WHERE ldap = '".$_SESSION['user_id']."' and id_question = '".$idtest."'");
					if (mysqli_num_rows($sql)==0){
						$sql = "INSERT INTO ozt.ozt_user_test_status(mag, ldap, user_name, user_role, id_question, start_date, status, result) VALUES ('".$_SESSION['postofficebox']."','".$_SESSION['user_id']."','".$_SESSION['displayname']."','".$_SESSION['title']."','".$idtest."',now(),'не завершён', 1)";
						mysqli_query($db, $sql);
					} 
					
					if ($_SESSION['test_current_question']=="") $_SESSION['test_current_question']=1;
					$questionpage = $_SESSION['test_current_question'];		
				
					$sql = "SELECT img, question, answer1, answer2, answer3, answer4, (SELECT COUNT(id_question) FROM ozt.ozt_questions WHERE id_question = '".$idtest."') t1 FROM ozt.ozt_questions WHERE id_question = '".$idtest."' AND question_numb = '".$questionpage."'";
					$sql_ozt_questions = mysqli_query($db, $sql);
					$rows_ozt_questions = mysqli_fetch_row($sql_ozt_questions);			
					
					?>
					<br><h4>Вопрос <?=($questionpage)?> из <?=$rows_ozt_questions[6]?></h4>											
					<?
					if ($info_text<>""){
						echo '<div class="alert alert-warning" role="alert">'.$info_text.'</div>';
					}
					?>
					
					<hr/>
					
					<form name="formalogin" action="index.php" method="post" ENCTYPE="multipart/form-data">
						<?
						echo '<input type="hidden" name="select_menu" value="'.$select_menu.'">';
						echo '<input type="hidden" name="idtest" value="'.$idtest.'">';
						echo '<input type="hidden" name="questionpage" value="'.$questionpage.'">';			
						
						if ($rows_ozt_questions[0]<>''){
							echo '<img src="adm/'.$rows_ozt_questions[0].'" class="img-thumbnail" style="height: 200px"><br/><br/>';
						}				
						?>
						
						<h4><?=$rows_ozt_questions[1]?></h4>	<!--текст вопроса-->	
						<div  align="left" style="padding-left: 20px">
							<label><input name="answer" type="radio" value="1">&emsp;<?=$rows_ozt_questions[2]?></label></br>
							<label><input name="answer" type="radio" value="2">&emsp;<?=$rows_ozt_questions[3]?></label></br>
							<label><input name="answer" type="radio" value="3">&emsp;<?=$rows_ozt_questions[4]?></label></br>
							<label><input name="answer" type="radio" value="4">&emsp;<?=$rows_ozt_questions[5]?></label>
						</div>
						<?
						if ($questionpage==$rows_ozt_questions[6]) {
							$_SESSION['lastquestion'] = 1;
							$buttontext = 'Завершить';
						} else $buttontext = 'Следующий вопрос';
						
						?>
						</br>
						<input type="submit" name="submit_user_answer" value="<?=$buttontext?>" class="btn btn-success">
					</form>

					<?			
				} else { //переходим к результатам теста. покажем ошибки и комментарии
					//юзер завершил тест. обновим таблицу ozt_user_test_status, статус завершен и результат
					$sql = "UPDATE ozt.ozt_user_test_status as ts
							SET ts.end_date = now(), ts.status = 'завершён', 
							ts.result = ((SELECT COUNT(session_id) FROM ozt_test_answers WHERE ldap = ts.ldap and id_question = ts.id_question and answer = correct) / (SELECT COUNT(id_question) FROM ozt.ozt_questions WHERE id_question = ts.id_question))*100 
							WHERE ts.ldap = '".$_SESSION['user_id']."' and ts.id_question = '".$idtest."'";
					mysqli_query($db, $sql);
					
					$sql_ozt_questions = mysqli_query($db, "SELECT result FROM ozt.ozt_user_test_status WHERE ldap = '".$_SESSION['user_id']."' and id_question = '".$idtest."'");
					$rows_ozt_questions = mysqli_fetch_row($sql_ozt_questions);
					
					if ($rows_ozt_questions[0] <> 100) {
						echo '</br><h5>Правильных ответов: ' .$rows_ozt_questions[0].'%</h5><hr/>';		
					} else echo '</br><h5>Поздравляем! Результат ' .$rows_ozt_questions[0].'%</h5></br></br></br>';
					
					$sql = "SELECT q.question as Вопрос,
							CASE
								WHEN a.answer = 1 
									THEN q.answer1
								WHEN a.answer = 2 
									THEN q.answer2
								WHEN a.answer = 3 
									THEN q.answer3
								WHEN a.answer = 4
									Then q.answer4 END AS 'Ваш ответ',     
							 CASE
								WHEN q.answer_true = 1 
									THEN q.answer1
								WHEN q.answer_true = 2 
									THEN q.answer2
								WHEN q.answer_true = 3 
									THEN q.answer3
								WHEN q.answer_true = 4
									Then q.answer4 END AS 'Правильный ответ', q.answer_comment AS Пояснение
							 FROM ozt_test_answers AS a INNER JOIN ozt_questions AS q on a.id_question = q.id_question and a.question_numb = q.question_numb
							 WHERE a.ldap = '".$_SESSION['user_id']."' and a.id_question = '".$idtest."' and a.answer <> a.correct";

					$sql_ozt_question = mysqli_query($db, $sql);
					$user_answers = mysqli_num_rows($sql_ozt_question);
					?>
					<form name="formalogin" action="index.php" method="post" ENCTYPE="multipart/form-data">
						<div  align="left" style="padding-left: 20px">
							<?
							while($rows_question = mysqli_fetch_row($sql_ozt_question)){
								echo '<h5>Вопрос: <b>'.$rows_question[0].'</b></h5>';
								echo 'Ваш ответ: <s><font color="red">'.$rows_question[1].'</font></s>';
								echo '</br>';
								echo 'Правильный ответ: <b><font color="green">'.$rows_question[2].'</font></b>';
								echo '</br>';
								echo '</br>';
								if ($rows_question[3]!="") echo 'Пояснение: <b>' .$rows_question[3].'</b>';
								echo '<hr>';			
							}
							$_SESSION['test_in_progress'] = "";				
							?>
						</div>
						<a class="btn btn-lg btn-success btn-block" href="index.php?select_menu=3"/>В главное меню</a>
					</form>
				<?	
				}	
			}

			echo $mytext;
			?>
		</div>
	</center>
	<hr/>
	<form method="post" name="erase" action="index.php?">
		<input type="submit" name="submit_erase_test" value="сброс моих тестов" class="btn btn-success">
	</form>
</html>

<!--раздел статистика-->
<html>
	<form>
	<?
	$sql = "SELECT COUNT(entered), COUNT(training), COUNT(testing), (SELECT COUNT(mag) FROM ozt.ozt_user_test_status WHERE mag = '".$_SESSION['postofficebox']."' and status = 'завершён' and MONTH(end_date) = MONTH(now()))
			FROM ozt.ozt_user_activity WHERE mag = '".$_SESSION['postofficebox']."' and MONTH(entered) = MONTH(now())";
	
	//"SELECT COUNT(mag) FROM ozt.ozt_user_test_status WHERE mag = '16' and status = 'завершён' and MONTH(end_date) = MONTH(now())";
	$sql_ozt_question = mysqli_query($db,$sql);
	$nums_question = mysqli_num_rows($sql_ozt_question);
	if ($nums_question>0){
		$rows_question = mysqli_fetch_row($sql_ozt_question);
		echo 'Всего в текущем месяце';
		$arr = array (
			'посещений:'=>$rows_question[0],
			'вход в ОП:'=>$rows_question[1],
			'вход в тесты:'=>$rows_question[2],
			'тестов пройдено:'=>$rows_question[3]
		); //Массив с парами данных "подпись"=>"значение"
		require_once('sys/construct_diag.php'); //Подключить скрипт
		$plot = new SimplePlot($arr); //Создать диаграмму
		$plot->show(); //И показать её		
		
		
		$sql = "SELECT s.end_date, s.user_name, q.name, s.result 
				FROM ozt.ozt_user_test_status AS s
				INNER JOIN ozt.ozt_question AS q ON q.id = s.id_question
				WHERE s.mag = '".$_SESSION['postofficebox']."' and s.status = 'завершён' and MONTH(s.end_date) = MONTH(now())
				ORDER BY s.end_date";
				
		$sql_ozt_question = mysqli_query($db,$sql);
		$nums_question = mysqli_num_rows($sql_ozt_question);
		if ($nums_question>0){
			echo '<hr/>Пройденные тесты';
			echo '<table class="table table-bordered">';
			echo '<tr><th>Дата</th><th>ФИО</th><th>Тест</th><th>Результат</th></tr>';
				
			while($rows_question = mysqli_fetch_row($sql_ozt_question)){
				$num_table++;
				echo '<tr><td>'.$rows_question[0].'</td><td>'.$rows_question[1].'</td><td>'.$rows_question[2].'</td><td>'.$rows_question[3].'</td></tr>';
			}
			echo '</table>';
		}
	}
		 
	?>	
	</form>


	
</html>

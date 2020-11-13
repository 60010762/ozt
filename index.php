<?php //интерфейс пользователя
header ("Content-Type: text/html; charset=utf-8");
//и так понятно
session_start(); 

//скрываем ошибки
//ini_set('display_errors','Off');

//Проверяем авторизован ли пользователь
if (!isset($_SESSION['user_id']))
{
	header('Location: login.php');
	exit;
}

?>
<script> //скрипт, не дает вернуться назад при прохождении теста
	window.onload = function() {
		if (typeof history.pushState === "function") {
			history.pushState("jibberish", null, null);
			window.onpopstate = function() {
				history.pushState('newjibberish', null, null);
				// Handle the back (or forward) buttons here
				// Will NOT handle refresh, use onbeforeunload for this.
			};
		}else {
			var ignoreHashChange = true;
			window.onhashchange = function() {
				if (!ignoreHashChange) {
					ignoreHashChange = true;
					window.location.hash = Math.random();
					// Detect and redirect change here
					// Works in older FF and IE9
					// * it does mess with your hash symbol (anchor?) pound sign
					// delimiter on the end of the URL
				} else {
					ignoreHashChange = false;
				}
			};
		}
	};
</script>
<?

//Подключаем БД
require 'sys/db_config.php';
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE);

if ($_GET["select_menu"]>0){
	$select_menu = mysqli_real_escape_string($db,htmlspecialchars(trim($_GET["select_menu"])));
} else {
	if ($_POST["select_menu"]>0){
		$select_menu = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST["select_menu"])));
	} else {
		$select_menu = 0;
	}
}

if ($_GET["otdel"]>0){
	$otdel = mysqli_real_escape_string($db,htmlspecialchars(trim($_GET["otdel"])));
} else {
	if ($_POST["otdel"]>0){
		$otdel = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST["otdel"])));
	} else {
		//$otdel = 0;
	}
}

if($_SESSION['test_in_progress']!=""){	
	$select_menu = 1;
	$idtest = $_SESSION['test_in_progress'];
	$questionpage = $_SESSION['test_current_question'];
}

function f_statistics($select_menu, $db) {
	//функция отмечает в статистике, куда и когда заходил пользователь
	switch ($select_menu) {
    case 1:
        $upd_what="testing";
        break;
    case 2:
        $upd_what="training";
        break;
	}
	$sql =  "UPDATE ozt.ozt_user_activity SET ".$upd_what." = now() WHERE session_id = '".$_SESSION['id']."'";
	mysqli_query($db, $sql);
	$_SESSION['temp'] = $sql;

}
	
//временная функция для удаления результатов всех тестов
if(@$_POST['submit_erase_test']) {		
	$_SESSION['test_in_progress']="";
	$_SESSION['test_current_question']="";
	mysqli_query($db,"DELETE FROM `ozt`.`ozt_test_answers` WHERE ldap = '".$_SESSION['user_id']."'");
	mysqli_query($db,"DELETE FROM `ozt`.`ozt_user_test_status` WHERE ldap = '".$_SESSION['user_id']."'");
	$select_menu = 0;
	$_SESSION['lastquestion'] = 0;
	$questionpage = "";			
}

$path = 'adm/img';
$select[$select_menu] = "active";
//массив меню
$title_name[0]=" - ".$_SESSION['postofficebox']."";
$title_name[3]=" - ".$_SESSION['postofficebox']."";
$title_name[1]=" - тест";
$title_name[2]=" - ОП";

//Массив отделов
$otdel_name[1]="Стройматериалы";
$otdel_name[2]="Столярные изделия";
$otdel_name[3]="Электротовары";
$otdel_name[4]="Инструменты";
$otdel_name[5]="Напольные покрытия";
$otdel_name[6]="Плитка";
$otdel_name[7]="Сантехника";
$otdel_name[8]="Водоснабжение";
$otdel_name[9]="Сад";
$otdel_name[10]="Скобяные изделия";
$otdel_name[11]="Краски";
$otdel_name[12]="Декор";
$otdel_name[13]="Освещение";
$otdel_name[14]="Хранение";
$otdel_name[15]="Кухни";

?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<? require("metacss.php");?>	
	<link rel='stylesheet' href='/adm/css/style.css'/>
</head>
<body>
	<nav class="navbar navbar-dark bg-dark flex-md-nowrap p-0 shadow">    
		<div class="dropdown">
		<?
		if ($select_menu!=1){
				echo '<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Меню</a>';
				echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">';
				if ($_SESSION['title'] == "менеджер сектора по обслуживанию клиентов" || $_SESSION['title'] == "специалист технической поддержки" || $_SESSION['role'] == "adm") {
					echo '<a class="dropdown-item" href="adm/index.php">Администрирование</a><div class="dropdown-divider"></div>';
				}
				echo '<a class="dropdown-item" href="https://m.leroymerlin.ru/catalogue/" target="_blank">Каталог товаров</a>';
				echo '<a class="dropdown-item" href="index.php?select_menu=2">Особенности продаж</a>';
				echo '<a class="dropdown-item" href="index.php?select_menu=1">Тест</a>';
				echo '<a class="dropdown-item" href="index.php?select_menu=0">В начало</a>';
				echo '</div>';
		}		
		?>                
        </div>   
		<h4 class="nav-link navbar-nav px-2" style="color:#fff">ОЗТ<?=$title_name[$select_menu]?></h4>
		<ul class="navbar-nav px-3">			
			<li class="nav-item text-nowrap">			
				<a class="nav-link" href="sys/logout.php">Выход</a>
			</li>
		</ul>
	</nav>				 
	 
	 <?
	 //картинка и приветствие	
	if ($select_menu<1){
		$sql_ozt_about = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_about` WHERE mag = '".$_SESSION['postofficebox']."'");
		$rows_ozt_about = mysqli_fetch_row($sql_ozt_about);		 
		?> 
		<form name="formalogin" action="index.php" method="post" ENCTYPE="multipart/form-data">				
			 <center>				 
				<div class="col-sm-4 col-sm-offset-4">
					</br>
					<?
					echo '<input type="hidden" name="select_menu" value="'.$select_menu.'">';
					if ($rows_ozt_about[1]<>''){
						echo '<img src="adm/'.$rows_ozt_about[1].'" class="img-thumbnail"><br/>';
						echo '<label>'.$rows_ozt_about[2].'<br/><br/>';
					}			 
					?>
					<a class="btn btn-lg btn-success btn-block" href="index.php?select_menu=3"/>Далее</a>
				</div>
			</center>
		</form>	
	<?
	}
	//раздел тест. 
	if ($select_menu==1) {
		f_statistics($select_menu, $db);
		include 'menu_test.php';
	}
				
	//раздел особенности продаж
	if ($select_menu==2) {
		f_statistics($select_menu, $db);
		include 'menu_features.php';
	}
	
	//главное меню
	if ($select_menu==3) {
		?>
		<center>
			<div class="col-sm-4 col-sm-offset-4">
				</br></br>
				<a class="btn btn-lg btn-success btn-block" href="https://m.leroymerlin.ru/catalogue/" target="_blank"/>Каталог товаров</a></br>
				<a class="btn btn-lg btn-success btn-block" href="index.php?select_menu=2"/>Особенности продаж</a></br>
				<a class="btn btn-lg btn-success btn-block" href="index.php?select_menu=1"/>Тест</a></br></br>
				<a class="btn btn-lg btn-success btn-block" href="index.php?select_menu=0"/>Назад</a></br></br>							
			</div>
		</center>
	<?		
	}
	?>	
</body>
</html>
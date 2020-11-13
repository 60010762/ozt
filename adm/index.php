<?php  //интерфейс администратора
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

//Подключаем БД
require 'sys/db_config.php';
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE);

//выбор меню
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

if ($_GET["cmd"]<>''){
	$cmd = mysqli_real_escape_string($db,htmlspecialchars(trim($_GET["cmd"])));
} else {
	if ($_POST["cmd"]<>''){
		$cmd = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST["cmd"])));
	} else {
		//$otdel = 0;
	}
}

if ($_GET["create_next"]<>''){
	$create_next = mysqli_real_escape_string($db,htmlspecialchars(trim($_GET["create_next"])));
} else {
	if ($_POST["create_next"]<>''){
		$create_next = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST["create_next"])));
	} else {
		//$otdel = 0;
	}
}


$select[$select_menu] = "active";

//массив меню
$title_name[0]="Страница приветствия";
$title_name[1]="Особенности продаж";
$title_name[2]="Редактор тестов";
$title_name[3]="Статистика";
$title_name[4]="Администраторы";

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


//Описание общее
if(@$_POST['submit_edit_select1']) {
	$about = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['about']))); 
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
		
		$sql2 = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_about` WHERE mag = '".$_SESSION['postofficebox']."'");
		if (mysqli_num_rows($sql2)>0){
			$result = mysqli_query($db,"UPDATE `ozt`.`ozt_about` SET img='".$img."',about='".$about."' WHERE mag = '".$_SESSION['postofficebox']."'");
		} else {
			$result = mysqli_query($db,"INSERT INTO `ozt`.`ozt_about` (`img`, `about`, `mag`) VALUES ('".$img."','".$about."','".$_SESSION['postofficebox']."')");
		}
	} else {
		$sql2 = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_about` WHERE mag = '".$_SESSION['postofficebox']."'");
		if (mysqli_num_rows($sql2)>0){
			$result = mysqli_query($db,"UPDATE `ozt`.`ozt_about` SET about='".$about."' WHERE mag = '".$_SESSION['postofficebox']."'");
		} else {
			$result = mysqli_query($db,"INSERT INTO `ozt`.`ozt_about` (`about`, `mag`) VALUES (''".$about."','".$_SESSION['postofficebox']."')");
		}
	}
}

if($_GET['submit_del_select1']=='del') {
	$sql2 = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_about` WHERE mag = '".$_SESSION['postofficebox']."'");
	if (mysqli_num_rows($sql2)>0){
		$result = mysqli_query($db,"UPDATE `ozt`.`ozt_about` SET img='' WHERE mag = '".$_SESSION['postofficebox']."'");
	}
}


?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<? require("metacss.php");?>
</head>
<body>
	<?
	//к разделу админки имеют доступ только менеджер сектора касс и сотрудник техподдержки
	if ($_SESSION['title'] != "менеджер сектора по обслуживанию клиентов" && $_SESSION['title'] != "специалист технической поддержки" && $_SESSION['role'] != "adm") {
		echo '<h4>У вас нет доступа к этому разделу</h4><br>';
		echo '<a class="nav-link" href="sys/logout.php">Выход</a>';
	} else {
		?>	
		<form action="index.php" method="get">
			<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
				<div class="dropdown">
					<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						 Меню
					</a>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
						<a class="dropdown-item" href="../index.php">Интерфейс пользователя</a><div class="dropdown-divider"></div>
						<a class="dropdown-item" href="index.php?select_menu=0"><?=$title_name[0]?></a>
						<a class="dropdown-item" href="index.php?select_menu=1"><?=$title_name[1]?></a>
						<a class="dropdown-item" href="index.php?select_menu=2"><?=$title_name[2]?></a><div class="dropdown-divider"></div>
						<a class="dropdown-item" href="index.php?select_menu=3"><?=$title_name[3]?></a>
						<a class="dropdown-item" href="index.php?select_menu=4"><?=$title_name[4]?></a>
					</div>					
				</div>

				<h4 class="nav-link navbar-nav px-3" style="color:#fff"> Админка ОЗТ</h4>
				<ul class="navbar-nav px-3">
					<li class="nav-item text-nowrap">
						<a class="nav-link" href="sys/logout.php">Выход</a>
					</li>
				</ul>
			</nav>
		</form>
		

				<main style="position: absolute; top: 50px; left: 20px">

					<a href="index.php?select_menu=0"><?=$title_name[0]?></a>&ensp;&bull;&nbsp;
					<a href="index.php?select_menu=1"><?=$title_name[1]?></a>&ensp;&bull;&nbsp;
					<a href="index.php?select_menu=2"><?=$title_name[2]?></a>&ensp;&bull;&nbsp;
					<a href="index.php?select_menu=3"><?=$title_name[3]?></a>&ensp;&bull;&nbsp;
					<a href="index.php?select_menu=4"><?=$title_name[4]?></a>

					<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
						<?
						if ($otdel>0) {$slash = " / ";} else {$slash = "";}
						?>
						<h5><font color="grey"><?=$title_name[$select_menu].$slash?> <a href="index.php?select_menu=<?=$select_menu?>"><?=$otdel_name[$otdel]?></a></font></h5>				
					</div>
					
					<?
					//раздел редактирования главной страницы
					if ($select_menu==0) {					
						$sql_ozt_about = mysqli_query($db,"SELECT * FROM `ozt`.`ozt_about` WHERE mag = '".$_SESSION['postofficebox']."'");
						$rows_ozt_about = mysqli_fetch_row($sql_ozt_about);
						?>
						<form name="formalogin" action="index.php" method="post" ENCTYPE="multipart/form-data">
							<?
							echo '<input type="hidden" name="select_menu" value="'.$select_menu.'">';
							if ($rows_ozt_about[1]<>''){
								echo '</br><img src="'.$rows_ozt_about[1].'" class="img-thumbnail" style="width: 30%"><br/>';
							}
							?>
							<label for="exampleFormControlFile1">Выбрать картинку для описания</label><br/>
							<?
							if ($rows_ozt_about[1]<>''){
								echo '<a href="index.php?select_menu='.$select_menu.'&submit_del_select1=del" Class="btn btn-danger btn-sm">Удалить изображение</a> или ';
							}
							?>
							<input type="file" name="filename"  /><hr>
							<label>Описание:</label>
							<textarea class="form-control" name="about"><?=$rows_ozt_about[2]?></textarea><br/>
							<div class="col-sm-4 col-sm-offset-4">
								<input type="submit" name="submit_edit_select1" value="Сохранить" class="btn btn-lg btn-success btn-block">
							</div>												
						</form>
						<?
					}
					//раздел создания/редактирования особенностей
					if ($select_menu==1)include 'menu_adm_features.php';					
					
					//раздел создание и редактирование тестов
					if ($select_menu==2) include 'menu_adm_test.php';
					
					//Раздел статистики
					if ($select_menu==3) include 'menu_adm_stat.php';
					
					//Раздел назначения админов
					if ($select_menu==4) include 'menu_adm_admins.php';
						
					?>
				</main>
			</div>
		</div>
	<?
	}
	echo $_SESSION['temp'] ;
	?>
</body>
</html>
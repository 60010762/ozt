<?
session_start();

//скрываем ошибки
ini_set('display_errors', 0); 
ini_set('display_startup_errors', 0); 
error_reporting(0);

//Подключение к БД
require 'sys/db_config.php';
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE);
require_once ("sys/auth.php");
//Проверка была ли авторизация
if (isset($_SESSION['user_id']))
{
	header('Location: index.php');
	exit;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
<?php
	//стили
	require_once("metacss.php");
?>
</head>
<body class="background" onload="$('#myModal').modal('show')">
<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-4 col-sm-offset-4">							
					<form role="form" action="login.php" method="post">
						<center><img src="img/logo.svg"/><h2 class="form-signin-heading">Основы знания товара</h2></center><br/>
						<center><h4 class="form-signin-heading" style="color: #6c6;">Добро пожаловать</h4></center>
						<label>LDAP <font color="red">*</font></label>
						<input type="text" class="form-control" name="login" placeholder="600XXXXX" required autofocus><br/>
						<label>Пароль <font color="red">*</font></label>
						<input type="password" autocomplete="off" class="form-control" name="password" placeholder="Password" required><br/>
						<button class="btn btn-lg btn-success btn-block" type="submit">Войти</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?
if ($text<>""){
?>
	<div class="modal fade bs-example-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		  </div>
		  <div class="modal-body">
			<center><h5><?=$text?></h5></center>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">ОК</button>
		  </div>
		</div>
	  </div>
	</div>
	<?
	}
 ?>
</body>
</html>
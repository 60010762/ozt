<?php
//Подключаем конфигурационный файл
$username = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['login'])));

$query = mysqli_query($db, "SELECT * FROM ozt.login WHERE `ldap` = '".$username."' LIMIT 1");
	$num_rows = mysqli_num_rows($query);
	$row = mysqli_fetch_row($query);
	if ($num_rows==0){
		$result = mysqli_query($db, "INSERT INTO ozt.login (`ldap`,`attempt`) VALUES ('".$username."','1')");
	} else {		
		$now = new DateTime(); // текущее время на сервере
		$date = DateTime::createFromFormat("Y-m-d H:i:s", $row[3]); // задаем дату в любом формате
		$interval = $now->diff($date); // получаем разницу в виде объекта DateInterval
		$time_auth = $interval->i; // кол-во минут
		if ($row[2]>3){			
			if ($time_auth>=30){
				$result = mysqli_query($db, "UPDATE ozt.login SET `attempt` = '0', `time` = '".date("Y-m-d H:i:s")."' WHERE `ldap` = '".$username."'");
			}
		} else {
			$row[2] = $row[2] + 1;
			//Добавляем счетчик входа
			$result = mysqli_query($db, "UPDATE ozt.login SET `attempt` = '".$row[2]."', `time` = '".date("Y-m-d H:i:s")."' WHERE `ldap` = '".$username."'");
		}
	}

	$query = mysqli_query($db, "SELECT * FROM ozt.login WHERE `ldap` = '".$username."' LIMIT 1");
	$row = mysqli_fetch_row($query);

	if ($row[2]<4){
		if ($username==60000000){
			$password = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['password'])));
			if ($password=="AAde1221"){
				//тестовая УЗ
				$_SESSION['id'] = 'test'.date("mdy").date("His");
				$_SESSION['timeactivity'] = new DateTime("now");
				$_SESSION['user_id'] = 'test';
				$_SESSION['displayname'] = 'test'; //фио
				$_SESSION['title'] = "менеджер сектора по обслуживанию клиентов"; //должность
				$_SESSION['department'] = 'adm';
				$_SESSION['postofficebox'] = 16;
				$_SESSION['physicaldeliveryofficename'] = 'test';
				$result = mysqli_query($db, "UPDATE ozt.login SET `attempt` = '0' WHERE `ldap` = '".$_POST['login']."'");
			} else {
					$text="Неверный логин или пароль";
			} 
		} else {
			if ($username != "") {
				if (is_numeric($username) && $username>60000000 and $username<69999999) {
						include_once ("ldap.php");
				} else {
					$text="Неверно введён логин: ".$username;
				} 
			}
			
			//Если пользователь не аутентифицирован, то проверить его используя LDAP
			if (isset($_POST['login']) && isset($_POST['password']) && $text == "") {      
				$login = $username.$domain;
				$password = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['password'])));
				//подсоединяемся к LDAP серверу
				$ldap = ldap_connect($ldaphost,$ldapport);
				//Включаем LDAP протокол версии 3
				ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

				if ($ldap) {
					// Пытаемся войти в LDAP при помощи введенных логина и пароля
					ini_set('display_errors','Off');
					if(!ldap_bind($ldap,$login,$password)){$text="Неверный логин или пароль";} else {$bind = ldap_bind($ldap,$login,$password);}
					
					//ini_set('display_errors','On');
					error_reporting('E_ALL');
					if ($bind) {
						// Проверим, является ли пользователь членом указанной группы.
						$result = ldap_search($ldap,$base,"(&(".$filter.$username."))");
						// Получаем количество результатов предыдущей проверки
						$result_ent = ldap_get_entries($ldap,$result);
					} else {
						$text="Неверный логин или пароль";				
					}
				} 
			}
	
			if ($result_ent['count'] != 0) {			
				$_SESSION['id'] = $username.date("mdy").date("His"); //лдап
				$_SESSION['timeactivity'] = new DateTime("now");
				$_SESSION['user_id'] = $username; //лдап
				$_SESSION['displayname'] = $result_ent[0]["displayname"][0]; //фио
				$_SESSION['title'] = $result_ent[0]["title"][0]; //должность
				$_SESSION['department'] = $result_ent[0]["department"][0]; //отдел
				$_SESSION['postofficebox'] = $result_ent[0]["postofficebox"][0]; //номер магазина
				$_SESSION['physicaldeliveryofficename'] = $result_ent[0]["physicaldeliveryofficename"][0]; //название магазина
				$result = mysqli_query($db, "UPDATE ozt.login SET `attempt` = '0' WHERE `ldap` = '".$username."'");
					
				if ($_SESSION['title'] != "менеджер сектора по обслуживанию клиентов" && $_SESSION['title'] != "специалист технической поддержки") {
					//проверка, есть ли у юзера назначенные права админа
					$sql =  "SELECT ldap, name FROM ozt.ozt_admins WHERE mag = '".$_SESSION['postofficebox']."'";
					$sql_ozt_admins = mysqli_query($db, $sql);
					$rows_ozt_admins = mysqli_fetch_row($sql_ozt_admins);
					if ($rows_ozt_admins[0] <> "") {
						$_SESSION['role'] = "adm";
					}
				}
				
				//проверка, есть ли у пользователя незаконченный тест
				$sql =  "SELECT id_question, result FROM ozt.ozt_user_test_status WHERE ldap = '".$_SESSION['user_id']."' and status = 'не завершён'";
				$sql_ozt_test = mysqli_query($db, $sql);
				$rows_ozt_test = mysqli_fetch_row($sql_ozt_test);
				
				
				//если есть, то отмечаем в сессии текущий тест и вопрос
				if ($rows_ozt_test[0] <> "") {
					$_SESSION['test_in_progress'] = $rows_ozt_test[0];
					$_SESSION['test_current_question'] = $rows_ozt_test[1];			
				} 
				
				//запишем для статистики ldap и время входа
				$sql =  "INSERT INTO ozt.ozt_user_activity (session_id, mag, entered) VALUES ('".$_SESSION['id']."', '".$_SESSION['postofficebox']."', now())";
				$sql_ozt_stat = mysqli_query($db, $sql);		  
			}			
		}
	} else {
		$remain = 30-$time_auth;
		if (substr($remain, -1) == 1) {
			$remain = $remain." минуту";
		} elseif (substr($remain, -1) > 1 && substr($remain, -1) < 5) {
			$remain = $remain." минуты";
		} else {
			$remain = $remain." минут";
		}
		$text = "Превышено кол-во попыток входа.<br/>Попробуйте войти через ".$remain;
	}		
?>
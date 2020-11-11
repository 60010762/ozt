<?php
//Подключаем конфигурационный файл
$username = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['login'])));
if ($username>60000000 and $username<69999999) {
	include_once ("ldap.php");
}
if ($username>55000000 and $username<59999999) {
	include_once ("ldap_by.php");
}
 
//Если пользователь не аутентифицирован, то проверить его используя LDAP
if (isset($_POST['login']) && isset($_POST['password']))
      {
      
		  $login = $username.$domain;
		  $password = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['password'])));
		  //подсоединяемся к LDAP серверу
		  $ldap = ldap_connect($ldaphost,$ldapport);
		  //Включаем LDAP протокол версии 3
		  ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

      if ($ldap)
            {
            // Пытаемся войти в LDAP при помощи введенных логина и пароля
			ini_set('display_errors','Off');
			if(!ldap_bind($ldap,$login,$password)){$text="Не верный логин или пароль";} else {$bind = ldap_bind($ldap,$login,$password);}
			
			//ini_set('display_errors','On');
			error_reporting('E_ALL');
            if ($bind)
                  {
					// Проверим, является ли пользователь членом указанной группы.
					$result = ldap_search($ldap,$base,"(&(".$filter.$username."))");
					// Получаем количество результатов предыдущей проверки
					$result_ent = ldap_get_entries($ldap,$result);
            } else {
				$text="Не верный логин или пароль";
			}
      }
	
      if ($result_ent['count'] != 0)
            {
            $_SESSION['user_id'] = $username; //лдап
            $_SESSION['displayname'] = $result_ent[0]["displayname"][0]; //фио
            $_SESSION['title'] = $result_ent[0]["title"][0]; //должность
            $_SESSION['department'] = $result_ent[0]["department"][0]; //отдел
            $_SESSION['postofficebox'] = $result_ent[0]["postofficebox"][0]; //номер магазина
            $_SESSION['physicaldeliveryofficename'] = $result_ent[0]["physicaldeliveryofficename"][0]; //название магазина
      }
}
?>
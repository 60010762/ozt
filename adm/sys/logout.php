<?
	session_start();
	session_unset();
    session_destroy();
	
	setcookie("user_id","",0,"/");
	setcookie("displayname","",0,"/");
	setcookie("title","",0,"/");
	setcookie("department","",0,"/");
	setcookie("postofficebox","",0,"/");
	setcookie("physicaldeliveryofficename","",0,"/");
	
	//Сохраняем печеньку
	unset($_COOKIE['user_id']);
	unset($_COOKIE['displayname']);
	unset($_COOKIE['title']);
	unset($_COOKIE['department']);
	unset($_COOKIE['postofficebox']);
	unset($_COOKIE['physicaldeliveryofficename']);
	
	setcookie("user_id",$_SESSION['displayname'],time()-1209600);
	setcookie("displayname",$_SESSION['displayname'],time()-1209600);
	setcookie("title",$_SESSION['title'],time()-1209600);
	setcookie("department",$_SESSION['department'],time()-1209600);
	setcookie("postofficebox",$_SESSION['postofficebox'],time()-1209600);
	setcookie("physicaldeliveryofficename",$_SESSION['physicaldeliveryofficename'],time()-1209600);
	
	header('Location:  /index.php');
	exit;
?>
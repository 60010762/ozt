<?php
//ip адрес или название сервера ldap(AD)
$ldaphost = "PRUADDCX1A.hq.ru.corp.leroymerlin.com";
//$ldaphost = "kz.corp.leroymerlin.com";
//$ldaphost = "p-dc-2.LMBY.adeo.com";
//Порт подключения
$ldapport = "389";
//Полный путь к группе которой должен принадлежать человек, что бы пройти аутентификацию. 
//"cn=allow_ppl,ou=users_IT,ou=IT,ou=Kyiv,ou=corp,dc=eddnet,dc=org" - это
//мой пример.
//$memberof = "OU=NewUsers,OU=People,OU=038 - Chelyabinsk,OU=Shops,OU=Leroy Merlin Vostok,DC=hq,DC=ru,DC=corp,DC=leroymerlin,DC=com";
//$memberof = "cn=Domain Users,OU=Serviced-AD-Service Groups Builtin,OU=Leroy Merlin Vostok,DC=hq,DC=ru,DC=corp,DC=leroymerlin,DC=com";
//OU=NewUsers,OU=People,OU=038 - Chelyabinsk,OU=Shops,OU=Leroy Merlin Vostok,DC=hq,DC=ru,DC=corp,DC=leroymerlin,DC=com
//Откуда начинаем искать 
$base = "OU=Leroy Merlin Vostok,DC=hq,DC=ru,DC=corp,DC=leroymerlin,DC=com";
//$base = "OU=LMBY,DC=LMBY,DC=adeo,DC=com"; //rfpf[cnfy
//$base = "OU=Leroy Merlin Kazakhstan,DC=kz,DC=corp,DC=leroymerlin,DC=com";
//Собственно говоря фильтр по которому будем аутентифицировать пользователя
$filter = "sAMAccountName=";
//Ваш домен, обязательно с собакой впереди. Необходим этот параметр 
//для авторизации через AD, по другому к сожалению работать не будет.
//$domain = "@leroymerlin.by";
$domain = "@leroymerlin.ru";

?>
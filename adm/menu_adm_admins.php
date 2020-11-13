<!--назначение админских прав-->
<html>
	<?
	if(@$_POST['submit_add_admin']) {
		$new_adm_ldap = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['new_adm_ldap']))); 	
		$new_adm_name = mysqli_real_escape_string($db,htmlspecialchars(trim($_POST['new_adm_name']))); 
		mysqli_query($db,"INSERT INTO `ozt`.`ozt_admins` (`mag`, `ldap`, `name`) VALUES ('".$_SESSION['postofficebox']."', '".$new_adm_ldap."','".$new_adm_name."')");	 
	}
	//удалить админа по заданному лдап
	if(isset($_GET['del'])) {
		$del_adm_id=$_GET['del'];
		mysqli_query($db,"DELETE FROM `ozt`.`ozt_admins` WHERE `id` = '".$del_adm_id."'");
		$select_menu = 4;
	}	
	?>
	<form method="post" ENCTYPE="multipart/form-data">
		<label align="left" style="font-size: 20px">Права администратора уже есть у менеджеров сектора касс и 
		специалиста технической поддержки. Для добавления других сотрудников используйте форму ниже.</label>
		<div style="display: flex">
			<input class="form-control" style="width: 20%;" name="new_adm_ldap" required placeholder="600XXXXX">
			&ensp;
			<input class="form-control" style="width: 50%;" name="new_adm_name" required placeholder="Имя Фамилия ">
			&ensp;
			<input type="submit" style="width: 180px;" name="submit_add_admin" value="Выдать права" class="btn btn-success">
		</div>
	</form>
	<?
	$sql_ozt_admins = mysqli_query($db,"SELECT id, ldap, name FROM ozt.ozt_admins WHERE mag = '".$_SESSION['postofficebox']."'");
	$rows_result = mysqli_num_rows($sql_ozt_admins);
	if ($rows_result>0){
		
		echo '</br><table class="table table-bordered">';
		echo '<tr><th>#</th><th>LDAP</th><th>ФИО</th><th></th></tr>';
		
		while($result = mysqli_fetch_row($sql_ozt_admins)){
			$num_table++;
			echo '<tr><td>'.$num_table.'</td><td>'.$result[1].'</td><td>'.$result[2].'</td>
			<td>
			'?>
			<a href="index.php?select_menu=<?=$select_menu?>&del=<?=$result[0]?>" onclick="return  confirm('Вы уверены, что хотите исключить <?=$result[1]?> из списка администраторов?')">Исключить из списка</a>
			<?'
			</td>
			</tr>';
		}
		echo '</table>';								
	}
	?>						

</html>

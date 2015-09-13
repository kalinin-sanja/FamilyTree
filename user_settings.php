<?php
include_once("header.php");
include_once (CMS_ROOTPATH."menu.php");

/*
if (!$user["user_name"]){
	echo 'BEVEILIGDE BLADZIJDE/ SECURED PAGE';
	session_unset();
	session_destroy();
	die();
}
*/

@$qry = "SELECT * FROM humo_users LEFT JOIN humo_groups
	ON humo_users.user_group_id=humo_groups.group_id
	WHERE humo_users.user_id='".$_SESSION['user_id']."'";
@$result = $dbh->query($qry);
if($result->rowCount() > 0) {
	@$userDb=$result->fetch(PDO::FETCH_OBJ);
	//echo $userDb->user_name;
}
/*
else{
	echo 'BEVEILIGDE BLADZIJDE/ SECURED PAGE';
	session_unset();
	session_destroy();
	die();
}
*/

if (isset($_POST['send_mail'])){
	$error='';
	if ($_POST["register_password"]!=$_POST["register_repeat_password"]){
		$error=__('ERROR: No identical passwords');
	}

	if ($error==false){
		$user_register_date=date("Y-m-d H:i");
		//user_name='".safe_text($_POST["register_name"])."',
		//user_remark='".safe_text($_POST["register_text"])."',
		//user_register_date='".safe_text($user_register_date)."',
		//user_group_id='".$humo_option["visitor_registration_group"]."'
		$sql="UPDATE humo_users SET";
		$sql.=" user_mail='".safe_text($_POST["register_mail"])."'";
		if ($_POST["register_password"]!='')
			$sql.=", user_password='".MD5($_POST["register_password"])."'";
		$sql.=" WHERE user_id=".$userDb->user_id;
		$result = $dbh->query($sql);
		echo '<h2>'.__('Your settings are updated!').'</h2>';
	}
	else{
		echo '<h2>'.$error.'</h2>';
	}


	if ($dataDb->tree_email){
		// *** Mail new registered user to the administrator ***
		$register_address=$dataDb->tree_email;

		$register_subject="HuMo-gen. ".__('Updated profile').": ".$userDb->user_name."\n";

		// *** It's better to use plain text in the subject ***
		$register_subject=strip_tags($register_subject,ENT_QUOTES);

		$register_message =__('Message sent through HuMo-gen from the website.')."<br>\n";
		$register_message .="<br>\n";
		$register_message .=__('User updated his/ her profile')."<br>\n";
		$register_message .=__('Name').':'.$userDb->user_name."<br>\n";
		$register_message .=__('E-mail').": <a href='mailto:".$_POST['register_mail']."'>".$_POST['register_mail']."</a><br>\n";
		//$register_message .=$_POST['register_text']."<br>\n";

		//$headers  = "MIME-Version: 1.0\n";
		//$headers .= "Content-type: text/html; charset=utf-8\n";
		//$headers .= "X-Priority: 3\n";
		//$headers .= "X-MSMail-Priority: Normal\n";
		//$headers .= "X-Mailer: php\n";
		//$headers .= "From: \"".$userDb->user_name."\" <".$_POST['register_mail'].">\n";

		//@$mail = mail($register_address, $register_subject, $register_message, $headers);

		include_once ('include/mail.php');
		// *** Set who the message is to be sent from ***
		$mail->setFrom($_POST['register_mail'], $userDb->user_name);
		// *** Set who the message is to be sent to ***
		$mail->addAddress($register_address, $register_address);
		// *** Set the subject line ***
		$mail->Subject = $register_subject;
		$mail->msgHTML($register_message);
		// *** Replace the plain text body with one created manually ***
		//$mail->AltBody = 'This is a plain-text message body';
		if (!$mail->send()) {
		//	echo '<br><b>'.__('Sending e-mail failed!').' '. $mail->ErrorInfo.'</b>';
		//} else {
		//	echo '<br><b>'.__('E-mail sent!').'</b><br>';
		}

	}
}
elseif (isset($userDb->user_name)){

	echo '<script type="text/javascript">';
	echo '
	function validate(form_id,register_mail) {
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		var address = document.forms[form_id].elements[register_mail].value;
		if(reg.test(address) == false) {
			alert(\'Invalid Email Address\');
			return false;
		}
	}
	';
	echo '</script>';

	print '<br><form id="form_id" method="post" action="'.$_SERVER['PHP_SELF'].'" accept-charset = "utf-8" onsubmit="javascript:return validate(\'form_id\',\'register_mail\');">';

	print '<table align="center" class="humo" style="width:500px;">';
	print '<tr class=table_headline><th class="fonts" colspan="2">'.__('User settings').'</th></tr>';

	$register_name=$userDb->user_name; if (isset($_POST['register_name'])){ $register_name=$_POST['register_name']; }
	//print '<tr><td>'.__('Username').':</td><td><input type="text" class="fonts" name="register_name" size="40" style="background-color:#FFFFFF" value="'.$register_name.'"></td></tr>';
	print '<tr><td>'.__('Username').':</td><td>'.$register_name.'</td></tr>';

	$register_password=''; if (isset($_POST['register_password'])){ $register_password=$_POST['register_password']; }
	print '<tr><td>'.__('Password').':</td><td><input type="password" class="fonts" name="register_password" size="40" style="background-color:#FFFFFF" value="'.$register_password.'"></td></tr>';

	$register_repeat_password=''; if (isset($_POST['register_repeat_password'])){ $register_repeat_password=$_POST['register_repeat_password']; }
	print '<tr><td>'.__('Repeat password').':</td><td><input type="password" class="fonts" name="register_repeat_password" size="40" style="background-color:#FFFFFF" value="'.$register_repeat_password.'"></td></tr>';

	$register_mail=$userDb->user_mail; if (isset($_POST['register_mail'])){ $register_mail=$_POST['register_mail']; }
	print '<tr><td>'.__('FULL e-mail address: ').'</td><td><input type="text" class="fonts" id="register_mail" name="register_mail" value="'.$register_mail.'" size="40" style="background-color:#FFFFFF"> </td></tr>';

	//$register_text=''; if (isset($_POST['register_text'])){ $register_text=$_POST['register_text']; }
	//print '<tr><td>'.__('Message: ').'</td><td><textarea name="register_text" ROWS="5" COLS="40" class="fonts">'.$register_text.'</textarea></td></tr>';

	//print '<tr><td></td><td style="font-weight:bold;" class="fonts" align="left">'.__('Please enter a full and valid email address,<br>otherwise I cannot respond to your e-mail!').'</td></tr>';
	print '<tr><td></td><td><input class="fonts" type="submit" name="send_mail" value="'.__('Change').'"></td></tr>';
	print '</table>';
	print '</form>';
}


// *** Theme select ***
// *** Hide theme select if there is only one theme, AND it is the default theme ***
$show_theme_select=true;
if (count($theme_folder)==1){
	if (isset($humo_option['default_skin']) AND $humo_option['default_skin'].'.css'==$theme_folder[0]) {
		$show_theme_select=false;
	}
}

if ($bot_visit){ $show_theme_select=false; }

if ($show_theme_select==true){
	print '<br><table align="center" class="humo" style="width:500px;">';
	print '<tr class=table_headline><th class="fonts">'.__('Select a theme').'</th></tr>';
		echo '<td align="center">';
		echo '<form title="'.__('Select a colour theme (a cookie will be used to remember the theme)').'" action="">';
		echo '<select name="switchcontrol" size="1" onchange="chooseStyle(this.options[this.selectedIndex].value, 365)">';

		if (isset($humo_option['default_skin'])){
			print '<option value="'.$humo_option['default_skin'].'" selected="selected">'.__('Select a theme').':</option>';
			echo '<option value="'.$humo_option['default_skin'].'">'.__('Standard-colours').'</option>';
		}
		else{
			print '<option value="none" selected="selected">'.__('Select a theme').':</option>';
			echo '<option value="none">'.__('Standard-colours').'</option>';
		}

		sort($theme_folder);
		for ($i=0; $i<count($theme_folder); $i++){
			$theme=$theme_folder[$i];
			$theme=str_replace(".css","", $theme);
			print '<option value="'.$theme.'">'.$theme.'</option>';
		}
		echo '</select></form>';
		echo '</td>';
	echo '</table>';
}

include_once(CMS_ROOTPATH."footer.php");
?>
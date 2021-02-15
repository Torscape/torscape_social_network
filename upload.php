<?php
	session_start();

	if(!isset($_SESSION['id'])){
		header("Location: index.php");
	}

	$target_dir = "./keys/";
	$target_file = $target_dir . $_SESSION['id'] . '.pub';
	if(isset($_FILES['publicKeyUpload'])){
		if(move_uploaded_file($_FILES['publicKeyUpload']['tmp_name'], $target_file)){
			header("Location: settings.php?tc=encryption");
		}else{
			echo 'Failed';
		}
	}elseif(isset($_POST['pKey_input'])){
		echo $_POST['pKey_input'];
	}
?>
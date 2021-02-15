<?php
session_start();
include("../config/connect.php");
$sid = $_SESSION['id'];
$c_id = filter_var(htmlspecialchars($_POST['cid']),FILTER_SANITIZE_NUMBER_INT);
$p_id = filter_var(htmlspecialchars($_POST['pid']),FILTER_SANITIZE_NUMBER_INT);
$check = $conn->prepare("SELECT c_author_id, c_time FROM comments WHERE c_id =:c_id");
$check->bindParam(':c_id',$c_id,PDO::PARAM_INT);
$check->execute();
while ($chR = $check->fetch(PDO::FETCH_ASSOC)) {
	$chR_aid = $chR['c_author_id'];
	$c_time = $chR['c_time'];
}
if ($chR_aid == $sid or $_SESSION['admin'] > 0) {
	// Delete notification 'comment'
	$notifyType = "comment";
	$sendNotification_sql = "DELETE FROM notifications WHERE notifyType_id=:notifyType_id AND notifyType=:notifyType AND time=:c_time";
	$sendNotification = $conn->prepare($sendNotification_sql);
	$sendNotification->bindParam(':notifyType_id',$p_id,PDO::PARAM_INT);
	$sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
	$sendNotification->bindParam(':c_time',$c_time,PDO::PARAM_INT);
	$sendNotification->execute();

	$delete_comm_sql = "DELETE FROM comments WHERE c_id= :c_id";
	$delete_comm = $conn->prepare($delete_comm_sql);
	$delete_comm->bindParam(':c_id',$c_id,PDO::PARAM_INT);
	$delete_comm->execute();

	echo "yes";
}else{
	echo "no";
}

?>
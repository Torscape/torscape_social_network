<?php
session_start();
include("../config/connect.php");
include("../includes/fetch_users_info.php");
$uid = $_SESSION['id'];
$getSaved_sql = "SELECT * FROM saved WHERE user_saved_id= :uid ORDER BY saved_time DESC";
$getSaved=$conn->prepare($getSaved_sql);
$getSaved->bindParam(':uid',$uid,PDO::PARAM_INT);
$getSaved->execute();
$countSaved = $getSaved->rowCount();
while ($fetchSaved = $getSaved->fetch(PDO::FETCH_ASSOC)) {
	$saved_id = $fetchSaved['id'];
	$saved_post_id = $fetchSaved['post_id'];
	$saved_time = $fetchSaved['saved_time'];
	$saved_timeAgo = time_ago($saved_time);
	$getPost_id_sql = "SELECT * FROM wpost WHERE post_id= :saved_post_id";
	$getPost_id=$conn->prepare($getPost_id_sql);
	$getPost_id->bindParam(':saved_post_id',$saved_post_id,PDO::PARAM_INT);
	$getPost_id->execute();
	while ($fetchPost_id = $getPost_id->fetch(PDO::FETCH_ASSOC)) {
    $wpostpost_id = $fetchPost_id['post_id'];
	$post_author_id = $fetchPost_id['author_id'];
	$post_img = $fetchPost_id['post_img'];
	$post_content = $fetchPost_id['post_content'];
	}
	$getUserData_sql = "SELECT * FROM signup WHERE id= :post_author_id";
	$getUserData=$conn->prepare($getUserData_sql);
	$getUserData->bindParam(':post_author_id',$post_author_id,PDO::PARAM_INT);
	$getUserData->execute();
	while ($fetchUserData = $getUserData->fetch(PDO::FETCH_ASSOC)) {
    $userData_username = $fetchUserData['Username'];
    $userData_fullname = $fetchUserData['Fullname'];
	}
?>
<tr id="saved_<?php echo $saved_id; ?>">
<td style="max-width: 600px">
<div style="display: inline-flex;">
<?php
if (!empty($post_img)) {
echo "<div><img src='../imgs/".$post_img."' /></div>";
}
?>
<div style="padding: 5px">
<a href="../u/<?php echo $userData_username; ?>" style='color: gray;font-size: 14px;'><?php echo "<b>".$userData_fullname."</b>"." - @".$userData_username; ?></a><br>
<span class="fa fa-clock-o" style='color: gray;font-size: 11px;'> <b style="font-family: sans-serif;"><?php echo $saved_timeAgo; ?></b></span>
<br><br>
<?php
$betterURL = '/(?:\[(.*)\]\(((?:http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,6}(?:\:[0-9]+)?(?:\/\S*)?)\)(?:\{(.*)\})*)/';
$post_content = preg_replace($betterURL, '<a href="$2" target="_blank" title="$3">$1</a>', $post_content);

//Text Formatting RegEx
$bold = '/\*\*(.*)\*\*/';
$post_content = preg_replace($bold, '<b>$1</b>', $post_content);

$strike = '/~~(.*)~~/';
$post_content = preg_replace($strike, '<strike>$1</strike>', $post_content);

$italics = '/\*(.*)\*/';
$post_content = preg_replace($italics, '<i>$1</i>', $post_content);

$spoiler = '/!(.*)!/';
$post_content = preg_replace($spoiler, '<details><summary>Spoiler!</summary>$1</details>', $post_content);

$underline = '/__(.*)__/';
$post_content = preg_replace($underline, '<u>$1</u>', $post_content);

$header_three = '/\$\$\$(.*)\$\$\$/';
$post_content = preg_replace($header_three, '<h3>$1</h3>', $post_content);

$header_two = '/\$\$(.*)\$\$/';
$post_content = preg_replace($header_two, '<h2>$1</h2>', $post_content);

$header_one = '/\$(.*)\$/';
$post_content = preg_replace($header_one, '<h1>$1</h1>', $post_content);

$post_content = nl2br("$post_content");


if (strlen($post_content) > 150) {
	echo substr($post_content, 0,150)."...";
}else{
	echo $post_content;
}
?> <br/><a href="<?php echo './post?pid='.$saved_post_id; ?>" style="color: #46a0ec;">Continue reading</a>
</div></div>
<div id="deleteSavedMsg_<?php echo $saved_id; ?>"></div>
</td>
<td align="center"><a href="javascript:void(0);" onclick="deleteSavedMsg('<?php echo $saved_id; ?>');"><span class="fa fa-times" style="color: #c1c1c1; font-size: 18px;"></span></a></td>
</tr>
<?php
}
?>
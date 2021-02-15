<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("config/connect.php");
include("includes/fetch_users_info.php");
include ("includes/time_function.php");
if(!isset($_SESSION['Username'])){
    header("location: index");
}
?>
<html dir="<?php echo lang('html_dir'); ?>">
<head>
    <title>Notifications | <?php echo lang('site_title')?></title>
    <meta charset="UTF-8">
    <meta name="keywords" content="Notifications,social network,social media,meet,free platform">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "includes/head_imports_main.php";?>
</head>
<body onload="fetchNotifications()">
<!--=============================[ NavBar ]========================================-->
<?php include "includes/navbar_main.php"; ?>

<?php
    if (is_dir("imgs/")) {
        $dircheckPath = "";
    }elseif (is_dir("../imgs/")) {
        $dircheckPath = "../";
    }elseif (is_dir("<?php echo $dircheckPath; ?>imgs/")) {
        $dircheckPath = "<?php echo $dircheckPath; ?>";
    }
?>
<!--=============================[ Div_Container ]========================================-->
<div class="main_container" align="center">
    <div style="display: inline-flex" align="center">
        <div style="text-align: <?php echo lang('textAlign'); ?>">
            <div class="fetchNotifications">
                <div id="notificationsP_data" data-load="0"></div>
                <p style='width: 100%;border:none;display: none' id="notificationsP_loading" align='center'><img src='<?php echo $dircheckPath; ?>imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'></p>
                
                <?php
                    $notify_sql = "SELECT * FROM notifications WHERE for_id =:myId ORDER BY time DESC";

                    $notify = $conn->prepare($notify_sql);
                    $notify->bindParam(':myId',$_SESSION['id'],PDO::PARAM_INT);
                    $notify->execute();
                    $notifyCount = $notify->rowCount();
                    if ($notifyCount > 0) {
                        while ($n_row = $notify->fetch(PDO::FETCH_ASSOC)) {
	                        $notify_id = $n_row['n_id'];
	                        $notify_from_id = $n_row['from_id'];
	                        $notify_for_id = $n_row['for_id'];
	                        $notifyType_id= $n_row['notifyType_id'];
	                        $notifyType = $n_row['notifyType'];
	                        $notifyType_str = $notifyType.'Notify_str';
	                        $notify_seen = $n_row['seen'];
                            $notify_time = time_ago($n_row['time']);

                            $notify_from = $conn->prepare("SELECT Fullname,Username,Userphoto FROM signup WHERE id=:notify_from_id");
                            $notify_from->bindParam(':notify_from_id',$notify_from_id,PDO::PARAM_INT);
                            $notify_from->execute();
                            while ($from_id_row = $notify_from->fetch(PDO::FETCH_ASSOC)) {
	                            $fullname = $from_id_row['Fullname'];
                                $userphoto = $from_id_row['Userphoto'];
                                $username = $from_id_row['Username'];
                            }
                            switch ($notifyType) {
                                case 'like':
                                $postBody = $conn->prepare("SELECT post_content FROM wpost WHERE post_id=:notifyType_id");
                                $postBody->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
                                $postBody->execute();
                                while ($row = $postBody->fetch(PDO::FETCH_ASSOC)) {
                                    $postContent = $row['post_content'];
                                }
                                $betterURL = '/(?:\[(.*)\]\(((?:http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,6}(?:\:[0-9]+)?(?:\/\S*)?)\)(?:\{(.*)\})*)/';
                                $bestURL = '/[[]*?([^\[\]]*?)][(]*?((?:http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,6}(?:\:[0-9]+)?(?:\/\S*)*?)\)[{]*?([^\{\}]*?)\}/';
                                $postContent = preg_replace($bestURL, '$1',  $postContent);
                                
                                if (strlen($postContent) > 80) {
                                    $getPCon = " : ".substr($postContent, 0,80)." ...";
                                }elseif (empty($postContent)) {
                                    $getPCon = "";
                                }else{
                                    $getPCon = " : ".$postContent;
                                }
                                echo "
                                <div id='sqresultItem'>
                                <a href='".$path."posts/post?pid=".$notifyType_id."'>
                                <div style='display: inline-flex;width: 100%;'>
                                <div class='navbar_fetchBoxUser' style='border-radius:2px;'>
                                <img src='".$path."imgs/user_imgs/$userphoto' />
                                </div>
                                <p style='font-size:13px;'><b>$fullname</b> ".lang('likeNotify_str')." <span style='color: #999;'>$getPCon</span>
                                <span style='font-size: small;'></span><br>
                                <img src='".$path."imgs/main_icons/1f49f.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
                                </p>
                                </div>
                                </a>
                                </div>";
                                break;
                                
                                case 'mention':
                                    $postBody = $conn->prepare("SELECT post_content FROM wpost WHERE post_id=:notifyType_id");
                                    $postBody->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
                                    $postBody->execute();
                                    while ($row = $postBody->fetch(PDO::FETCH_ASSOC)) {
                                        $postContent = $row['post_content'];
                                    }
                                    if (strlen($postContent) > 80) {
                                        $getPCon = " : ".substr($postContent, 0,80)." ...";
                                    }elseif (empty($postContent)) {
                                        $getPCon = "";
                                    }else{
                                        $getPCon = " : ".$postContent;
                                    }
                                    echo "
                                    <div id='sqresultItem'>
                                    <a href='".$path."posts/post?pid=".$notifyType_id."'>
                                    <div style='display: inline-flex;width: 100%;'>
                                    <div class='navbar_fetchBoxUser' style='border-radius:2px;'>
                                    <img src='".$path."imgs/user_imgs/$userphoto' />
                                    </div>
                                    <p style='font-size:13px;'><b>$fullname</b> ".lang('mentionNotify_str')." <span style='color: #999;'>$getPCon</span>
                                    <span style='font-size: small;'></span><br>
                                    <img src='".$path."imgs/main_icons/1f4e2.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
                                    </p>
                                    </div>
                                    </a>
                                    </div>";
                                    break;
                                
                                case 'comment':
                                echo "
                                <div id='sqresultItem'>
                                <a href='".$path."posts/post?pid=".$notifyType_id."'>
                                <div style='display: inline-flex;width: 100%;'>
                                <div class='navbar_fetchBoxUser' style='border-radius:2px;'>
                                <img src='".$path."imgs/user_imgs/$userphoto' />
                                </div>
                                <p style='font-size:13px;'><b>$fullname</b> ".lang('commmentNotify_str').".
                                <span style='font-size: small;'></span><br>
                                <img src='".$path."imgs/main_icons/1f5e8.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
                                </p>
                                </div>
                                </a>
                                </div>";
                                break;
                                case 'share':
                                $postBody = $conn->prepare("SELECT post_content FROM wpost WHERE post_id=:notifyType_id");
                                $postBody->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
                                $postBody->execute();
                                while ($row = $postBody->fetch(PDO::FETCH_ASSOC)) {
                                    $postContent = $row['post_content'];
                                }
                                if (strlen($postContent) > 80) {
                                    $getPCon = " : ".substr($postContent, 0,80)." ...";
                                }elseif (empty($postContent)) {
                                    $getPCon = "";
                                }else{
                                    $getPCon = " : ".$postContent;
                                }
                                echo "
                                <div id='sqresultItem'>
                                <a href='".$path."posts/post?pid=".$notifyType_id."'>
                                <div style='display: inline-flex;width: 100%;'>
                                <div class='navbar_fetchBoxUser' style='border-radius:2px;'>
                                <img src='".$path."imgs/user_imgs/$userphoto' />
                                </div>
                                <p style='font-size:13px;'><b>$fullname</b> ".lang('shareNotify_str')." <span style='color: #999;'>$getPCon</span>
                                <span style='font-size: small;'></span><br>
                                <img src='".$path."imgs/main_icons/1f504.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
                                </p>
                                </div>
                                </a>
                                </div>";
                                break;
                                case 'star':
                                $getUsername = $conn->prepare("SELECT Username FROM signup WHERE id=:notifyType_id");
                                $getUsername->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
                                $getUsername->execute();
                                while ($row = $getUsername->fetch(PDO::FETCH_ASSOC)) {
                                    $pUsername = $row['Username'];
                                }
                                echo "
                                <div id='sqresultItem'>
                                <a href='".$path."u/".$pUsername."'>
                                <div style='display: inline-flex;width: 100%;'>
                                <div class='navbar_fetchBoxUser' style='border-radius:2px;'>
                                <img src='".$path."imgs/user_imgs/$userphoto' />
                                </div>
                                <p style='font-size:13px;'>".lang('starNotify_str')." <b>$fullname</b>
                                <span style='font-size: small;'></span><br>
                                <img src='".$path."imgs/main_icons/2b50.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
                                </p>
                                </div>
                                </a>
                                </div>";
                                break;
                                case 'follow':
                                $getUsername = $conn->prepare("SELECT Username FROM signup WHERE id=:notifyType_id");
                                $getUsername->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
                                $getUsername->execute();
                                while ($row = $getUsername->fetch(PDO::FETCH_ASSOC)) {
                                    $pUsername = $row['Username'];
                                }
                                echo "
                                <div id='sqresultItem'>
                                <a href='".$path."u/".$pUsername."'>
                                <div style='display: inline-flex;width: 100%;'>
                                <div class='navbar_fetchBoxUser' style='border-radius:2px;'>
                                <img src='".$path."imgs/user_imgs/$userphoto' />
                                </div>
                                <p style='font-size:13px;'><b>$fullname</b> ".lang('followNotify_str')."
                                <span style='font-size: small;'></span><br>
                                <img src='".$path."imgs/main_icons/1f465.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
                                </p>
                                </div>
                                </a>
                                </div>";
                                break;
                                }
                        }
                    }

                ?>
                
                <p id="notificationsP_noMore" style='display:none;color:#9a9a9a;font-size:14px;text-align:center;'><?php echo lang('no_notifications'); ?></p>
                <input type="hidden" id="notificationsP_load" value="0"> 
            </div>
        </div>
    </div>

</div>
<!--===============================[ End ]==========================================-->
<?php include("includes/footer.php");?>
<?php include "includes/endJScodes.php"; ?>
</body>
</html>
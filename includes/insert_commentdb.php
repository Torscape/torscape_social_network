<?php
session_start();
include("../config/connect.php");
include ("time_function.php");
include ("num_k_m_count.php");
session_start();
$s_id=$_SESSION['id'];
$c_id = rand(0,9999999)+time();
$comment_content = filter_var(htmlentities($_POST['cContent']),FILTER_SANITIZE_STRING);
$apos = '/(&#39;)+/';
$comment_content = preg_replace($apos, '\'', $comment_content);
$get_post_id = filter_var(htmlentities($_POST['pid']),FILTER_SANITIZE_NUMBER_INT);
$check_path_var = filter_var(htmlentities($_POST['cp']),FILTER_SANITIZE_STRING);
$comment_time = time();

if (trim($comment_content) == NULL){
}else{
$ictodbsql = "INSERT INTO comments
(c_id,c_author_id,c_post_id,c_content,c_time)
VALUES
(:c_id,:s_id,:get_post_id,:comment_content,:comment_time)
";
$insert_comment_toDB = $conn->prepare($ictodbsql);
$insert_comment_toDB->bindParam(':s_id',$s_id,PDO::PARAM_INT);
$insert_comment_toDB->bindParam(':c_id',$c_id,PDO::PARAM_INT);
$insert_comment_toDB->bindParam(':get_post_id',$get_post_id,PDO::PARAM_INT);
$insert_comment_toDB->bindParam(':comment_content',$comment_content,PDO::PARAM_STR);
$insert_comment_toDB->bindParam(':comment_time',$comment_time,PDO::PARAM_INT);
$insert_comment_toDB->execute();
}


    $query2_sql = "SELECT * FROM signup WHERE id=:s_id";
    $query2 = $conn->prepare($query2_sql);
    $query2->bindParam(':s_id',$s_id,PDO::PARAM_INT);
    $query2->execute();
    while ($query_fetch2 = $query2->fetch(PDO::FETCH_ASSOC)) {
        $query_fetch_id2 = $query_fetch2['id'];
        $query_fetch_username2 = $query_fetch2['Username'];
        $query_fetch_fullname2 = $query_fetch2['Fullname'];
        $query_fetch_userphoto2 = $query_fetch2['Userphoto'];
        $query_fetch_verify2 = $query_fetch2['verify'];


    }
    if ($query_fetch_verify2 == "1"){
        $verifypage_var = $verifyUser;
        }else{
        $verifypage_var = "";
    }

    $uProfileUrl = $check_path_var."u/$query_fetch_username2";
    $imgs_path = $check_path_var."imgs/";
    $em_img_path = $imgs_path."emoticons/";
    include ("emoticons.php");
    $comm_body = str_replace($em_char,$em_img,$comment_content);
    $hashtag_path = $check_path_var."hashtag/";
    $user_path = $check_path_var."u/";
    $hashtags_url = '/(\#)([x00-\xFF]+[a-zA-Z0-9x00-\xFF_\w]+)/';
    $mention_url = '/(\@)([x00-\xFF]+[a-zA-Z0-9x00-\xFF_\w]+)/';
    preg_match_all($mention_url, $comm_body, $matches);
    $matchNum = count($matches[2]);

    for($i = 0; $i < $matchNum; $i++){
        $getuser_sql = "SELECT id FROM signup WHERE Username=:mentionedUser";
        $getuser = $conn->prepare($getuser_sql);
        $getuser->bindParam(':mentionedUser',$matches[2][$i],PDO::PARAM_STR);
        $getuser->execute();
        while ($getuser_row = $getuser->fetch(PDO::FETCH_ASSOC)) {
            $nId = time()+rand(0,999999999);
            $s_id = $_SESSION['id'];
            $for_id = $getuser_row['id'];
            $notifyType = "c_mention";
            $nSeen = "0";
            $nTime = time();
            if(!($s_id == $for_id)){
                $sendNotification = $conn->prepare("INSERT INTO notifications (n_id, from_id, for_id, notifyType_id, notifyType, seen, time) VALUES (:nId, :fromId, :forId, :notifyTypeId, :notifyType, :seen, :nTime)");
                $sendNotification->bindParam(':nId',$nId,PDO::PARAM_INT);
                $sendNotification->bindParam(':fromId',$s_id,PDO::PARAM_INT);
                $sendNotification->bindParam(':forId',$for_id,PDO::PARAM_INT);
                $sendNotification->bindParam(':notifyTypeId',$get_post_id,PDO::PARAM_INT);
                $sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
                $sendNotification->bindParam(':seen',$nSeen,PDO::PARAM_INT);
                $sendNotification->bindParam(':nTime',$nTime,PDO::PARAM_INT);
                $sendNotification->execute();
            }
    }   
}
    $comm_body = preg_replace($mention_url, '<a href="'.$user_path.'$2" title="@$2">@$2</a>', $comm_body);
    $comm_body = preg_replace($hashtags_url, '<a href="'.$hashtag_path.'$2" title="#$2">#$2</a>', $comm_body);

    $comm_body = nl2br($comm_body);

        //Comment Body
        echo "
        <table style='width:100%;' id='comment_$id_4comm' class='uComment'>
        <tr><td style='width:50px;position:relative'>
        <div class='user_comment_img'>
         <img src='./$imgs_path"."user_imgs/$query_fetch_userphoto2'/>
        </div>
        </td><td><a class='userLinkComment' href='$uProfileUrl'>$query_fetch_fullname2</a><span>$verifypage_var </span>
        <p style='word-break: break-word;' id='commentContent_$id_4comm'>
        <span class='spanComment'>$comm_body</span><br/>
        <p style='margin: 0; padding: 0;'>
        <span class='comment_time'>".time_ago($comment_time)."</span>";
         if ($edited_4comm == "1") {
            $editedComment = " <span>&bull;</span> ".lang('comm_edited')." ($timeEdited_4commAgo)";
         }else{
            $editedComment="";
         }
         echo"<span id='editedComment_$id_4comm' style='font-size:11px;color:#808080;'> $editedComment</span>
        </p>
        </p>
        <div id='CommentLoading_$id_4comm'>
        </div>
        <div id='commentEditBox_$id_4comm' style='display:none;'>
        <textarea dir='auto' class='commentContent_EditBox' id='commEditBox_$id_4comm'>$content_4comm</textarea>
        <div style='margin-bottom: 15px;margin-top: 5px;'>
        <a href='javascript:void(0)' onclick=\"editComment_save('$id_4comm','$check_path')\" class='default_flat_btn'>".lang('save')."</a>
        <a href='javascript:void(0)' onclick=\"editComment_cancel('$id_4comm')\" class='silver_flat_btn'>".lang('cancel')."</a>
        </div>
        </td>
        <td>
        <div class='dropdown'>
        <a class='post_options dropdown-toggle' data-toggle='dropdown' style='float:".lang('float2').";' href='#'><span>&bull;&bull;&bull;</span></a>
        <ul class='dropdown-menu ".lang('postDropdown')."' style='top:10px;color:#999;text-align: ".lang('textAlign').";'>
        ";
        if ($author_id_4comm == $_SESSION['id']) {
        echo " 
        <li><a href='javascript:void(0)' onclick=\"editComment('$id_4comm')\"><span class='fa fa-pencil-square-o'></span> ".lang('comm_edit')."</a></li>
        <li><a href='javascript:void(0)' onclick=\"deleteComment('$id_4comm', '$get_post_id')\"><span class='fa fa-trash-o'></span> ".lang('comm_delete')."</a></li>";
        }elseif($_SESSION['admin'] > 0){
            echo " 
            <li><a href='javascript:void(0)' onclick=\"editComment('$id_4comm')\"><span class='fa fa-pencil-square-o'></span> ".lang('comm_edit')."</a></li>
            <li><a href='javascript:void(0)' onclick=\"deleteComment('$id_4comm', '$get_post_id')\"><span class='fa fa-trash-o'></span> ".lang('comm_delete')."</a></li>
            <li class='divider'></li>
            <li><a href='javascript:void(0)' onclick=\"return false;\"><span class='fa fa-bug'></span> ".lang('comm_report')."</a></li>";
        }else{
        echo "
        <li><a href='javascript:void(0)' onclick=\"return false;\"><span class='fa fa-bug'></span> ".lang('comm_report')."</a></li>";
        }
        echo"
        </ul>
        </div>
        </td>
        </tr>
    </table>
            ";
// send notification to user
$get_post_authorId = $conn->prepare("SELECT author_id FROM wpost WHERE post_id=:get_post_id");
$get_post_authorId->bindParam(':get_post_id',$get_post_id,PDO::PARAM_INT);
$get_post_authorId->execute();
while ($getAuthor = $get_post_authorId->fetch(PDO::FETCH_ASSOC)) {
$nId = time()+rand(0,999999999);
$s_id = $_SESSION['id'];
$for_id = $getAuthor['author_id'];
$notifyType = "comment";
$nSeen = "0";
$nTime = time();
if ($for_id != $s_id) {
$sendNotification = $conn->prepare("INSERT INTO notifications (n_id, from_id, for_id, notifyType_id, notifyType, seen, time) VALUES (:nId, :fromId, :forId, :notifyTypeId, :notifyType, :seen, :nTime)");
$sendNotification->bindParam(':nId',$nId,PDO::PARAM_INT);
$sendNotification->bindParam(':fromId',$s_id,PDO::PARAM_INT);
$sendNotification->bindParam(':forId',$for_id,PDO::PARAM_INT);
$sendNotification->bindParam(':notifyTypeId',$get_post_id,PDO::PARAM_INT);
$sendNotification->bindParam(':notifyType',$notifyType,PDO::PARAM_STR);
$sendNotification->bindParam(':seen',$nSeen,PDO::PARAM_INT);
$sendNotification->bindParam(':nTime',$nTime,PDO::PARAM_INT);
$sendNotification->execute();
}
}
// ==================================
exit();
?>
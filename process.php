<?php
require('connection.php');
require_once('functions.php');

if(isset($_POST['action']) && $_POST['action'] == 'register')
{
	register_user($_POST);
}

if(isset($_POST['action']) && $_POST['action'] == 'login')
{
	login_user($_POST);
}

if(isset($_POST['action']) && $_POST['action'] == 'message')
{
	post_message($_POST);
}
if(isset($_POST['action']) && $_POST['action'] == 'comment')
{
	foreach ($_POST as $key => $value)
	{
		$msg_id = intval($_POST['message_id']);
		$user = intval($_SESSION['user_id']);
		comment_query($msg_id, $user, $_POST['comment']);
	}
}
if(isset($_POST['action']) && $_POST['action'] == 'delete_com')
{
	$_SESSION['comm_id'] = $_POST['comment_id'];
	$comm_id = intval($_SESSION['comm_id']);
	delete_comment($comm_id);
	header('Location: main.php');
	die;
}
if(isset($_POST['action']) && $_POST['action'] == 'edit_msg')
{
	$_SESSION['msg_id'] = $_POST['message_id'];
	header('location: edit.php');
	die;
}
if(isset($_POST['action']) && $_POST['action'] == 'delete_msg')
{
	$_SESSION['msg_id'] = $_POST['message_id'];
	$msg_id = intval($_SESSION['msg_id']);
	delete_message_comments($msg_id);
	delete_message($msg_id);
	header('Location: main.php');
	die;
}
if(isset($_POST['action']) && $_POST['action'] == 'confirm_edit')
{
	edit_msg_query($_POST);
	header('Location: main.php');
	die;
}
if(isset($_POST['action']) && $_POST['action'] == 'cancel_edit')
{
	unset($_SESSION['msg_id']);
	header('Location: main.php');
	die;
}
if(isset($_POST['action']) && $_POST['action'] == 'log_off')
{
	session_destroy();
	header("Location: index.php");
	die();
}


?>
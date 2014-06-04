<?php
session_start();
require('connection.php');

date_default_timezone_set("America/Los_Angeles");

// var_dump($_POST);
// die;

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
if(isset($_POST['action']) && $_POST['action'] == 'log_off')
{
	session_destroy();
	header("Location: index.php");
	die();
}

function register_user($post)
{
	foreach ($post as $key => $value)
	{
		if(empty($value))
		{
			$_SESSION['error'][] = $key. " cannot be left blank.";
		}
		else
		{
			switch($key)
			{
				case 'first_name':
				$esc_fname = escape_this_string($_POST['first_name']);
				case 'last_name':
				$esc_lname = escape_this_string($_POST['last_name']);
				if(is_numeric($value))
				{
					$_SESSION['error'][] = $key. ' cannot contain numbers.';
				}
				break;
				case 'email':
				if(!filter_var($value, FILTER_VALIDATE_EMAIL))
				{
					$_SESSION['error'][] = $key. " is not a valid email.";
				}
				else
				{
					$query = "SELECT * FROM users";
					$existing = fetch_all($query);
					foreach ($existing as $ekey) {
						if($value == $ekey['email'])
						{
							$_SESSION['error'][] = $key. " has already been registered.";
						}
					}
				}
				break;
				case 'password':
				if (strlen($post['password']) < 6) {
					$_SESSION['error'][] = 'Password needs to be more than 6 characters long.';
				}
				break;
			}
		}
	}
	if(!isset($_SESSION['error']))
	{
		$esc_fname = escape_this_string($post['first_name']);
		$esc_lname = escape_this_string($post['last_name']);
		$esc_email = escape_this_string($post['email']);
		$esc_password = escape_this_string($post['password']);
		$query = "INSERT INTO users (first_name, last_name, email, password, created_at, updated_at) VALUES ('{$esc_fname}', '{$esc_lname}', '{$esc_email}', '{$esc_password}', NOW(), NOW())";
		run_mysql_query($query);
		$_SESSION['registered'] = "Thank you for submitting your information.";
	}
	header("Location: index.php");
}

function login_user($post)
{
	$query = "SELECT * FROM users WHERE users.password = '{$post['password']}' AND users.email = '{$post['email']}'";
	$user = fetch_all($query);
	if(count($user) > 0)
	{
		$_SESSION['user_id'] = $user[0]['id'];
		$_SESSION['first_name'] = $user[0]['first_name'];
		$_SESSION['logged_in'] = TRUE;
		header('location: main.php');
		die;
	}
	else
	{
		$_SESSION['login_error'][] = "There is no registered user with those credentials";
		header('location: index.php');
		die;
	}
}

function post_message($post)
{
	foreach ($post as $key => $value)
	{
		if(empty($value))
		{
			$_SESSION['post_error'][] = "Cannot post an empty message. Please type a message and try again.";
			header('Location: main.php');
			die;
		}
		else
		{
			$esc_post = escape_this_string($post['quote']);
			$query = "INSERT INTO messages (user_id, message, created_at, updated_at) VALUES ('{$_SESSION['user_id']}', '{$esc_post}', NOW(), NOW())";
			run_mysql_query($query);
			header('Location: main.php');
			die;
		}
	}
}
function grab_all_messages()
{
	$_SESSION['message'] = array();
	$query = "SELECT * FROM messages ORDER BY created_at DESC";
	$messages = fetch_all($query);
	return $messages;
}
function grab_user_name($array)
{
	$query2 = "SELECT CONCAT(users.first_name, ' ',users.last_name) AS name FROM users WHERE id = ".$array['user_id'];
	$user_name = fetch_record($query2);
	return $user_name['name'];
}
function grab_all_comments($array)
{
	$query = "SELECT * FROM comments WHERE comments.message_id = '{$array['id']}' ORDER BY comments.created_at ASC";
	$comments = fetch_all($query);
	if(count($comments) > 0)
	{
		$_SESSION['comment'] = 0;
		foreach ($comments as $array) {
			$name = grab_user_name($array);
			$date = date_create($array['created_at']);
			$_SESSION['comments'][] = $array['comment'];
		}
	}
	return $comments;
}
function comment_query($msg_id, $usr_id, $comment)
{
	$query = "INSERT INTO comments (message_id, user_id, comment, created_at, updated_at) VALUES ('{$msg_id}', '{$usr_id}', '{$comment}', NOW(), NOW())";
	run_mysql_query($query);
	header('Location: main.php');
	die;
}
?>
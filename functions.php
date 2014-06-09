<?php
session_start();

date_default_timezone_set("America/Los_Angeles");

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
				case 'last_name':
				if(is_numeric($value))
				{
					$_SESSION['error'][] = $key. ' cannot contain numbers.';
				}
				if (preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $value))
				{
					$_SESSION['error'][] = $key. ' cannot contain special characters.';
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
				case 'confirm_password':
				if ($post['confirm_password'] != $post['password']) {
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
	$esc_email = escape_this_string($post['email']);
	$query = "SELECT * FROM users WHERE users.password = '{$post['password']}' AND users.email = '{$esc_email}'";
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
		if(empty($post['quote']))
		{
			$_SESSION['main_error'][] = "Cannot post an empty message. Please type a message and try again.";
		}
		else
		{
			$esc_post = escape_this_string($post['quote']);
			$query = "INSERT INTO messages (user_id, message, created_at, updated_at) VALUES ('{$_SESSION['user_id']}', '{$esc_post}', NOW(), NOW())";
			run_mysql_query($query);
		}
		header('Location: main.php');
		die;
	}
}

// can combine grab_all_messages and grab_user_name by doing a joined query
// $query = "SELECT messages.*, messages.id AS message_id, CONCAT(users.first_name, ' ', users.last_name) AS user_name FROM messages JOIN users ON messages.user_id = users.id ORDER BY messages.created_at DESC;"

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

// can combine grab_all_comments and grab_user_name by doing a joined query
// $query = "SELECT comments.*, comments.id AS comment_id, CONCAT(users.first_name, ' ', users.last_name) AS user_name FROM comments JOIN users ON comments.user_id = users.id ORDER BY comments.created_at ASC;"

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
	if(empty($comment))
	{
		$_SESSION['main_error'][] = "Cannot post an empty comment. Please type a comment and try again.";
	}
	else
	{
		$esc_comment = escape_this_string($comment);
		$query = "INSERT INTO comments (message_id, user_id, comment, created_at, updated_at) VALUES ('{$msg_id}', '{$usr_id}', '{$esc_comment}', NOW(), NOW())";
		run_mysql_query($query);
	}
	header('Location: main.php');
	die;
}
function delete_comment($int)
{
	$query = "DELETE FROM comments WHERE id = '{$int}'";
	run_mysql_query($query);
	$_SESSION['main_success'][] = "Your comment has been successfully deleted.";
}
function message_grabber($int)
{
	$query = "SELECT message FROM messages WHERE messages.id = '{$int}'";
	$message = fetch_all($query);
	foreach ($message as $content) {
		$text = $content['message'];
	}
	return $text;
}
function delete_message($int)
{
	$query = "DELETE FROM messages WHERE id = '{$int}'";
	run_mysql_query($query);
	$_SESSION['main_success'][] = "Your message has been successfully deleted.";
}
function delete_message_comments($int)
{
	$query = "SELECT * FROM comments WHERE comments.message_id = '{$int}'";
	$comments = fetch_all($query);
	foreach ($comments as $comment) {
		$query2 = "DELETE FROM comments WHERE id = '{$comment['id']}'";
		run_mysql_query($query2);
		$_SESSION['main_success'][] = "All comments, attached to the corresponding message, have been deleted.";
	}
}
function edit_msg_query($post)
{
	if (empty($post['edit_msg_box'])) {
		$_SESSION['edit_error'][] = "Cannot edit your message into a blank message. Please edit your message to have content and try again.";
		header('location: edit.php');
		die;
	}
	else
	{
		$esc_comment = escape_this_string($post['edit_msg_box']);
		$query ="UPDATE messages SET message='{$esc_comment}', updated_at=NOW() WHERE id = '{$post['message_id']}'";
		run_mysql_query($query);
		$_SESSION['main_success'][] = "Your message has been successfully edited.";
		$_SESSION['edit'] = TRUE;
		header("location: main.php");
		die;
	}
}

?>
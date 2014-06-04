<?php
include('process.php');

?>

<!doctype html>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Wall Assignment - advanced version</title>
	<link rel="stylesheet" type="text/css" href="css.css">
</head>
<body>
	<div id="header_wrapper">
		<h1>CodingDojo Wall</h1>
		<p>Welcome <?php
		if(isset($_SESSION['first_name']))
		{
			echo $_SESSION['first_name'];
		}
		?>!</p>
		<form id="log_form" action="process.php" method="post">
			<input type="hidden" name="action" value="log_off">
			<button id="log_button">Log Off</button>
		</form>
	</div>
	<div id="content_wrapper">
		<div id="post_wrapper">
			<?php
			if(isset($_SESSION['message_deleted']))
			{
				foreach($_SESSION['message_deleted'] as $key => $message)
				{
					?>
					<p class="msg_delete_success"><?= $message ?></p>
					<?php
					unset($_SESSION['message_deleted']);
				}
			}
			?>
			<?php
			if(isset($_SESSION['message_editted']))
			{
				foreach($_SESSION['message_editted'] as $key => $message)
				{
					?>
					<p class="msg_edit_success"><?= $message ?></p>
					<?php
					unset($_SESSION['message_editted']);
				}
			}
			?>
			<h3>Post a message</h3>
			<?php
			if(isset($_SESSION['post_error']))
				{ ?>
			<p class="error"><?= $_SESSION['post_error'] ?></p>
			<?php
		}?>

		<form id="post_form" action="process.php" method="post">
			<input type="hidden" name="action" value="message">
			<textarea id="quote_box" name="quote" placeholder="Type your quote here" ></textarea>
			<button>Post a message</button>
		</form>
	</div>
	<div id="message_wrapper">
		<?php
		$messages = grab_all_messages();
		foreach ($messages as $array)
		{
			$name = grab_user_name($array);
			$date = date_create($array['created_at']);
			?>
			<h4><?= $name ?> - <?= date_format($date, 'F jS, Y \a\t h:i a') ?></h4>
			<?php
			if($array['created_at'] != $array['updated_at'])
			{
				$up_date = date_create($array['updated_at']);
				?>
				<p class="editted_timestamp">This message has been editted on <?= date_format($up_date, 'F jS, Y \a\t h:i a') ?></p>
				<?php
			}?>
			<p class="message"><?= $array['message'] ?></p>
			<?php
			if($array['user_id'] == $_SESSION['user_id'])
			{?>
			
			<form class="edit_msg_form" action="process.php" method="post">
				<input type="hidden" name="action" value="edit_msg">
				<input type="hidden" name="message_id" value="<?php echo $array['id']?>">
				<button>Edit message</button>
			</form>
			<?php
			$difference = time()-strtotime($array['created_at']);
			if ($difference < 1800)
			{
				?>
				<form class="delete_msg_form" action="process.php" method="post">
					<input type="hidden" name="action" value="delete_msg">
					<input type="hidden" name="message_id" value="<?php echo $array['id']?>">
					<button>Delete message</button>
				</form>
				<?php
			}	?>
			<?php
		}
		?>
		<h6>Post a message</h6>
		<form class="comment_form" action="process.php" method="post">
			<input type='hidden' name='action' value='comment'>
			<input type="hidden" name="message_id" value='<?php echo $array['id'] ?>'>
			<textarea name="comment" placeholder="Type your comment here" ></textarea>
			<button>Post a comment</button>
		</form>
		<div class="comment_wrapper">
			<?php
			$comments = grab_all_comments($array);
			foreach ($comments as $comment) {
				$comment_author = grab_user_name($comment);
				$date = date_create($comment['created_at']);
				if ($comment['message_id'] == $array['id']) {
					?>
					<h5><?= $comment_author ?> - <?= date_format($date, 'F jS, Y \a\t h:i a') ?></h5>
					<p class="comments"><?= $comment['comment'] ?></p>
					<?php
					unset($_SESSION['comments']);

				}
			}?>
			</div><?php
		}
		?>
	</div>
</div>
</body>
</html>
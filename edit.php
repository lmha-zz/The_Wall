<?php
include('process.php');

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Edit your comment</title>
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
		<?php 
		$msg_id = intval($_SESSION['msg_id']);
		$message = message_grabber($msg_id);
		?>
		<div id="edit_wrapper">
			<h3>Edit your message</h3>
			<form id="msg_editor_box"action="process.php" method="post">
				<input type="hidden" name="action" value="confirm_edit">
				<input type="hidden" name="message_id" value="<?= $msg_id ?>">
				<textarea name="edit_msg_box"><?= $message ?></textarea>
				<button id="edit_button" action="process.php">Done Editing</button>
			</form>
			<form id="skip_wrapper" action="main.php" method="post">
				<button id="cancel_button" action="main.php">Cancel</button>
			</form>
		</div>
	</div>
</body>
</html>
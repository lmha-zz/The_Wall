<?php
require('process.php');

?>

<!doctype html>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Wall Assignment - intermediate version</title>
	<link rel="stylesheet" type="text/css" href="css.css">
</head>
<body>
	<div id="header_wrapper">
		<h1>CodingDojo Wall</h1>
	</div>
	<div id="register_wrapper">
		<h2>New User:</h2>
		<?php 
		if(isset($_SESSION['error']))
		{
			foreach($_SESSION['error'] as $key => $message)
			{
				?>
				<p class="error"><?= $message ?></p>
				<?php
				unset($_SESSION['error']);
			}
		}
		elseif (isset($_SESSION['registered']))
		{
			?>
			<p class="success"><?= $_SESSION['registered'] ?></p>
			<?php
			unset($_SESSION['registered']);
		}
		?>
		<form action="process.php" method="post">
			<input type="hidden" name="action" value="register">
			<label>First Name: <input class="first_name_input" type="text" name="first_name" placeholder="Type your first name here"></label>
			<label>Last Name: <input class="last_name_input" type="text" name="last_name" placeholder="Type your last name here"></label>
			<label>Email: <input class="email_input" type="text" name="email" placeholder="Type your email here"></label>
			<label>Password: <input class="password_input" type="password" name="password" placeholder="Type your password here"></label>
			<label>Confirm Password: <input class="confirm_password" type="password" name="confirm_password" placeholder="Confirm your password here"></label>
			<button>Register</button>
		</form>
	</div>
	<div id="returning_wrapper">
		<h2>Returning User:</h2>
		<?php 
		if(isset($_SESSION['login_error']))
		{
			foreach($_SESSION['login_error'] as $key => $message)
			{
				?>
				<p class="login_error"><?= $message ?></p>
				<?php
				unset($_SESSION['login_error']);
			}
		}
		?>
		<form action="process.php" method="post">
			<input type="hidden" name="action" value="login">
			<label>Email: <input class="email_input" type="text" name="email" placeholder="Type your email here"></label>
			<label>Password: <input class="password_input" type="password" name="password" placeholder="Type your password here"></label>
			<button>Login</button>
		</form>
	</div>
</body>
</html>

<?php
session_destroy();

?>


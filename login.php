<?php
	session_start();
	require("connection.php");
	// require("include/functions.php");
?>
<!DOCTYPE HTML>
<html lang="en-US">
	<head>
		<meta charset="UTF-8">
		<title>Login and Registration</title>
		<!-- jQuery/js -->
		<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script> -->
		<!-- css -->
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div id="wrapper" class="container">
<?php 		if (isset($_SESSION["error_messages"])) //either reg or login
			{	?>
				<div id="display_messages" class="alert alert-block alert-error">
<?php 				if (isset($_SESSION["error_messages"]["registration"]))
					{
						foreach($_SESSION["error_messages"]["registration"] as $message)
						{	?>
							<p><?= $message ?></p>
<?php					}
					}
					else if (isset($_SESSION["error_messages"]["login"]))
					{
						foreach($_SESSION["error_messages"]["login"] as $message)
						{	?>
							<p><?= $message ?></p>
<?php					}
					}	?>
				</div> <!-- closes the alert block -->
<?php		}	?>

			<div id="registration" class="span4">
				<form id="registration_form" class="form-horizontal" action="process.php" method="post">
					<div class="control-group">
						<div class="controls">
							<legend>Register</legend>
						</div>
					</div>
					<div <?= (isset($_SESSION["error_messages"]["registration"]["first_name"])) ? "class='control-group error'" : "class='control-group'" ?> >
						<label class="control-label" for="first_name">First Name: </label>
						<div class="controls">
							<input class="span2" type="text" name="first_name" id="first_name" placeholder="First Name" />
						</div>
					</div>
					<div <?= (isset($_SESSION["error_messages"]["registration"]["last_name"]) ) ? "class='control-group error'" : "class='control-group'" ?> >
						<label class="control-label" for="last_name">Last Name: </label>
						<div class="controls">
							<input class="span2" type="text" name="last_name" id="last_name" placeholder="Last Name" />
						</div>
					</div>
					<div <?= (isset($_SESSION["error_messages"]["registration"]["email"]) ) ? "class='control-group error'" : "class='control-group'" ?> >
						<label class="control-label" for="email">Email Address: </label>
						<div class="controls">
							<input class="span2" type="text" name="email" id="email" placeholder="Email Address" />
						</div>
					</div>
					<div <?= (isset($_SESSION["error_messages"]["registration"]["password"]) ) ? "class='control-group error'" : "class='control-group'" ?> >
						<label class="control-label" for="password">Password: </label>
						<div class="controls">
							<input class="span2" type="password" name="password" id="password" placeholder="password" />
						</div>
					</div>
					<div <?= (isset($_SESSION["error_messages"]["registration"]["confirm_password"]) ) ? "class='control-group error'" : "class='control-group'" ?> >
						<label class="control-label" for="confirm_password">Confirm Password: </label>
						<div class="controls">
							<input class="span2" type="password" name="confirm_password" id="confirm_password" placeholder="confirm password" />
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="hidden" name="action" value="register" />
							<button type="submit" class="btn">Register</button>
						</div>
					</div>
				</form> <!-- End of registration form -->
			</div> <!-- End of registration div -->
			<div id="login" class="span4">
				<form id="login_form" class="form-horizontal" action="process.php" method="post">
					<div class="control-group">
						<div class="controls">
							<legend>Login</legend>
						</div>
					</div>
					<div <?= (isset($_SESSION["error_messages"]["login"]["email"])) ? "class='control-group error'" : "class='control-group'" ?> >
						<label class="control-label" for="email">Email Address: </label>
						<div class="controls">
							<input class="span2" type="text" name="email" id="email" placeholder="Email Address" />
						</div>
					</div>
					<div <?= (isset($_SESSION["error_messages"]["login"]["password"]) ) ? "class='control-group error'" : "class='control-group'" ?> >
						<label class="control-label" for="password">Password: </label>
						<div class="controls">
							<input class="span2" type="password" name="password" id="password" placeholder="password" />
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="hidden" name="action" value="login" />
							<button type="submit" class="btn">Login</button>
						</div>
					</div>
				</form> <!-- End of login form -->
			</div> <!-- End of login div -->
		</div> <!-- End of wrapper! -->
	</body>
</html>
<?php
	//unset both reg/login messages
	unset($_SESSION["error_messages"]);
?>
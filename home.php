<?php

session_start();

// var_dump($_SESSION);
// die();

if ($_SESSION["logged_in"])
{
	$user = $_SESSION["user"];
	//require("include/functions.php");
	//require("include/header.php");
}
else
{
	//echo "who are YOU?!!";
	header("location: login.php");
}
?>
<!DOCTYPE HTML>
<html lang="en-US">
	<head>
		<meta charset="UTF-8">
		<title><?= $user["first_name"] ?>'s Friend Finder</title>
		<!-- jQuery/js -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<!-- <script src="js/myscript.js"></script> -->
		<script type="text/javascript">
			$(document).ready(function(){
				//alert("Hi");
				//$("#user_list").append();
			});
		</script>

		<!-- css -->
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div id="wrapper" class="container">
			<div id="welcome" class="well">
				<div class="pull-right">
					<form id="logout" action="process.php" method="post">
						<button class="btn" type="submit" name="logout">Log Out</button>
					</form>
				</div>
				<p>Welcome, <?= $user["first_name"] ?>!</p>
				<p><?= $user["email"] ?></p>
			</div>
			<div id="friend_list" class="span9">
				<?= $_SESSION["friend_table"] ?>
			</div>
			<div id="user_list" class="span9">
				<?= $_SESSION["user_table"] ?>
			</div>

		</div> <!-- End of wrapper! -->
	</body>
</html>
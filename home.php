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
				$("#user_list").
			});
		</script>

		<!-- css -->
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div id="wrapper" class="container">
			<div id="welcome" class="well">
				<p>Welcome, <?= $user["first_name"] ?>!</p>
				<p><?= $user["email"] ?></p>
			</div>
			<div id="friend_list">
				<?#= get_friends() ?>
			</div>
			<div id="user_list">
				<?#= get_users() ?>
			</div>

		</div> <!-- End of wrapper! -->
	</body>
</html>
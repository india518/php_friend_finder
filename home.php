<?php
session_start();

if ($_SESSION["logged_in"])
	$current_user = $_SESSION["user"];
else
	header("location: login.php");
?>
<!DOCTYPE HTML>
<html lang="en-US">
	<head>
		<meta charset="UTF-8">
		<title><?= $current_user["first_name"] ?>'s Friend Finder</title>
		<!-- jQuery/js -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<!-- <script src="js/myscript.js"></script> -->
		<script type="text/javascript">
			$(document).ready(function(){
				
				$(document).on("submit", ".add_friend", function(){
					$.post(
						$(this).attr("action"),
						$(this).serialize(),
						function(data){
							//alert("Are we here?");
							$("#friend_list").html(data["friend_table"]);
							$("#user_list").html(data["user_table"]);
						},
						"json"
					);
					return false;
				});

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
				<p>Welcome, <?= $current_user["first_name"] ?>!</p>
				<p><?= $current_user["email"] ?></p>
			</div>
			<div id="friend_list" class="span5">
				<?= $_SESSION["friend_table"] ?>
			</div>
			<div id="user_list" class="span7">
				<?= $_SESSION["user_table"] ?>
			</div>

		</div> <!-- End of wrapper! -->
	</body>
</html>
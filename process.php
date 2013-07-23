<?php

include_once("connection.php");
session_start();

class Process
{
	var $connection;

	function __construct()
	{
		$this->connection = new Database();

		if (isset($_POST['action']) AND $_POST["action"] == "login")
			$this->login();
		else if (isset($_POST['action']) AND $_POST["action"] == "register")
			$this->register();
		// else if (isset($_POST['action']) AND $_POST["action"] == "post_message")
		// 	post_message();
		// else if (isset($_POST['action']) AND $_POST["action"] == "post_comment")
		// 	post_comment();
		// else if (isset($_POST['action']) AND $_POST["action"] == "delete_message")
		// 	remove_message();
		else
		{
			//We are assuming the user wants to log out - for now
			session_destroy();
			header("location: login.php");
		}
	}

	function email_validation()
	{
		$message = NULL;
		if (empty($_POST["email"]))
			$message = "Email address cannot be blank.";
		else if (! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) )
			$message = "Email should be in valid format.";
		return $message;
	}

	function login()
	{
		$errors = array();

		$email_message = $this->email_validation();
		if ($email_message)
			$errors["email"] = $email_message;

		if (count($errors) > 0)
		{
			$_SESSION["error_messages"]["login"] = $errors;
			header("location: login.php");
		}
		else
		{
			// Check that user exists
			$find_user_query = "SELECT users.id, users.first_name, CONCAT(users.first_name, ' ',users.last_name) AS full_name, users.email, users.password  FROM users WHERE users.email = '{$_POST['email']}'";
			// echo $find_user_query;
			// die();

			$user = $this->connection->fetch_record($find_user_query);
			if (! $user)
			{
				$_SESSION["error_messages"]["login"]["user"] = "There is no account with this email address. Try registering for a new account!";
					header("location: login.php");
			}
			else // we found a user!
			{
				// but is their password valid?
				if (md5($_POST["password"]) != $user["password"])
				{
					$_SESSION["error_messages"]["login"]["password"] = "Incorrect password.";
					header("location: login.php");
				}
				else
				{
					// The password *is* valid!
					// Now, we create a user object (of sorts) in the $_SESSION variable, and log them in!
					$_SESSION["user"] = array(
						"id" => intval($user["id"]),
						"first_name" => $user["first_name"],
						"full_name" => $user["full_name"],
						"email" => $user["email"]
					);
					$_SESSION["logged_in"] = TRUE;
					header("location: home.php");
					//yay!
				}
			}
		}
	} //end login()

	function register()
	{
		$min_password_length = 7;
		$errors = array();

		//First name validation
		if (empty($_POST["first_name"]))
			$errors["first_name"] = "First Name cannot be blank.";
		else if (preg_match("#[\d]#", $_POST["first_name"]))
			//note: is_numeric($_POST["first_name"]) doesn't really work!
			// it will allow a first name like "India518" when it shouldn't!
			$errors["first_name"] = "First Name cannot contain numbers.";
		//Last name validation
		if (empty($_POST["last_name"]))
			$errors["last_name"] = "Last Name cannot be blank.";
		else if (preg_match("#[\d]#", $_POST["last_name"]))
			$errors["last_name"] = "Last Name cannot contain numbers.";
		//Email validation
		$message = $this->email_validation();
		if ($message)
			$errors["email"] = $message;

		// Password format validation
		if (empty($_POST["password"]))
			$errors["password"] = "Password cannot be blank.";
		else if (strlen($_POST["password"]) < $min_password_length)
			$errors["password"] = "Password should be at least {$min_password_length} characters.";
		//Confirm password validation
		if (empty($_POST["confirm_password"]))
			$errors["confirm_password"] = "The Confirm Password field cannot be blank.";
		else if ($_POST["confirm_password"] != $_POST["password"])
			$errors["confirm_password"] = "Passwords do not match.";

		if (count($errors) > 0)
		{
			$_SESSION["error_messages"]["registration"] = $errors;

			// var_dump($_SESSION);
			// die();

			header("location: login.php");
		}
		else
		{
			//FIRST - check to see if user already exists
			$find_user_query = "SELECT users.* FROM users WHERE users.email = '{$_POST['email']}'";
			$user = $this->connection->fetch_record($find_user_query);

			if ($user)
			{
				$_SESSION["error_messages"]["registration"]["user"] = "A user with this email already exists. Try logging in!";
				
				header("location: login.php");
			}
			else
			{
				//NEXT: we have a new, unique user. Stick 'em in the database!
				$first_name = mysql_real_escape_string($_POST["first_name"]);
				$last_name = mysql_real_escape_string($_POST["last_name"]);
				$email = mysql_real_escape_string($_POST["email"]);
				$password = md5(mysql_real_escape_string($_POST["password"]));
				$insert_user_query = "INSERT INTO users (first_name, last_name, email, password, created_at) VALUES ('{$first_name}', '{$last_name}', '{$email}', '{$password}', NOW())";
				//$this->connection->mysql_query($insert_user_query);
				mysql_query($insert_user_query);
				//NOTE: Add the validation for mysql_affected_id or whatever here...

				header("location: home.php");
			}
		}
	} //end registration()

	function check_post_text($text, $item)
	{
		//$item tells us whether this is a message or a comment
		if (strlen($text) < 1)
		{
			$_SESSION["posting_error_message"] = "You can't post an empty {$item}! Try entering some text and try again.";
			return false;
			//header("location: wall.php");
		}
		return true;
	}

	// function post_message()
	// {
	// 	//Check that message isn't blank
	// 	$message = $_POST["message"];
	// 	$test = check_post_text($message, "message");
	// 	if ($test)
	// 	{	//Store message in database
	// 		$user_id = $_SESSION["user"]["id"];
	// 		$insert_message_query = "INSERT INTO messages (user_id, message, created_at) VALUES ('{$user_id}', '{$message}', NOW())";
	// 		mysql_query($insert_message_query);
	// 	}
	// 	header("location: wall.php");
	// }

	// function post_comment()
	// {
	// 	//Check that comment isn't blank
	// 	$comment = $_POST["comment"];
	// 	$test = check_post_text($comment, "comment");
	// 	if ($test)
	// 	{	//Store comment in database
	// 		$message_id = $_POST["message_id"];
	// 		$user_id = $_SESSION["user"]["id"];
	// 		$insert_comment_query = "INSERT INTO comments (message_id, user_id, comment, created_at) VALUES ('{$message_id}', '{$user_id}', '{$comment}', NOW())";
	// 		mysql_query($insert_comment_query);
	// 	}
	// 	header("location: wall.php");
	// }

	// function remove_message()
	// {
	// 	$message_id = $_POST["message_id"];
	// 	//first, remove all comments on that message
	// 	remove_comments($message_id);
	// 	// now remove the message itself
	// 	$delete_message_query = "DELETE FROM messages WHERE id = {$message_id}";
	// 	mysql_query($delete_message_query);
	// 	header("location: wall.php");
	// }

	// function remove_comments($message_id)
	// {
	// 	$delete_comments_query = "DELETE FROM comments WHERE message_id = {$message_id}";
	// 	mysql_query($delete_comments_query);
	// }

}

$process = new Process();
	
?>
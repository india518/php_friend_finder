<?php

include_once("connection.php");
require("include/HTML_helper.php");
session_start();

class Process
{
	var $connection;

	function __construct()
	{
		$this->connection = new Database();

		if (isset($_POST['action']))
		{
			if (($_POST["action"] == "login") OR ($_POST["action"] == "register"))
			{
				$form = $_POST["action"]; //which form did we come from?
				$this->signIn($form);
			}
			else if ($_POST["action"] == "add_friend")
			{
				$friend_user_id = $_POST["add_id"];
				$status = $this->add_friend($friend_user_id);
				if($status)
				{	// refresh friend table:
					$data = Array();
					$friends = $this->get_friends();
					$data["friend_table"] = $this->build_friend_table($friends);
					$users = $this->get_users();
					$data["user_table"] = $this->build_user_table($friends, $users);

					//update $_SESSION variables in case of page reload:
					$_SESSION["friend_table"] = $this->build_friend_table($friends);
					$_SESSION["user_table"] = $this->build_user_table($friends, $users);
					echo json_encode($data);
					//NOTE: Question to Chris
					//Is it better to set the session variables as above, or
					// should I do something more like this:

					/*
					$friends = $this->get_friends();
					$_SESSION["friend_table"] = $this->build_friend_table($friends);
					$users = $this->get_users();
					$_SESSION["user_table"] = $this->build_user_table($friends, $users);
					echo json_encode($_SESSION);
					*/

					//PROs: No need for an extra array variable ($data)
					//Cons: We're sending the WHOLE session back, when we
					// really just need two items in $_SESSION. Could
					// this turn out to be a bad practice, in a complicated app
					// with much more info in session?
				}
				else
				{
					$_SESSION["add_friend_error"] = "Problem creating friendship in the database!";
					header("location: home.php");
				}
			}
			else if ($_POST["action"] == "logout")
			{
				session_destroy();
				header("location: login.php");
			}
		}
		else
		{	//User shouldn't get here, lets log out and re-direct to login
			session_destroy();
			header("location: login.php");
		}
	}

	function signIn($form)
	{
		if ($form == "login")
			$status = $this->loginValidation();
		else
			$status = $this->registerValidation();

		if($status)
		{	//this is when the page loads, so not called from AJAX
			$friends = $this->get_friends();
			$users = $this->get_users();
			$_SESSION["friend_table"] = $this->build_friend_table($friends);
			$_SESSION["user_table"] = $this->build_user_table($friends, $users);
			header("location: home.php");
			//$data = Array();
			//$data["friend_table"] = $this->build_friend_table($friends);
			//$data["user_table"] = $this->build_user_table($friends, $users);
			//echo json_encode($data);
		}
		else
			header("location: login.php");
	}

	private function loginValidation()
	{
		$errors = array();
		$email_message = $this->email_validation();
		if ($email_message)
			$errors["email"] = $email_message;

		if (count($errors) > 0)
		{
			$_SESSION["error_messages"]["login"] = $errors;
			return FALSE;
		}
		else
		{	// Check that user exists
			$find_user_query = "SELECT users.id, users.first_name, CONCAT(users.first_name, ' ',users.last_name) AS full_name, users.email, users.password  FROM users WHERE users.email = '{$_POST['email']}'";
			$user = $this->connection->fetch_record($find_user_query);
			if (! $user)
			{
				$_SESSION["error_messages"]["login"]["user"] = "There is no account with this email address. Try registering for a new account!";
					return FALSE;
			}
			else // we found a user!
			{	// but is their password valid?
				if (md5($_POST["password"]) != $user["password"])
				{
					$_SESSION["error_messages"]["login"]["password"] = "Incorrect password.";
					return FALSE;
				}
				else
				{	// The password *is* valid! Populate $_SESSION and log in!
					$_SESSION["user"] = array(
						"id" => intval($user["id"]),
						"first_name" => $user["first_name"],
						"full_name" => $user["full_name"],
						"email" => $user["email"]
					);
					$_SESSION["logged_in"] = TRUE;
					return TRUE;
					//yay!
				}
			}
		}
	} //end loginValidation()

	private function register()
	{
		$status = $this->registerValidation();

		if($status)
			header("location: home.php");
		else
			header("location: login.php");
	}

	private function registerValidation()
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
			return FALSE;
		}
		else
		{	//Does user already exists?
			$find_user_query = "SELECT users.* FROM users WHERE users.email = '{$_POST['email']}'";
			$user = $this->connection->fetch_record($find_user_query);

			if ($user)
			{
				$_SESSION["error_messages"]["registration"]["user"] = "A user with this email already exists. Try logging in!";
				return FALSE;
			}
			else
			{	//NEXT: we have a new, unique user. Stick 'em in the database!
				$first_name = mysql_real_escape_string($_POST["first_name"]);
				$last_name = mysql_real_escape_string($_POST["last_name"]);
				$email = mysql_real_escape_string($_POST["email"]);
				$password = md5(mysql_real_escape_string($_POST["password"]));
				$insert_user_query = "INSERT INTO users (first_name, last_name, email, password, created_at) VALUES ('{$first_name}', '{$last_name}', '{$email}', '{$password}', NOW())";
				mysql_query($insert_user_query);
				$id = mysql_insert_id();
				//NOTE: At some point, add the validation for mysql_affected_id or whatever here...

				//populate $_SESSION and let the new kid in to play!
				$_SESSION["user"] = array(
						"id" => $id,
						"first_name" => $first_name,
						"full_name" => "{$first_name} {$last_name}",
						"email" => $email
				);

				$_SESSION["logged_in"] = TRUE;
				return TRUE;
			}
		}
	} //end registration()

	function email_validation()
	{
		$message = NULL;
		if (empty($_POST["email"]))
			$message = "Email address cannot be blank.";
		else if (! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) )
			$message = "Email should be in valid format.";
		return $message;
	}

	// NOTE: Should the following function
	//  get_users(), get_friends(), build_friend_table, build_user_table
	// be in another class somewhere? Do they still belong in Process?

	function get_users()
	{
		//NOTE: this gets all users, (except current user) but not whether
		// they are friends with current user - will have to add that in!
		$get_users_query =<<<_SQL
			SELECT CONCAT(users.first_name,' ',users.last_name) AS name, users.email, users.id
			FROM users
			WHERE users.id != '{$_SESSION['user']['id']}'
_SQL;
		return $this->connection->fetch_all($get_users_query);
	}

	function get_friends()
	{
		$get_friends_query =<<<_SQL
			SELECT CONCAT(users.first_name,' ',users.last_name) AS name, users.email, users.id
			FROM users
			JOIN friends ON users.id = friends.user_id
			WHERE friends.friend_id = '{$_SESSION['user']['id']}'
_SQL;
		return $this->connection->fetch_all($get_friends_query);
	}

	function build_friend_table($friends)
	{
		if (count($friends) > 0)
		{
			$friends_caption = "List of Friends";
			$friend_table = new HTML_helper;
			return $friend_table->print_table($friends, $friends_caption);
		}
		else
		{	//no friends :(
			return "";
		}
	}

	function build_user_table($friends, $users)
	{
		$user_caption = "List of Users subscribed to Friend Finder";
		$user_table = new HTML_helper;

		foreach($users as $key=>$user)
		{
			$found = FALSE;
			foreach($friends as $friend)
			{
				if ($user['id'] ==  $friend['id']) 
				{
					$users[$key]["action"] = "Friends";
					$found = TRUE;
				}
			}

			if (! $found)
			{
				$form  =<<<_HTML
					<form class='add_friend' action='process.php' method='post'>
						<!-- ARG! -->
						<!-- The values stored in Input type="submit" does not get submitted in the form via Ajax! -->
						<input type='hidden' name='add_id' value='{$user['id']}' />
						<input type='hidden' name='action' value='add_friend' data-user_id='{$user['id']}' />
						<button class='btn' type='submit'>Add Friend</button>
					</form>
_HTML;
				$users[$key]["action"] = $form;
			}
		}

		return $user_table->print_table($users, $user_caption);
	}

	function add_friend($friend_user_id)
	{
		//We need to INSERT INTO the friends join table twice, so that we
		// have a reciprocal friendship
		$friendship_query =<<<_SQL
			INSERT INTO friends (user_id, friend_id)
			VALUES ({$_SESSION['user']['id']}, {$friend_user_id}),
			({$friend_user_id}, {$_SESSION['user']['id']})
_SQL;
		mysql_query($friendship_query);
		$count = mysql_affected_rows();
		if ($count == 2)
			return TRUE;
		else
			return FALSE;
	}

}

$process = new Process();
	
?>
<?php

session_start();

if ($_SESSION["logged_in"])
{
	echo "Welcome, " . $_SESSION["user"]["first_name"];
}
else
{
	echo "who are YOU?!!";
}

?>
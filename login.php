<?php
//---------------------------------------------------------------------------------------------------------------------
// Admin: Main for admin application.
//---------------------------------------------------------------------------------------------------------------------
// Connect to database.
session_start();

// Authenticate user.
$a = authenticate($d['logon_username'], $d['logon_pswd'], $db);
$authenticated = $a["authenticated"];
$error = $a["error"];
// Logoff.
if (!empty($_REQUEST["diablo_logoff"]) AND $authenticated) {
  session_unset();
  session_destroy();
  $authenticated = 0;
}
// Display page.
$p = $_REQUEST["p"];
if (!$authenticated) {
  display_logon($db, $error);
} else {
	include_once ("php/controllers/page.php");
	$p = new Page($db, $p);
	$p->display();
}
// Authenticate user.
// $user = username
// $pswd = password
// $db = the db object
// Returns an array.
function authenticate($user, $pswd, $db) {
	$error = "";
	$valid_user = array("user1", "user2");
	$valid_pswd = "empire";
	$authenticated = 0;
	//session_start();
	// Check username and password.
	if (!empty($user) OR !empty($pswd)) {
		$user_str = " " . implode(",", $valid_user);
		if (!strpos($user_str, $user) OR ($pswd != $valid_pswd)) {
			$error = "Invalid user or password.";
		} else {
			$authenticated = 1;
			$_SESSION['username'] = $user;
			$_SESSION["user_type"] = "Administrator";
		}
	// Otherwise, authenticate if user session already exists.
	} elseif (!empty($_SESSION['username']) AND $_SESSION["user_type"] == "Administrator") {
		$authenticated = 1;
	}
	$a = array("authenticated"=>$authenticated, "error"=>$error);
	return $a;
}
// Display admin logon.
// $error = optional error string
function display_logon($db, $error) {
  // Output page.
	echo "<!DOCTYPE html>";
	echo "<html lang='en'>";
	echo "<head>";
  echo "<meta charset='utf-8'>";
  echo "<meta http-equiv='X-UA-Compatible' content='IE=edge'>";
  echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
  echo "<meta name='description' content=''>";
  echo "<meta name='author' content=''>";
	echo "<link href=\"images/favicon.png\" rel=\"SHORTCUT ICON\" />";
	echo "<link href=\"images/favicon.png\" rel=\"icon\" type=\"image/x-icon\" />";
  echo "<title>Logon</title>";
  echo "<link href='css/bootstrap.min.css' rel='stylesheet'>";
  echo "<link href='css/signin.css' rel='stylesheet'>";
	echo "</head>";
	echo "<body>";
	echo "<div class='container'>";
	echo "<form name='admin' enctype='multipart/form-data' action='admin.php' method='post' class='form-signin' role='form'>";
	echo "<div class='panel panel-default'>";
	echo "<div class='panel-heading'><strong>Diablo Timing</strong></div>";
	echo "<div class='panel-body'>";
	// Display the form fields.
	display_form_fields($db);
	echo "<button class='btn btn-lg btn-primary btn-block' type='submit'>Sign In</button>";
	// Display error or informational message.
	if (!empty($error)) echo "<div class='alert alert-danger' role='alert'>$error</div>";
	echo "</div>";	// Panel body
	echo "</div>";	// Panel
	echo "</form>"; // form
  echo "</div>";  // container
  echo "</body>";
  echo "</html>";
}
function display_form_fields($db) {
	$form = "logon_admin";
  $fields = array();
  if ($_POST["logon_cancel"] != "Cancel" AND !empty($_POST["fn"])) {
		$fields  = array_combine($_POST["fn"], $_POST["fv"]);
	}
  include_once("php/views/form.php");
  $f = new Form($db);
	$data = $f->getFormFields($form);
	foreach($data AS $k=>$v) {
		if ($v["display_name"] == "Username") {
			$af = "autofocus";
			$t = "text";
		} elseif ($v["input"] == "password") {
			$af = "";
			$t = "password";
		} else {
			$af = "";
			$t = "text";
		}
		$val = $fields[$v["db_name"]];
		echo "<input type='hidden' name='fn[]' value='" . $v["db_name"] . "'>";
    echo "<input type='$t' name='fv[]' class='form-control' placeholder='".$v["display_name"]."' autocomplete='off' value='$val' $af>";
  }
}
?>

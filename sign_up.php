<!-- Register a new user. -->
<?php
include('config.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="assets/style.css" rel="stylesheet" title="Style" />
		<title>Sign up</title>
	</head>
	<body>
<?php
// check if the form has been sent
if(isset($_POST['username'], $_POST['password'], $_POST['passverif'], $_POST['email']) and $_POST['username'] != '')
{
	// remove slashes depending on the configuration
	if(get_magic_quotes_gpc())
	{
		$_POST['username']  = stripslashes($_POST['username']);
		$_POST['password']  = stripslashes($_POST['password']);
		$_POST['passverif'] = stripslashes($_POST['passverif']);
		$_POST['email']  	= stripslashes($_POST['email']);
	}
	// check if the two passwords are identical
	$errors = [];
	if($_POST['password'] == $_POST['passverif'])
	{
		// check if the choosen password is strong enough.
		if(checkPassword($_POST['password'], $errors))
		{
			// check if the email form is valid
			if(preg_match('#^(([a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+\.?)*[a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+)@(([a-z0-9-_]+\.?)*[a-z0-9-_]+)\.[a-z]{2,}$#i', $_POST['email']))
			{
				// protect the variables
				$username = mysqli_real_escape_string($link, $_POST['username']);
				$password = mysqli_real_escape_string($link, $_POST['password']);
				$email	  = mysqli_real_escape_string($link, $_POST['email']);
				$salt	  = (string)rand(10000, 99999);	     // generate a five digit salt
				$password = hash("sha512", $salt.$password); // compute the hash of salt concatenated to password
				// check if there is no other user with the same username
				$dn = mysqli_num_rows(mysqli_query($link, 'select id from users where username="'.$username.'"'));
				if($dn == 0)
				{
					// We save the informations to the databse
					if(mysqli_query($link, 'insert into users(username, password, email, salt) values ("'.$username.'", "'.$password.'", "'.$email.'","'.$salt.'")'))
					{
						// We dont display the form
						$form = false;
?>
		<div class="message">You have successfuly been signed up. You can now log in.<br />
        <a href="login.php">Log in</a></div>
<?php
					}
					else
					{
						// Otherwise, we say that an error occured
						$form	= true;
						$message = 'An error occurred while signing up.';
					}
				}
				else
				{
					// Otherwise, we say the username is not available
					$form	= true;
					$message = 'The username is already in use, please choose another one.';
				}
			}
			else
			{
				// Otherwise, we say the email is not valid
				$form	= true;
				$message = 'The email adress is invalid.';
			}
		}
		else
		{
			// Otherwise, we say the password is too weak
			$form	= true;
			$message = '';
			foreach ($errors as $item)
				$message = $message.$item."<BR>";
		}
	}
	else
	{
		// Otherwise, we say the passwords are not identical
		$form	 = true;
		$message = 'The passwords are not identical.';
	}
}
else
{
	$form = true;
}
if ($form) {
	//We display a message if necessary
	if(isset($message)) echo '<div class="message">'.$message.'</div>';

	//We display the form again
?>
		<div class="content">
			<form action="sign_up.php" method="post">
				Please fill in the following form to sign up:<br />
				<div class="center">
					<label for="username">Username</label><input type="text" name="username" value="<?php if(isset($_POST['username'])){echo htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');} ?>" /><br />
					<label for="password">Password<span class="small"> (10 characters min.)</span></label><input type="password" name="password" /><br />
					<label for="passverif">Password<span class="small"> (verification)</span></label><input type="password" name="passverif" /><br />
					<label for="email">Email</label><input type="text" name="email" value="<?php if(isset($_POST['email'])){echo htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8');} ?>" /><br />
					<input type="submit" value="Sign up" />
				</div>
			</form>
		</div>
<?php
}
?>
		<div class="foot"><a href="index.php">Home</a></div>
	</body>
</html>

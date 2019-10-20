<?php
// start the session
session_start();

// configure the connection to the Heroku Database:
$heroku_svr = 'eu-cdbr-west-02.cleardb.net:3306';	// db URL
$heroku_usr = 'ba94587e2266f9';						// user
$heroku_pwd = 'b2ff9a09';							// password
$heroku_sch = 'heroku_45ca0508f4cea63';				// schema
$link	    = new mysqli($heroku_svr, $heroku_usr, $heroku_pwd, $heroku_sch);	// connect to the db

if (!$link) {
	die('Could not connect: ' . mysqli_error());
}

// checkPassword: check if the password is strong enough
// $pwd receives the password to test
// $errors returns the non-compliant items of the provided password
function checkPassword($pwd, &$errors) {
	$errors_init = $errors;

	if (strlen($pwd) < 10) $errors[] = "Password must be at least 10 characters long!";
	if (!preg_match("#[0-9]+#", $pwd)) $errors[] = "Password must include at least one number!";
	if (!preg_match("#[a-zA-Z]+#", $pwd)) $errors[] = "Password must include at least one letter!";
	if (!preg_match("#[a-z]+#", $pwd)) $errors[] .= "Password must include at least one lowercase letter!";
	if (!preg_match("#[A-Z]+#", $pwd)) $errors[] .= "Password must include at least one uppercase letter!";
	if (!preg_match("#\W+#", $pwd)) $errors[] .= "Password must include at least one symbol!";

	return ($errors == $errors_init);
}

// getKey: retrieve password for encrypted messages in database
// $user1 and $user2: the users that the message belongs to
function getKey($user1, $user2) {
	global $link;

	//Message DataBase. Access data cryptography hardcoded.
	$cipher = "aes-256-cbc";
	$ivlen  = openssl_cipher_iv_length($cipher);
	$iv		= base64_decode("5AIQwo8fWMKaUxDI9R9YwppTwqPCmlPCo5emwo8fWOg"); // hardcoded random iv with 256 bits
	$dbkey  = base64_decode("zT/PiCvCiUYtd96Pwogrwp1Br0lGLd7PiCvCicKdbUs"); // hardcoded random key with 256 bits

	if ($user1 > $user2) {	// always ensure the same ordering of the users - swap if necessary
		$tmp = $user1;
		$user1 = $user2;
		$user2 = $tmp;
	};

	$method = openssl_get_cipher_methods();
	if (in_array($cipher, $method)) {
		$key = base64_encode(openssl_random_pseudo_bytes(24)); // generate a new random key (192 bits) in case it is the first message
		$encrypted_key = openssl_encrypt($key, $cipher, $dbkey, 0, $iv);

		$req = mysqli_query($link, 'select * from messagekeys where user1="'.$user1.'" and user2="'.$user2.'"');
		$dn  = mysqli_num_rows($req);
		$dat = mysqli_fetch_array($req);

		// check if it is the first message between the two users
		if ($dn == 0) mysqli_query($link, 'insert into messagekeys(user1, user2, mskey) values ('.$user1.', "'.$user2.'", "'.$encrypted_key.'")');
		else {
			$cryp_key = $dat['mskey'];
			$key = openssl_decrypt($cryp_key, $cipher, $dbkey, 0, $iv);
		}
		return $key;
	}
	else return false;
}
?>

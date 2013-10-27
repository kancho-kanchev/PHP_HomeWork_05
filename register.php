<?php 
$pageTitle = 'Форма за регистрация';
require_once 'includes'.DIRECTORY_SEPARATOR.'header.php';
require_once 'includes'.DIRECTORY_SEPARATOR.'connection.php';
if (isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == true) {
	header('Location: index.php');
	exit;
}
if ($_POST) {
	if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['nickname'])) {
		$error = false;
		$password = trim($_POST['password']);
		if (mb_strlen($password, 'UTF-8') < 3 || mb_strlen($password, 'UTF-8') > 20) {
			echo 'Паролата трябва да е между 3 и 20 символа</br>'."\n";
			$error = true;
		}
		else if (!(!preg_match('/[^A-Za-z0-9]/', $password))) {
			echo 'Паролата може да съдържа букви и цифри</br>'."\n";
			$error = true;
		}
		$username = trim($_POST['username']);
		if (mb_strlen($username, 'UTF-8') < 3 || mb_strlen($username, 'UTF-8') > 20) {
			echo 'Потребителското име трябва да е между 3 и 20 символа</br>'."\n";
			$error = true;
		}
		else if (!(!preg_match('/[^A-Za-z0-9]/', $username) && (ctype_alpha($username[0])))) {
			echo 'Потребителското име трябва да започва с буква и може да съдържа букви и цифри</br>'."\n";
			$error = true;
		}
		$nickname = trim($_POST['nickname']);
		if (mb_strlen($nickname, 'UTF-8') < 5 || mb_strlen($nickname, 'UTF-8') > 20) {
			echo 'Прякора трябва да е между 5 и 20 символа</br>'."\n";
			$error = true;
		}
		else if (!(!preg_match('/[^A-Za-z0-9]/', $nickname) && (ctype_alpha($nickname[0])))) {
			echo 'Прякора трябва да започва с буква и може да съсържа букви и цифри</br>'."\n";
			$error = true;
		}
		$firstname = trim($_POST['firstname']);
		if (mb_strlen($firstname, 'UTF-8') > 20) {
			echo 'Името трябва да е по-малко от 20 символа</br>'."\n";
			$error = true;
		}
		else if (!(!preg_match('/[^A-Za-z]/', $firstname))) {
			echo 'Името трябва да започва с буква и може да съдържа букви</br>'."\n";
			$error = true;
		}
		
		$lastname = trim($_POST['lastname']);
		if (mb_strlen($lastname, 'UTF-8') > 20) {
			echo 'Фамилията трябва да е по-малко от 20 символа</br>'."\n";
			$error = true;
		}
		else if (!(!preg_match('/[^A-Za-z]/', $lastname))) {
			echo 'Фамилията трябва да започва с буква и може да съдържа букви</br>'."\n";
			$error = true;
		}
		
		$email = trim($_POST['email']);
		if (mb_strlen($email, 'UTF-8') < 5 || mb_strlen($email, 'UTF-8') > 50) {
			echo 'Мейлът трябва да е между 5 и 50 символа</br>'."\n";
			$error = true;
		}
		else if (!(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/', $email) && (ctype_alpha($email[0])))) {
			echo 'Мейлът не е валиден</br>'."\n";
			$error = true;
		}
	}
	else {
		echo 'Потребителското име, паролата, прякора и мейла са задължителни<br>'."\n";
	}
	if (!($stmt = mysqli_prepare($connection, 'SELECT username, nickname, email FROM users WHERE username=? OR nickname=? OR email=?'))) {
		//echo mysqli_error($connection);
		header('error.php?message=databaseerror');
		exit;
	}
	else {
		mysqli_stmt_bind_param($stmt, 'sss', $username, $nickname, $email);
		mysqli_stmt_execute($stmt);
		$rows = mysqli_stmt_result_metadata($stmt);
		while ($field = mysqli_fetch_field($rows)) {
			$fields[] = &$row[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $fields);
		while (mysqli_stmt_fetch($stmt)) {
			echo 'Потребителското име, прякорът и/или мейла са заети</br>'."\n";
			$error = true;
		}
		$message = 'Невалидно потребителско име или парола.';
	}
	if (!$error) {
		$rigths = 1;
		if (!($stmt = mysqli_prepare($connection, 'INSERT INTO users(username, password, nickname, firstname, lastname, email, rights) VALUES (?, ?, ?, ?, ?, ?, ?)'))) {
			//echo mysqli_error($connection);
			//echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			header('error.php?message=databaseerror');
			exit;
		}
		if (!$stmt->bind_param("ssssssi", $username, $password, $nickname, $firstname, $lastname, $email, $rigths)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		echo 'Записа е успешен';
		$_SESSION['isLogged'] = true;
		$_SESSION['username'] = $username;
		header('Location: index.php');
		exit;
	}
	else {
		echo "\n".'Неуспешна регистрация';
	}
}
?>
	<form method="POST" action="register.php">
		<div>Потребителско име:<input type="text" name="username" value="<?= (isset($username)) ? $username : '';?>"/></div>
		<div>Парола:<input type="password" name="password" value="<?= (isset($password)) ? $password : '';?>"/></div>
		<div>Прякор:<input type="text" name="nickname" value="<?= (isset($nickname)) ? $nickname : '';?>"/></div>
		<div>Име:<input type="text" name="firstname" value="<?= (isset($firstname)) ? $firstname : '';?>"/></div>
		<div>Фамилия:<input type="text" name="lastname" value="<?= (isset($lastname)) ? $lastname : '';?>"/></div>
		<div>e-mail:<input type="text" name="email" value="<?= (isset($email)) ? $email : '';?>"/></div>
		<div><input type="submit" name="submit" value="Регистрирай" /></div>
	</form>
	<form method="POST" action="destroy.php">
	    <input type="submit" value="Отказ">
	</form>
<?php 
include_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';

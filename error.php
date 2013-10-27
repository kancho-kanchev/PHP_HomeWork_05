<?php
$pageTitle = 'Грешка';
$message = '';
require_once 'includes'.DIRECTORY_SEPARATOR.'header.php';
if ($_GET && isset($_GET['message'])) {
	switch (trim($_GET['message'])) {
	    case "connectionerror":
	        echo "Грешка при свързване с базата данни!";
	        break;
	    case "databaseerror":
	        echo "Грешка в базата данни!";
	        break;
	    default:
			echo "Грешка - неопределена!";
	}
}
if ($_POST) {
	header('Location: index.php');
	exit;
}
?>
	<form method="POST" action="error.php">
	<input type="submit" name="submit" value="Върни в начална страница">
	</form>
<?php
include_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';


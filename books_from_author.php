<?php
$pageTitle = 'Книги от автор';
require_once 'includes'.DIRECTORY_SEPARATOR.'header.php';
require_once 'includes'.DIRECTORY_SEPARATOR.'connection.php';
if (isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == true) {
	echo 'Здравей '.$_SESSION['username']."\n";
	echo '<div>'."\n";
	echo '<a href="destroy.php">Изход</a>'."\n";
	echo '</div>'."\n";
}
else {
	if ($_POST && isset($_POST['username']) && isset($_POST['password'])) {
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$stmt = mysqli_prepare($connection, 'SELECT username, password FROM users WHERE username=? AND password=?');
		if (!$stmt) {
			//echo mysqli_error($connection);
			header('error.php?message=databaseerror');
			exit;
		}
		else {
			mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
			mysqli_stmt_execute($stmt);
			$rows = mysqli_stmt_result_metadata($stmt);
			while ($field = mysqli_fetch_field($rows)) {
				$fields[] = &$row[$field->name];
			}
			call_user_func_array(array($stmt, 'bind_result'), $fields);
			while (mysqli_stmt_fetch($stmt)) {
				//echo '<pre>'.print_r($row, true). '</pre>';
				$_SESSION['isLogged'] = true;
				$_SESSION['username'] = $row['username'];
				echo 'Здравей '.$row['username']."\n";
				echo '<div>'."\n";
				echo '<a href="destroy.php">Изход</a>'."\n";
				echo '</div>'."\n";
			}
			$message = 'Невалидно потребителско име или парола.';
		}
	}
	if (!(isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == true)) {
		?>
	<form method="POST" action="books_from_author.php">
		<div>Потребител:<input type="text" name="username" /></div>
		<div>Парола:<input type="password" name="password" /></div>
		<div>
			<a href="register.php">Регистрирай се</a>
			<input type="submit" name="submit" value="Влез" />
		</div>
	</form>
<?php
	}
}
if ($_GET && isset($_GET['author'])) {
	$author_id = trim($_GET['author']);
	$error = true;
	$author_id = mysqli_real_escape_string($connection, $author_id);
	$stmt = mysqli_prepare($connection, 'SELECT author_id FROM authors WHERE author_id =?');
	if (!$stmt) {
		echo mysqli_error($connection);
		exit;
	}
	else {
		mysqli_stmt_bind_param($stmt, 'i', $author_id);
		mysqli_stmt_execute($stmt);
		$rows = mysqli_stmt_result_metadata($stmt);
		while ($field = mysqli_fetch_field($rows)) {
			$fields[] = &$row[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $fields);
		while (mysqli_stmt_fetch($stmt)) {
			$error = false;
		}
	}
	if (!$error) {
		$query = 'SELECT b.book_id, b.book_title, a.author_name as author_name, a.author_id
			FROM `authors` as a
			LEFT JOIN books_authors as ba
			ON ba.author_id = a.author_id
			LEFT JOIN books as b
			ON b.book_id = ba.book_id
			LEFT JOIN books_authors as ba2
			ON ba2.book_id = b.book_id
			WHERE ba2.author_id = '.$author_id;
		$q = mysqli_query($connection, $query);
		$result = array();
		while($row = mysqli_fetch_assoc($q)) {
			$result[$row['book_id']]['book_title'] = $row['book_title'];
			$result[$row['book_id']]['authors'][$row['author_id']] = $row['author_name'];
		}
		//echo '<pre>'.print_r($result, true). '</pre>';
		echo '	<div>'."\n";
		echo '			<a href="index.php">Обратно към списъка с книгите</a>'."\n";
		echo '		</div>'."\n";
		echo '		<div>'."\n";
		echo '			<a href="new_book.php">Добави книга</a>'."\n";
		echo '			<a href="new_author.php">Добави автор</a>'."\n";
		echo '		</div>'."\n";
		echo '		<table border="1">'."\n";
		echo '			<tr><td>Книга</td><td>Автори</td></tr>'."\n";
		foreach ($result as $key_book => $value_book) {
			echo '			<tr><td><a href="book.php?book='.$key_book.'">'.$value_book['book_title'].'</a></td><td>';
			$authors = array();
			foreach ($value_book['authors'] as $key => $value_author) {
				$authors[] = '<a href="books_from_author.php?author='.$key.'">'.$value_author.'</a>';
			}
			echo implode(', ', $authors).'</td></tr>'."\n";
		}
		echo '		</table>'."\n";
	}
	else {
		echo '	<div>'."\n";
		echo '			<a href="index.php">Обратно към списъка с книгите</a>'."\n";
		echo '		</div>'."\n";
		echo '		<div>Невалиден индентификатор за автор!</div>'."\n";
	}
}
else {
	header('Location: index.php');
	exit;
}
/*
 * Времето за предаване на домашната изтича 
 */
require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';

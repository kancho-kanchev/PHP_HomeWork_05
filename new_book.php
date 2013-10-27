<?php
$pageTitle = 'Добавяне на книга';
require_once 'includes'.DIRECTORY_SEPARATOR.'header.php';
require_once 'includes'.DIRECTORY_SEPARATOR.'connection.php';
require_once 'includes'.DIRECTORY_SEPARATOR.'functions.php';
$query = 'SELECT * FROM authors';
$q = mysqli_query($connection, $query);
while($row = mysqli_fetch_assoc($q)) {
	$result[$row['author_id']]= $row['author_name'];
}
//echo '<pre>'.print_r($result, true). '</pre>';
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
	<form method="POST" action="new_book.php">
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
if ($_POST) {
	$error = false;
	if (isset($_POST['title'])) {
		$title = trim($_POST['title']);
		if (mb_strlen($title, 'UTF-8') < 3 || mb_strlen($title, 'UTF-8') > 250) {
			echo 'Заглавието трябва да е между 3 и 250 символа</br>'."\n";
			$error = true;
		}
		else {
			$title = mysqli_real_escape_string($connection, $title);
			$stmt = mysqli_prepare($connection, 'SELECT book_title FROM books WHERE book_title =?');
			if (!$stmt) {
				echo mysqli_error($connection);
				exit;
			}
			else {
				mysqli_stmt_bind_param($stmt, 's', $title);
				mysqli_stmt_execute($stmt);
				$rows = mysqli_stmt_result_metadata($stmt);
				while ($field = mysqli_fetch_field($rows)) {
					$fields[] = &$row[$field->name];
				}
				call_user_func_array(array($stmt, 'bind_result'), $fields);
				while (mysqli_stmt_fetch($stmt)) {
					echo '	<p>Заглавието вече съществува.</p>'."\n";
					$error = true;
				}
			}
		}
	}
	else {
		$error = true;
	}
	if (isset($_POST['authors'])) {
		$authors_id = $_POST['authors'];
		//$authors_id[] = '22';
		//echo '<pre>'.print_r($authors_id, true). '</pre>';
		foreach ($authors_id as $key => $author_id) {
			$authors_id[$key] = mysqli_real_escape_string($connection, $author_id);
			$stmt = mysqli_prepare($connection, 'SELECT `author_id` FROM `authors` WHERE `author_id` =?');
			if (!$stmt) {
				echo mysqli_error($connection);
				exit;
			}
			else {
				mysqli_stmt_bind_param($stmt, 'i', $author_id);
				mysqli_stmt_execute($stmt);
				$rows = mysqli_stmt_get_result($stmt);
				$row_cnt = mysqli_num_rows($rows);
				if ($row_cnt == 0) {
					$error = true;
				}
			}
		}
	}
	else {
		echo 'Изберете автор/и';
		$error = true;
	}
	if (!$error) {
		if (!($stmt = mysqli_prepare($connection, 'INSERT INTO books(book_title) VALUES (?)'))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';
			exit;
		}
		if (!$stmt->bind_param("s", $title)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';
			exit;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';
			exit;
		}
		echo '	<p>Заглавието е записано успешно</p>'."\n";
		$title_id = mysqli_insert_id($connection);
		foreach ($authors_id as $author_id) {
			if (!($stmt = mysqli_prepare($connection, 'INSERT INTO `books_authors` (book_id, author_id) VALUES (?, ?)'))) {
				delete_row ($title_id);
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';
				exit;
			}
			if (!$stmt->bind_param("ii", $title_id, $author_id)) {
				delete_row ($title_id);
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';
				exit;
			}
			if (!$stmt->execute()) {
				delete_row ($title_id);
				echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';
				exit;
			}
		}
		echo '	<p>Авторите са асоциирани успешно</p>'."\n";
		unset ($title);
		unset($authors_id);
	}
}
echo '	<div>'."\n";
echo '			<a href="index.php">Обратно към списъка с книгите</a>'."\n";
echo '		</div>'."\n";
?>
	<form method="POST" action="new_book.php">
		<div>Заглавие:<input type="text" name="title" value="<?= (isset($title)) ? $title : '';?>"/></div>
		<div>Автор/и: <select multiple name="authors[]">
				<?php 
					foreach ($result as $key=>$value) {
						echo'				<option value="'.$key.'"';
						if (isset($authors_id)) {
							foreach ($authors_id as $value_selected) {
								if ($value_selected==$key){
									echo 'selected';
								}
							}
						}
						echo '>'.$value.'</option>'."\n";
					}
				?>
			</select></div>
		<div><input type="submit" name="submit" value="Запис" /></div>
	</form>
<?php
require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';

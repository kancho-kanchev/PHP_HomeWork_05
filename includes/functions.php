<?php
/*
 * Целта на тази функция е при успешен запис на име на книга,
 * но неуспешен на автор, да изтрие последния запис в таблица books
 */
function delete_row($title_id) {
	if (!($stmt = mysqli_prepare($connection, 'DELETE FROM `books` WHERE book_id=?'))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';
		exit;
	}
	if (!$stmt->bind_param("i", $title_id)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';
		exit;
	}
	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		require_once 'includes'.DIRECTORY_SEPARATOR.'footer.php';
		exit;
	}
}
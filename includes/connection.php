<?php
/*
 * Разчитам, че в повечето случаи на localhost има постребител root  без парола.
 */
$connection = mysqli_connect('localhost', 'root', '', 'books');
if (!$connection) {
	//echo mysqli_error($connection);
	header('error.php?message=connectionerror');
	exit;
}
mysqli_set_charset($connection, 'UTF8');

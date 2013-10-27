<?php
/*
 * Коментара е заради енкодинга
 */
session_name('HomeWork05');
session_set_cookie_params(0,'/','',false,true);
session_start();
mb_internal_encoding('UTF-8');
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= $pageTitle; ?></title>
		<meta charset="UTF-8">
		<style>
			form{
				padding: 25px;
				border: 2px black solid;
				-moz-border-radius: 15px;
				-webkit-border-radius: 15px;
				-khtml-border-radius:15px;
				border-radius: 15px;
				-webkit-box-shadow:inset 1px 1px 2px #999;
				-moz-box-shadow:inset 1px 1px 2px #999;
				-khtml-box-shadow:inset 1px 1px 2px #999;
				box-shadow:inset 1px 1px 2px #999;
			}
		</style>
	</head>
	<body>
	
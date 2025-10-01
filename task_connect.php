<?php

function connect() {
	$hostname = 'localhost';
	$dbname ='task';

	$username = 'task';
	$password = 'task';

	$dsn = "mysql:host=$hostname;dbname=$dbname";
	

	
	try {
		return new PDO($dsn, $username, $password);
	} catch (Exception $e){
		echo ($e->getMessage());

	}
}

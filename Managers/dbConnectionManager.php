<?php
/**
* Class maintains the database connection string.
* @author University of Lincoln CS2 Group Project 02/2015
*/
class dbConnectionManager
{
	const strHost = 'localhost';
	const intPort = 5432;
	const strDBName = 'CS2';
	const strUser = 'stock';
	const strPassword = 'test';
	private $con,$db;

	protected function getConnection()
	{
		$con = 'pgsql:host='.self::strHost.' port='.self::intPort.' dbname='.self::strDBName.' user='.self::strUser.' password='.self::strPassword;
		return $con;
	}
}
?>

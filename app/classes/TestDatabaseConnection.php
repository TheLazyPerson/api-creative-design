<?php

	/**
	*  Trial Class to test database connection
	*/
	class TestDatabaseConnection extends Mapper {

		public function testSqlConnection(){
			$sql = "SELECT * FROM test";
			$result = mysql_query($sql);

	        if (!$result){
	            die("Database Query Failed: ". mysql_error());

	        }
	        $results = [];
			// $rows = mysql_fetch_array($result);
			while ( $row = mysql_fetch_array($result)) {
				
				$results[] = new TestEntity($row);
			} 

			return $results;
		}
	}
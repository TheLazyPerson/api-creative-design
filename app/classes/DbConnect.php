<?php

/**
 * Created by PhpStorm.
 * User: om
 * Date: 13/11/16
 * Time: 3:11 PM
 */
class DbConnect
{
    //variable to store database link
    private $db;
    protected $host;
    protected $user;
    protected $password;
    protected $database;

    //class constructor
    function __construct($host, $user, $password, $database)
    {   
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
    }

    //this method will connect to database
    function connect()
    {

        //connecting to mysql database
        $connection=mysql_connect($this->host,$this->user,$this->password);
        //Checking if any error occured while connecting
        if(!$connection){
            die("database connection failed:".mysql_error());
        }
        
        //Select a databse to use
        $this->db=mysql_select_db($this->database,$connection);
        if(!$this->db){
            die("database selection failed:".mysql_error());
        }
            
        

        //finally return the connection link
        return $this->db;
    }

    function __destruct()
    {

    }
}
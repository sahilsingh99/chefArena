<?php
  class database{
      // Properties
      private $dbhost = 'backend.test';
      private $dbuser = 'root';
      private $dbpass = '';
      private $dbname = 'testingphp';

      // Connect
      public function connect(){
          $mysql_connect_str = "mysql:host=".$this->dbhost.";dbname=".$this->dbname;
          $db_connection = new PDO($mysql_connect_str,$this->dbuser,$this->dbpass);
          $db_connection->setAttribute( PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
          return $db_connection;
      }

  }
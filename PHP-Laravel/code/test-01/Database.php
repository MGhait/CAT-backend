<?php
class Database {

    public $connection;

    public function __construct($config,$username = 'root',$password = 'Password')
    {
        $dsn ='mysql:' .http_build_query( $config,'',';');
        $this->connection= new PDO($dsn,$username,$password,[

        ]);
    }

    public function query($query, $params = [])
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        return $statement ;
    }
}
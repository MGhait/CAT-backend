# some notes

## connect to database
```php
// connect to our MySQL database

$dsn ="mysql:host=localhost;port=3306;dbname=laracast;user=root;password=*******;charset=utf8mb4";
/*here we define our database as mysql 
THEN our host (127.0.0.1) which is local host
THEN the port 3306 "on sql server I'd changed it to 3307"
THEN database name [dbname] our database to be connected
THEN username "optional to be here or as a parameter to PDO class constructor" 
THEN password if there's one mine is Password (^_*)
LAST THING we define charset (I think it's optional too ^_^) utf8 or utf8mb4
*/


$pdo = new PDO($dsn);
// instance of the class PDO stored in available pdo

$statement = $pdo->prepare("SELECT * FROM posts");
// doing our query as a result of variable statement, so we can execute with the following function
$statement->execute();

// $posts = $statement -> fetchAll();
// TO AVOID DUPLICATION we should use this instead
$posts = $statement -> fetchAll(PDO::FETCH_ASSOC);
// dd($posts); // our damp and die function 


// TO PRINT IT WE CAN LOOP FOR IT 
foreach ($posts as $post){
    echo "<li>". $post['NAME'] ."</li>";
} 
```
## creating a class for connection 
``` php
<?php 
// making connection to database in a cepareted class
class Database {

    public $connection;

    public function __construct(){

    $dsn ="mysql:host=localhost;port=3306;dbname=laracast;user=root;password=Password;charset=utf8mb4";
    $this->connection= new PDO($dsn);

    }

    public function query($query){
        $statement = $this->connection->prepare($query);
        
        $statement->execute();
        return $statement ;
    }
}

```

> we notice that we hardcoded the database class to make it more dynamic we can make it like that

```php

<?php
class Database {

    public $connection;

    public function __construct($config,$username = 'root',$password = 'Password'){
        /*
         *  it creates queries like that example.com?host=localhost&port=3306
         *  we change the arg separator to looks like our dsn (host=localhost;port=3306)
         */

//      $dsn ="mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
        $dsn ='mysql:' .http_build_query( $config,'',';');
        $this->connection= new PDO($dsn,$username,$password,[
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

    }

    public function query($query){
        $statement = $this->connection->prepare($query);
        
        $statement->execute();
        return $statement ;
    }
}
```

## index.php file after edit

```php

<?php

require 'function.php';
// require 'router.php';
require 'Database.php';

$config = require ('config.php');
$db = new Database($config['database']);

$id = $_GET['id'];
//dd( $_GET['id']);

//$query = "SELECT * FROM posts where id = {$id}";
// A big security mistake here if someone add a query in url like drop table users;

//we can make it like that 
$query = "SELECT * FROM posts where id = ?";
$posts = $db->query($query,[$id])-> fetchAll();

//or we can make it this way
//$query = "SELECT * FROM posts where id = :id";
//$posts = $db->query($query,['id' => $id])-> fetchAll();


//dd($posts);
//$posts = $db->query("SELECT * FROM posts")-> fetchAll();
// if we want one value we can use fetch() function



foreach ($posts as $post){
    echo "<li>". $post['NAME'] ."</li>";
} 
``` 
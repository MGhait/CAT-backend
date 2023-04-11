# some notes

## connect to database
```php
// connect to our MySQL database

$dsn ="mysql:host=localhost;port=3306;dbname=laracast;user=root;password=*******;charset=utf8mb4";
/*here we define our database as mysql 
THEN our host (127.0.0.1) which is local host
THEN the port 3306 "on sql server I'd changed it to 3307"
THEN databse name [dbname] our database to be connected
THEN username "optional to be here or as a prameter to PDO class constractor" 
THEN password if there's one mine is Password (^_*)
LAST THING we define charset (I think it's optioal too ^_^) utf8 or utf8mb4
*/


$pdo = new PDO($dsn);
// instanse of the class PDO stored in avariable pdo

$statement = $pdo->prepare("SELECT * FROM posts");
// doing our query as a result of variable statement so we can execute with the following function
$statement->execute();

// $posts = $statement -> fetchAll();
// TO AVOID DUPLICATION we shold use this instead
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
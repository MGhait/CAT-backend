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
## index.php file 
```php
<?php

require 'function.php';
require 'Database.php';
 require 'router.php';
 

```
## updating router.php file
```php
<?php
//we use pars_url() to get url separated form any possible queries
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];  // this give as path 'url only'

//adding note and notes controllers
$routes = [
'/' => 'controllers/index.php',
'/about' => 'controllers/about.php',
'/notes' => 'controllers/notes.php',
'/note' => 'controllers/note.php',
'/contact' => 'controllers/contact.php'
];

function abort($code=404){
http_response_code($code);
//require "views/{$code}.php";
require "views/404.php";
die();
}
```
> adding notes and not controllers and views

## note controller 
```php
<?php

$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'Note';

$note = $db -> query("SELECT * FROM notes WHERE user_id = :id",['id'=>$_GET["id"]])->fetch(PDO::FETCH_ASSOC);
//dd($note);
require "views/note.view.php";

```

## notes controller

```php
<?php

$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'My Notes';

$notes = $db -> query("SELECT * FROM notes WHERE user_id = 1")->fetchAll(PDO::FETCH_ASSOC);

require "views/notes.view.php";
```


## notes.view

```php
<?php require ('partials/head.php');?>
<?php require ('partials/nav.php');?>
<?php require ('partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <?php foreach ($notes as $note) : ?>
        <li>
           <a href="/note?id=<?= $note['id']?>" class="text-blue-500 hover:underline">
               <?= $note['body']?>
           </a>
        </li>
        <?php endforeach; ?>
    </div>
</main>
<?php require ('partials/footer.php')?>

```

## note.view

```php
<?php require ('partials/head.php');?>
<?php require ('partials/nav.php');?>
<?php require ('partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p class="mb-6">
            <a href="/notes" class="text-blue-500 underline ">
                go back ..
            </a>
        </p>
        <p>
            <?= $note["body"] ?>
        </p>
    </div>
</main>
<?php require ('partials/footer.php')?>
```
> note that the output of `$note` variable is bool if there's no something
> from database it will return false so, 
> we have to make statement to avoid that by updating note.php

## note.php
```php
<?php

$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'Note';

//to be sure that the user have this note

//$note = $db -> query("SELECT * FROM notes WHERE user_id = :user and id = :id",[
//    'user'=> 1,
//    'id'=>$_GET["id"]
//])->fetch(PDO::FETCH_ASSOC);
$note = $db -> query("SELECT * FROM notes WHERE id = :id",[
    'id'=>$_GET["id"]
])->fetch(PDO::FETCH_ASSOC);
//dd($note);

if (! $note) {
    abort();
}

if ($note['user_id'] != 1)
{
    abort(403);
}
require "views/note.view.php";

```

> creating a new response class to make our response
> more readable and updating my note controller

```php
<?php

$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'Note';

$note = $db -> query("SELECT * FROM notes WHERE id = :id",[
    'id'=>$_GET["id"]
])->fetch(PDO::FETCH_ASSOC);
//dd($note['user_id']);

if (! $note) {
    abort();
}
$currentUserId = 1;
if ($note['user_id'] != $currentUserId)
{
    abort(Response::FORBIDDEN);
}
require "views/note.view.php";

```
```php
<?php

class  Response {
    const NOT_FOUND = 404;
    const  FORBIDDEN = 403;

}

```

> adding function authorize in function.php and replacing
> the fitch and fitchAll function with get and find in database calss
 
## Database.php
```php
<?php
class Database {

    public $connection;
    public $statement;

    public function __construct($config,$username = 'root',$password = 'Password')
    {
        $dsn ='mysql:' .http_build_query( $config,'',';');
        $this->connection= new PDO($dsn,$username,$password,[
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

//    public function query($query, $params = [])
//    {
//        $statement = $this->connection->prepare($query);
//        $statement->execute($params);
//        return $statement ;
//    }
    public function query($query, $params = [])
    {
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);
        return $this ;
    }

    public function get()
    {
        return $this->statement-> fetchAll();
    }

    public function find()
    {
        return $this->statement->fetch();
    }

    public function findOrFail()
    {
        $res = $this->find();
        if (! $res)
        {
            abort();
        }
        return $res;
    }

}
```

## note.php
```php
<?php
$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'Note';
$currentUserId = 1;

$note = $db -> query("SELECT * FROM notes WHERE id = :id",[
    'id'=>$_GET["id"]
])->findOrFail();
//dd($note);

//if ($note['user_id'] != $currentUserId)
//{
//    abort(Response::FORBIDDEN);
//}

authorize($note['user_id'] ==$currentUserId);
require "views/note.view.php";

```
## function.php
```php
<?php
//dump and die function -to check any point of our code-
function dd($value)
{
echo '<pre>';
    var_dump($value);
    echo '</pre>';
die();
}
// to get the boolean value of the current page
// we use it to make short-hand-if more readable
function urlIs($value){
return $_SERVER['REQUEST_URI'] == $value;
}

//short hand if
//=$_SERVER['REQUEST_URI'] =='/'?"bg-gray-900 text-white":"text-gray-300"

function authorize($condition, $status = Response::FORBIDDEN)
{
    if (! $condition) {
        abort($status);
    }
}
```
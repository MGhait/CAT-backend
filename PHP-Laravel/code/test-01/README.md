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
// require 'Router.php';
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
 require 'Router.php';
 

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
'/notes' => 'controllers/index.php',
'/note' => 'controllers/show.php',
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
require "views/show.view.php";

```

## notes controller

```php
<?php

$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'My Notes';

$notes = $db -> query("SELECT * FROM notes WHERE user_id = 1")->fetchAll(PDO::FETCH_ASSOC);

require "views/index.view.php";
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
require "views/show.view.php";

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
require "views/show.view.php";

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
require "views/show.view.php";

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
> we created a validator class to validate the note before 
> it goes to database, updated routes.php by making notes-note-note_create into note dir
> and rename it to index-show-create also update the
> bath of or files

## validator.php 

```php
<?php

class  Validator
{
    public static function string($value, $min =1 ,$max= INF)
    {
        $value = trim($value);
        if (strlen($value) >= $min && strlen($value) <= $max)
        {
            return strlen($value);
        }

    }
}

```
## notes/index.view.php
```php
<?php require ('views/partials/head.php')?>
<?php require ('views/partials/nav.php');?>
<?php require ('views/partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <ul>
            <?php foreach ($notes as $note) : ?>
                <li>
                    <a href="/note?id=<?= $note['id']?>" class="text-blue-500 hover:underline">
                        <?= htmlspecialchars($note['body'])?>
                    </a>
                </li>
            <?php endforeach; ?>

            <p class="mt-10">
                <a href="/note/create" class="text-blue-600 hover:underline">
                    Create Note
                </a>
            </p>
        </ul>
    </div>
</main>
<?php require('views/partials/footer.php') ?>
```
## notes/create.view.php
```php
<?php require ('views/partials/head.php')?>
<?php require ('views/partials/nav.php');?>
<?php require ('views/partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">

<!--        <form method="post">-->
<!--            <label for="body">Description</label>-->
<!--            <div>-->
<!--                <textarea id="body" name="body"></textarea>-->
<!--            </div>-->
<!---->
<!--            <p class="mt-10">-->
<!--                <button type="submit">-->
<!--                    Create-->
<!--                </button>-->
<!--            </p>-->
<!--        </form>-->





<!--        0000000000000000000000000000000000000000000000000000000000000000000000000000000000000       -->
       <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
           <div>
               <div class="md:grid md:grid-cols-3 md:gap-6">
                   <div class="mt-5 md:col-span-2 md:mt-0">
                       <form action="#" method="post">
                           <div class="space-y-12">
                               <div class="border-b border-gray-900/10 pb-12">
                                   <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                       <div class="col-span-full">
                                           <label for="about" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                                           <div class="mt-2">
                                               <textarea
                                                   id="body"
                                                   name="body"
                                                   rows="3"
                                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                   placeholder="Here's an idea for a note.."

                                               ><?= isset($_POST['body'])? $_POST['body'] : ''?></textarea>
                                               <?php if(isset($errors['body'])) : ?>
                                                <p class="text-red-500 text-xs mt-2">
                                                    <?= $errors['body']?>
                                                </p>
                                               <?php endif; ?>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <div class="mt-6 flex items-center justify-end gap-x-6">
                               <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                               <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                           </div>
                       </form>


                   </div>
               </div>
           </div>
       </div>
    </div>
</main>
<?php require('views/partials/footer.php') ?>
```
## notes/show.view.php
```php
<?php require ('views/partials/head.php')?>
<?php require ('views/partials/nav.php');?>
<?php require ('views/partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p class="mb-6">
            <a href="/notes" class="text-blue-500 underline ">
                go back ..
            </a>
        </p>
        <p>
            <?= htmlspecialchars($note["body"]) ?>
        </p>
    </div>
</main>
<?php require('views/partials/footer.php') ?>
```
## controllers/notes/index.php
```php
<?php
$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'My Notes';

$notes = $db -> query("SELECT * FROM notes WHERE user_id = 1")->get();

require "views/notes/index.view.php";

```
## controllers/notes/show.php
```php
<?php
$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'Note';
$currentUserId = 1;

$note = $db -> query("SELECT * FROM notes WHERE id = :id",[
    'id'=>$_GET["id"]
])->findOrFail();

authorize($note['user_id'] ==$currentUserId);
require "views/notes/show.view.php";

```
## controllers/notes/create.php
```php
<?php
require 'Validator.php';

$config = require ('config.php');
$db = new Database($config['database']);
$heading = 'Create Note';

if($_SERVER['REQUEST_METHOD']== 'POST')
{
    $errors =[];
    $invalidNum =150;

    if(! Validator::string($_POST['body'],1,300)){
        $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
    }

    if(empty($errors)){
        $db->query('INSERT INTO notes(body, user_id) VALUE(:body, :user_id)',[
            'body'=> $_POST['body'],
            'user_id'=> 1
        ]);
    }
}
require 'views/notes/create.view.php';
```
> using name spacing in our project

## Database.php
```php
<?php
namespace core;
use PDO;
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
## function.php
```php
<?php

use core\Response;

function dd($value)
{
echo '<pre>';
    var_dump($value);
    echo '</pre>';
die();
}

function urlIs($value){
return $_SERVER['REQUEST_URI'] == $value;
}
function authorize($condition, $status = Response::FORBIDDEN)
{
    if (! $condition) {
        abort($status);
    }
}

function base_bath($path)
{
    return BASE_PATH . $path;
}
function view($path, $attributes = [])
{
    extract($attributes);
    require base_bath('views/'.$path);
}
```
## response.php
```php
<?php
namespace core;
class  Response {
    const NOT_FOUND = 404;
    const  FORBIDDEN = 403;

}

```

## router.php
```php
<?php
$routes = require base_bath('routes.php');

function abort($code=404){
    http_response_code($code);
    require base_bath("views/{$code}.php");
    die();
}
function routeToController($uri,$routes){
    if (array_key_exists($uri,$routes)){
        require base_bath($routes[$uri]);
    }
    else {
        abort();
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
routeToController($uri,$routes);

```

## Validator.php
```php
<?php
namespace core;
class  Validator
{
    public static function string($value, $min =1 ,$max= INF)
    {
        $value = trim($value);
        if (strlen($value) >= $min && strlen($value) <= $max)
        {
            return strlen($value);
        }

    }
}

```


## public/index.php
```php
<?php

const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'core/function.php';



spl_autoload_register(function ($class) {
    $class= str_replace('\\',DIRECTORY_SEPARATOR,$class);
   require base_bath("{$class}.php");
});
require base_bath('core/Router.php');

```


## controllers/notes/create.php
```php
<?php
use core\Database;
use core\Validator;

require base_bath('core/Validator.php');

$config = require base_bath('config.php');
$db = new Database($config['database']);
$errors =[];
if($_SERVER['REQUEST_METHOD']== 'POST')
{

    $invalidNum =150;

    if(! Validator::string($_POST['body'],1,300)){
        $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
    }

    if(empty($errors)){
        $db->query('INSERT INTO notes(body, user_id) VALUE(:body, :user_id)',[
            'body'=> $_POST['body'],
            'user_id'=> 1
        ]);
    }
}
view("notes/create.view.php" ,[
    'heading'=> 'Create Note',
    'errors'=>$errors
]);
```
## controllers/notes/index.php
```php
<?php
use core\Database;
$config = require base_bath('config.php');
$db = new Database($config['database']);

$notes = $db -> query("SELECT * FROM notes WHERE user_id = 1")->get();

view("notes/index.view.php" ,[
    'heading'=> 'My Notes',
    'notes'=>$notes
]);


```
## controllers/notes/show.php
```php
<?php
use core\Database;

$config = require base_bath('config.php');
$db = new Database($config['database']);

$currentUserId = 1;

$note = $db -> query("SELECT * FROM notes WHERE id = :id",[
    'id'=>$_GET["id"]
])->findOrFail();

authorize($note['user_id'] ==$currentUserId);

view("notes/show.view.php" ,[
    'heading'=> 'Note',
    'note'=>$note
]);

```
> edit our routes and change router and edite crate note and adding destroy and store 
> here is code changes for all files and new ones
 
## notes/create.php
```php
<?php
// commented files usually are deleted
//use core\Database;
//use core\Validator;

//require base_bath('core/Validator.php');

//$config = require base_bath('config.php');
//$db = new Database($config['database']);
//$errors =[];
//if($_SERVER['REQUEST_METHOD']== 'POST')
//{
//
//    $invalidNum =150;
//
//    if(! Validator::string($_POST['body'],1,300)){
//        $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
//    }
//
//    if(empty($errors)){
//        $db->query('INSERT INTO notes(body, user_id) VALUE(:body, :user_id)',[
//            'body'=> $_POST['body'],
//            'user_id'=> 1
//        ]);
//    }
//}
view("notes/create.view.php" ,[
    'heading'=> 'Create Note',
    'errors'=> []
]);
```

## notes/destroy.php
```php
<?php
use core\Database;

$config = require base_bath('config.php');
$db = new Database($config['database']);

$currentUserId = 1 ;

$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_POST["id"]
])->findOrFail();

authorize($note['user_id'] == $currentUserId);

$db->query('DELETE FROM notes WHERE id = :id',[
'id'=>$_GET['id'],
]);

header('location: /notes');
exit();


```

## notes/show.php
```php
<?php
use core\Database;

$config = require base_bath('config.php');
$db = new Database($config['database']);

$currentUserId = 1 ;
//dd($_POST);
//dd($_SERVER['REQUEST_METHOD']);
//if($_SERVER['REQUEST_METHOD'] == 'POST') {
//    $note = $db->query("SELECT * FROM notes WHERE id = :id", [
//        'id' => $_GET["id"]
//    ])->findOrFail();
//
//    authorize($note['user_id'] == $currentUserId);
//
//    $db->query('DELETE FROM notes WHERE id = :id',[
//    'id'=>$_GET['id'],
//    ]);
//
//    header('location: /notes');
//    exit();
//
//
//}else{
//    $note = $db->query("SELECT * FROM notes WHERE id = :id", [
//        'id' => $_GET["id"]
//    ])->findOrFail();
//
//    authorize($note['user_id'] == $currentUserId);
//
//    view("notes/show.view.php", [
//        'heading' => 'Note',
//        'note' => $note
//    ]);
//}


$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_GET["id"]
])->findOrFail();

authorize($note['user_id'] == $currentUserId);

view("notes/show.view.php", [
    'heading' => 'Note',
    'note' => $note
]);

```
## notes/store.php

>this file to store our note into DB

```php
<?php
use core\Database;
use core\Validator;

$config = require base_bath('config.php');
$db = new Database($config['database']);

$errors= [];

if($_SERVER['REQUEST_METHOD']== 'POST')
{
    $invalidNum =250;
    if(! Validator::string($_POST['body'],1,$invalidNum)){
        $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
    }
    if (! empty($errors)) {
        // validation issues
         return view("notes/create.view.php" ,[
            'heading'=> 'Create Note',
            'errors'=>$errors
        ]);
    }
        $db->query('INSERT INTO notes(body, user_id) VALUE(:body, :user_id)',[
            'body'=> $_POST['body'],
            'user_id'=> 1
        ]);
        header('location: /notes');
        die();
}
```

## routes.php
```php
<?php

//return  [
//    '/' => 'controllers/index.php',

//    '/about' => 'controllers/about.php',

//    '/notes' => 'controllers/notes/index.php',

//    '/note' => 'controllers/notes/show.php',

//    '/note/create' => 'controllers/notes/create.php',

//    '/contact' => 'controllers/contact.php',
//];


$router->get('/', 'controllers/index.php');
$router->get('/about','controllers/about.php');
$router->get('/contact','controllers/contact.php');

$router->get('/notes','controllers/notes/index.php');
$router->get('/note','controllers/notes/show.php');
$router->delete('/note','controllers/notes/destroy.php');

$router->get('/note/create','controllers/notes/create.php');
$router->post('/notes','controllers/notes/store.php');

```

## Router.php class (:router.php file)
```php
<?php

namespace core;

class  Router {
    protected $routes = [];

    public function add($method, $uri,$controller) {
        $this->routes[]= [
            'uri'=> $uri,
            'controller' =>$controller,
            'method' => $method
        ];
    }

    public function get($uri,$controller){
        $this->add('GET', $uri, $controller);
    }

    public function post($uri,$controller){
        $this->add('POST', $uri, $controller);
    }

    public function delete($uri,$controller){
        $this->add('DELETE', $uri, $controller);
    }

    public function patch($uri,$controller){
        $this->add('BATCH', $uri, $controller);
    }

    public function put($uri,$controller){
        $this->add('PUT', $uri, $controller);
    }
    public function route($uri,$method){
        foreach ($this->routes as $route) {
            if ($route['uri'] == $uri && $route['method'] == strtoupper($method)) {
                return require base_bath($route['controller']);
            }
        }
        $this->abort();
    }
    public function abort($code=404){
    http_response_code($code);
    require base_bath("views/{$code}.php");
    die();
}


}

//function abort($code=404){
//    http_response_code($code);
//    require base_bath("views/{$code}.php");
//    die();
//}
//function routeToController($uri,$routes){
//    if (array_key_exists($uri,$routes)){
//        require base_bath($routes[$uri]);
//    }
//    else {
//        abort();
//    }
//}
//


```

## some edit on public/index.php
```php
<?php
// base_bath function on line 12 replaced with code below it after edit the router.php
//to our class Router.php and take an instance of this class 
const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'core/function.php';



spl_autoload_register(function ($class) {
    $class= str_replace('\\',DIRECTORY_SEPARATOR,$class);
   require base_bath("{$class}.php");
});
//require base_bath('core/Router.php');

$router=new core\Router();

$routes = require base_bath('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
//routeToController($uri,$routes);


$method = isset($_POST['_method']) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];

$router->route($uri,$method);
//dd($router);

```
> create new classes App and Container and update db in controllers/notes/

## App.php class
```php
<?php

namespace core;

class App
{

    protected static $container;
    public static function setContainer($container)
    {
        static::$container = $container;
    }

    public static function container()
    {
        return  static::$container;
    }

    //to make direct access from app class to bind function in Container class
    public static function bind($key, $resolver)
    {
        static::container()->bind($key, $resolver);
    }

    //to make direct access from app class to resolve function in Container class
    public static function resolve($key)
    {
        return static::container()->resolve($key);
    }
}
```

## Container.php class
```php
<?php

namespace core;

use Exception;

class Container
{
    protected $bindings = [];

    public function bind($key, $resolver)
    {

        $this->bindings[$key] = $resolver;
    }


    /**
     * @throws Exception
     */
    public function resolve($key)
    {
        if (! array_key_exists($key, $this->bindings))
        {
            throw new Exception("No matching bind found for $key");
        }
            $resolver = $this->bindings[$key];
            return call_user_func($resolver);
    }

    //end of class
}
```

## bootstrap.php file
```php
<?php
use core\App;
use core\Container;
use core\Database;

$container = new Container();

$container->bind('core\Database',function (){
    $config = require base_bath('config.php');

    return new Database($config['database']);

});

//$db= $container->resolve('core\Database');
//dd($db);

App::setContainer($container);

```
## notes/destroy.php
```php
<?php
use core\Database;
use core\App;
//$config = require base_bath('config.php');
//$db = new Database($config['database']);

//$db = App::container()->resolve('core\Database');
//make it directly from app class
$db = App::resolve(Database::class);

//dd($db);
$currentUserId = 1 ;

$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_POST["id"]
])->findOrFail();

authorize($note['user_id'] == $currentUserId);

$db->query('DELETE FROM notes WHERE id = :id',[
'id'=>$_GET['id'],
]);

header('location: /notes');
exit();


```

## notes/index.php
```php
<?php
use core\Database;
use core\App;
//$config = require base_bath('config.php');
//$db = new Database($config['database']);
$db = App::resolve(Database::class);

$notes = $db -> query("SELECT * FROM notes WHERE user_id = 1")->get();

view("notes/index.view.php" ,[
    'heading'=> 'My Notes',
    'notes'=>$notes
]);


```

## notes/show.php
```php

<?php
use core\Database;
use core\App;
//$config = require base_bath('config.php');
//$db = new Database($config['database']);
$db = App::resolve(Database::class);

$currentUserId = 1 ;
$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_GET["id"]
])->findOrFail();

authorize($note['user_id'] == $currentUserId);

view("notes/show.view.php", [
    'heading' => 'Note',
    'note' => $note
]);

```

## notes/store.php
```php
<?php
use core\Database;
use core\Validator;
use core\App;
//$config = require base_bath('config.php');
//$db = new Database($config['database']);
$db = App::resolve(Database::class);

$errors= [];

if($_SERVER['REQUEST_METHOD']== 'POST')
{
    $invalidNum =250;
    if(! Validator::string($_POST['body'],1,$invalidNum)){
        $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
    }
    if (! empty($errors)) {
        // validation issues
         return view("notes/create.view.php" ,[
            'heading'=> 'Create Note',
            'errors'=>$errors
        ]);
    }
        $db->query('INSERT INTO notes(body, user_id) VALUE(:body, :user_id)',[
            'body'=> $_POST['body'],
            'user_id'=> 1
        ]);
        header('location: /notes');
        die();
}
```
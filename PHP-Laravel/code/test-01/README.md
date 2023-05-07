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

//    '/note/create' => 'controllers/notes/create.view.php',

//    '/contact' => 'controllers/contact.php',
//];


$router->get('/', 'controllers/index.php');
$router->get('/about','controllers/about.php');
$router->get('/contact','controllers/contact.php');

$router->get('/notes','controllers/notes/index.php');
$router->get('/note','controllers/notes/show.php');
$router->delete('/note','controllers/notes/destroy.php');

$router->get('/note/create','controllers/notes/create.view.php');
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

> adding edit option to finish our CRUD project here is the last update of files and 
> new files created edit.php - update.php and update in some views


## notes/edit.php
```php
<?php
use core\Database;
use core\App;
$db = App::resolve(Database::class);

$currentUserId = 1 ;
$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_GET["id"]
])->findOrFail();

authorize($note['user_id'] == $currentUserId);
view("notes/edit.view.php" ,[
    'heading'=> 'Create Note',
    'errors'=> [],
    'note'=>$note
]);
```

## notes/update.php
```php

<?php

// find the corresponding note

use core\Database;
use core\App;
use core\Validator;

$db = App::resolve(Database::class);

$currentUserId = 1 ;
$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_POST["id"]
])->findOrFail();

// authorize that the current user can dit the note
authorize($note['user_id'] == $currentUserId);

// validate the form
$errors= [];
$invalidNum =250;
if(! Validator::string($_POST['body'],1,$invalidNum)){
    $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
}

// if no validation errors, update the record in the notes database table.
if (count($errors)) {
    return view('notes/edit.view.php',[
       'heading'=> 'Edit Note',
       'errors' => $errors,
       'note' => $note
    ]);
}

$db->query('update notes set body = :body where  id = :id',[
    'id' => $_POST['id'],
    'body' => $_POST['body']
]);

// redirect the user

header('location: /notes');
die();

```

## views/notes/edit.view.php
```php

<?php require base_bath('views/partials/head.php')?>
<?php require base_bath('views/partials/nav.php');?>
<?php require base_bath('views/partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">

       <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
           <div>
               <div class="md:grid md:grid-cols-3 md:gap-6">
                   <div class="mt-5 md:col-span-2 md:mt-0">
                       <form method="POST" action="/note">
                           <input type="hidden" name="_method" value="PATCH">
                           <input type="hidden" name="id" value="<?= $note['id'] ?>">
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

                                               ><?=  $note['body'] ?></textarea>
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
                           <div class="mt-6 flex items-center justify-end gap-x-6 gap-x-4 justify-end">

                               <a
                                       href="/notes"
                                       class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                               >
                                   Cancel
                               </a>
                               <button
                                       type="submit"
                                       class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                               >
                                   Update
                               </button>
                           </div>
                       </form>


                   </div>
               </div>
           </div>
       </div>
    </div>
</main>
<?php require base_bath('views/partials/footer.php') ?>


```

## views/notes/show.view.php
```php
<?php require base_bath('views/partials/head.php')?>
<?php require base_bath('views/partials/nav.php');?>
<?php require base_bath('views/partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p class="mb-6">
            <a href="/notes" class="text-blue-500 underline ">
                go back ..
            </a>
        </p>
        <p class="w-full md:w-auto">
            <?= htmlspecialchars($note["body"]) ?>
        </p>

        <footer class="mt-6">

            <a href="/note/edit?id=<?= $note['id'] ?>"
               class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
                Edit
            </a>

        </footer>


    </div>
</main>
<?php require base_bath('views/partials/footer.php') ?>
```

## last update for routes.php file
```php
<?php

$router->get('/', 'controllers/index.php');
$router->get('/about','controllers/about.php');
$router->get('/contact','controllers/contact.php');

$router->get('/notes','controllers/notes/index.php');
$router->get('/note','controllers/notes/show.php');
$router->delete('/note','controllers/notes/destroy.php');

$router->get('/note/edit','controllers/notes/edit.php');
$router->patch('/note','controllers/notes/update.php');

$router->get('/note/create','controllers/notes/create.view.php');
$router->post('/notes','controllers/notes/store.php');

```

## At This Point It Works But We Will Do Some Refactor 
> here is the changes in files and classes created 

> Create (Middleware) directory inside core dir and its includes [ Auth.php - Gust.php - Middleware.php ] classes
> Create Authenticator.php class inside core dir  
> update function and Router and validator classes  
> Create (Http) directory and move controllers dir inside it
> & create new dir (Forms) inside it includes LoginForm.php class  
> Create session directory in controllers and create it's view  
> update index.view.php and update routes.php

## core/Middleware/Auth.php
```php
<?php
namespace core\Middleware;
class Auth
{
    public function handle()
    {
        if (! $_SESSION['user'] ?? false) {
            header('location: /');
            exit();
        }
    }
}
```
## core/Middleware/Guest.php 
```php
<?php
namespace core\Middleware;
class Guest
{
    public function handle()
    {
        if ($_SESSION['user'] ?? false) {
            header('location: /');
            exit();
        }

    }
}
```
## core/Middleware/Middleware.php

```php
<?php

namespace core\Middleware;

class Middleware
{
    public const MAP = [
        'guest' => Guest::class,
        'auth' => Auth::class
    ];

    public static function resolve($key) {

        if (! $key) {
            return;
        }

        $middleware = static::MAP[$key] ?? false;

        if(!$middleware) {
            throw new \Exception("No matching middleware found for key {$key} .");
        }
        (new $middleware)->handle();

    }

}
```
## core/Authenticator.php

```php
<?php

namespace core;

class Authenticator
{
    public function attempt($email, $password)
    {

        $user = App::resolve(Database::class)
            ->query('select * from users where email = :email',[
            'email' => $email
        ])->find();

        if ($user) {
            if (password_verify($password,$user['password'])) {
                $this->login([
                    'email' => $email
                ]);

                return true;
            }
        }
        return false;


    }

    public function login($user) {
        $_SESSION['user'] = [
            'email' => $user['email']
        ];

        session_regenerate_id(true);
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();

        $parms = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $parms['path'], $parms['domain'], $parms['secure'], $parms['httponly']);
    }

}
```
## core/function.php
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

function abort($code = 404)
{
    http_response_code($code);
    require base_bath("views/{$code}.php");
    die();

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

function redirect($path) {
    header("location:{$path}");
    exit();
}
```

## core/Router.php
```php
<?php

namespace core;

use core\Middleware\Auth;
use core\Middleware\Guest;
use core\Middleware\Middleware;

class  Router {
    protected $routes = [];

    public function add($method, $uri,$controller) {
        $this->routes[]= [
            'uri'=> $uri,
            'controller' =>$controller,
            'method' => $method,
            'middleware' => null
        ];
        return $this;
    }

    public function get($uri,$controller){
        return $this->add('GET', $uri, $controller);
    }

    public function post($uri,$controller){
        return $this->add('POST', $uri, $controller);
    }

    public function delete($uri,$controller){
       return  $this->add('DELETE', $uri, $controller);
    }

    public function patch($uri,$controller){
       return  $this->add('BATCH', $uri, $controller);
    }

    public function put($uri,$controller){
       return  $this->add('PUT', $uri, $controller);
    }

    public function only($key) {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;
        return $this;
    }
    public function route($uri,$method){
        foreach ($this->routes as $route) {
            if ($route['uri'] == $uri && $route['method'] == strtoupper($method)) {
                // apply the middleware

                Middleware::resolve($route['middleware']);
//                if ($route['middleware']) {
//
//                }
//                if ($route['middleware'] == 'guest') {
//                    (new Guest)->handle();
//                }
//
//                if ($route['middleware'] == 'auth') {
//                    (new Auth)->handle();
//                }

                return require base_bath('Http/controllers/' . $route['controller']);
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

```

## validator.php
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

    public static function email($value)
    {
        return filter_var($value,FILTER_VALIDATE_EMAIL);
    }
}

```
## Http/controllers/notes/create.php
```php
<?php

view("notes/create.view.php" ,[
    'heading'=> 'Create Note',
    'errors'=> []
]);
```

## Http/controllers/notes/destroy.php
```php
<?php
use core\Database;
use core\App;
$db = App::resolve(Database::class);

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


## Http/controllers/notes/edit.php
```php

<?php
use core\Database;
use core\App;
$db = App::resolve(Database::class);

$currentUserId = 1 ;
$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_GET["id"]
])->findOrFail();

authorize($note['user_id'] == $currentUserId);
view("notes/edit.view.php" ,[
    'heading'=> 'Create Note',
    'errors'=> [],
    'note'=>$note
]);
```


## Http/controllers/notes/index.php
```php
<?php
use core\Database;
use core\App;
$db = App::resolve(Database::class);

$notes = $db -> query("SELECT * FROM notes WHERE user_id = 1")->get();

view("notes/index.view.php" ,[
    'heading'=> 'My Notes',
    'notes'=>$notes
]);


```


## Http/controllers/notes/show.php
```php
<?php
use core\Database;
use core\App;
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


## Http/controllers/notes/store.php
```php
<?php
use core\Database;
use core\Validator;
use core\App;

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


## Http/controllers/notes/update.php
```php
<?php

// find the corresponding note

use core\Database;
use core\App;
use core\Validator;

$db = App::resolve(Database::class);

$currentUserId = 1 ;
$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_POST["id"]
])->findOrFail();

// authorize that the current user can dit the note
authorize($note['user_id'] == $currentUserId);

// validate the form
$errors= [];
$invalidNum =250;
if(! Validator::string($_POST['body'],1,$invalidNum)){
    $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
}

// if no validation errors, update the record in the notes database table.
if (count($errors)) {
    return view('notes/edit.view.php',[
       'heading'=> 'Edit Note',
       'errors' => $errors,
       'note' => $note
    ]);
}

$db->query('update notes set body = :body where  id = :id',[
    'id' => $_POST['id'],
    'body' => $_POST['body']
]);

// redirect the user

header('location: /notes');
die();
```
## Http/controllers/registration/create.php
```php
<?php

view('registration/create.view.php');

```

## Http/controllers/registration/store.php
```php
<?php

use core\App;
use core\Database;
use core\Validator;
//dd($_POST);

$email = $_POST['email'];
$password = $_POST['password'];

// validate the form inputs
$errors = [];
if (! Validator::email($email)) {
    $errors['email'] = 'Please provide valid email address';
}
if (! Validator::string($password , 7, 255)) {
    $errors['password'] = 'Please provide a password at least 7 characters';
}
//dd($errors);
if (! empty($errors)) {
     return view('registration/create.view.php',[
        'errors' => $errors
     ]);
//    dd('errors');
}

// check if the account already exists
$db = App::resolve(Database::class);
$user = $db->query('SELECT * FROM users WHERE email = :email',[
    'email' => $email
])->find();
//dd($user);
if ($user) {
    // if yes, redirect to a login page.
    header('location: /');
    exit();
//    dd($_SESSION);
} else {
    // if not, save one to the database
    $db->query('INSERT INTO users(email,password) VALUE (:email, :password)',[
        'email' => $email,
        'password' => password_hash($password,PASSWORD_BCRYPT)
    ]);
    //mark that the user has logged in
   login([
       'email' => $email
   ]);
//    dd($_SESSION);
    header('location: /');
    exit();
}



```

## Http/controllers/session/create.php
```php
<?php
view('session/create.view.php');
```

## Http/controllers/session/destroy.php
```php
<?php

// log the user out
logout();
header('location: /');
exit();

```

## Http/controllers/session/store.php
```php
<?php

use core\Authenticator;
use Http\Forms\LoginForm;

// login the user if the credentials match.
$email = $_POST['email'];
$password = $_POST['password'];

$form = new LoginForm();
if ($form->validate($email,$password)) {
    if ((new Authenticator)->attempt($email,$password))
    {
        redirect('/');
    //    header('location: /');
    //    exit();
    }
$form->error('email','No matching account found for that email address and password');
}

return view('session/create.view.php',[
    'errors' => $form->errors()
]);

//$errors = [];
//if (! Validator::email($email)) {
//    $errors['email'] = 'Please provide valid email address';
//}
//if (! Validator::string($password)) {
//    $errors['password'] = 'Please provide a valid password.';
//}
//

// match the credentials

//$user = $db->query('select * from users where email = :email',[
//    'email' => $email
//])->find();
//
//if ($user) {
//    if (password_verify($password,$user['password'])) {
//        login([
//            'email' => $email
//        ]);
//
//        header('location: /');
//        exit();
//    }
//}
//return view('session/create.view.php',[
//    'errors' => [
//        'email' => 'No matching account found for that email address and password '
//    ]
//]);


```

## Http/controllers/about.php
```php
<?php

view("about.view.php" ,[
    'heading'=> 'About Us'
]);


```

## Http/controllers/contact.php
```php
<?php

view("contact.view.php" ,[
    'heading'=> 'Contact Us'
]);


```

## Http/controllers/index.php
```php
<?php

 view("index.view.php" ,[
     'heading'=> 'Home'
 ]);

```

## Http/Forms/LoginForm.php
```php
<?php

namespace Http\Forms;

use core\Validator;

class LoginForm
{
    protected $errors = [];
    public function validate($email,$password)
    {
        if (! Validator::email($email)) {
            $this->errors['email'] = 'Please provide valid email address';
        }
        if (! Validator::string($password)) {
            $this->errors['password'] = 'Please provide a valid password.';
        }

        return empty($this->errors);
    }
    public function errors() {
        return $this->errors;
    }
    public function error($field, $message) {
        $this->errors[$field] = $message;
    }

}
```
## public/index.php
```php
<?php

session_start();
const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'core/function.php';



spl_autoload_register(function ($class) {
    $class= str_replace('\\',DIRECTORY_SEPARATOR,$class);
   require base_bath("{$class}.php");
});

require base_bath('bootstrap.php');

$router=new core\Router();

$routes = require base_bath('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

$router->route($uri,$method);

```

## views/notes/create.view.php
```php
<?php require base_bath('views/partials/head.php')?>
<?php require base_bath('views/partials/nav.php');?>
<?php require base_bath('views/partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">

       <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
           <div>
               <div class="md:grid md:grid-cols-3 md:gap-6">
                   <div class="mt-5 md:col-span-2 md:mt-0">
                       <form method="POST" action="/notes">
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

                                               ><?= $_POST['body'] ?? '' ?></textarea>
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
                               <button type="button"  class="text-sm font-semibold leading-6 text-gray-900 hover:bg-grey-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" >
                                   <a href="/notes">
                                       Back
                                   </a>
                               </button>
                               <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                           </div>
                       </form>


                   </div>
               </div>
           </div>
       </div>
    </div>
</main>
<?php require base_bath('views/partials/footer.php') ?>
```
## views/partials/nav.php
```php
<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <img class="h-8 w-8" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                        <a href="/" class="<?=urlIs('/') ? "bg-gray-900 text-white":"text-gray-300 "?> hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Home</a>
                        <a href="/about" class="<?=urlIs('/about')?"bg-gray-900 text-white":"text-gray-300 "?> hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">About</a>
                        <?php if ($_SESSION["user"] ?? false) : ?>
                            <a href="/notes" class="<?=urlIs('/notes')?"bg-gray-900 text-white":"text-gray-300 "?> hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Notes</a>
                        <?php endif; ?>
                        <a href="/contact" class="<?=urlIs('/contact')?"bg-gray-900 text-white":"text-gray-300 "?> hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Contact Us</a>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <button type="button" class="rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                        <span class="sr-only">View notifications</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative ml-3">
                        <div>
                            <?php if (isset($_SESSION['user'])) : ?>
                                <button type="button" class="flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>

                                    <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                </button>
                            <?php else: ?>
                                <a href="/register" class="<?=urlIs('/register') ? "bg-gray-900 text-white":"text-gray-300 "?> hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Register</a>
                                <a href="/login" class="<?=urlIs('/login') ? "bg-gray-900 text-white":"text-gray-300 "?> hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Login</a>
                            <?php endif; ?>
                        </div>

                    </div>
                    <?php if ($_SESSION['user'] ?? false) : ?>
                        <div class="ml-3">
                            <form method="POST" action="/session">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class= "text-white hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Logout</button>
                            </form>

                        </div>
                    <?php endif; ?>

                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <!-- Mobile menu button -->
                <button type="button" class="inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <!-- Menu open: "hidden", Menu closed: "block" -->
                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <!-- Menu open: "block", Menu closed: "hidden" -->
                    <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="md:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
            <a href="#" class="bg-gray-900 text-white block rounded-md px-3 py-2 text-base font-medium" aria-current="page">Dashboard</a>

            <a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Team</a>

            <a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Projects</a>

            <a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Calendar</a>

            <a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Reports</a>
        </div>
        <div class="border-t border-gray-700 pb-3 pt-4">
            <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                    <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium leading-none text-white">Tom Cook</div>
                    <div class="text-sm font-medium leading-none text-gray-400">tom@example.com</div>
                </div>
                <button type="button" class="ml-auto flex-shrink-0 rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                    <span class="sr-only">View notifications</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </button>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Your Profile</a>

                <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Settings</a>

                <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Sign out</a>
            </div>
        </div>
    </div>
</nav>
```

## views/registration/create.view.php
```php

<?php require base_bath('views/partials/head.php')?>
<?php require base_bath('views/partials/nav.php');?>
<?php //require base_bath('views/partials/banner.php')?>
    <main>
        <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
                <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Register for a new account</h2>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <form class="space-y-6" action="/register" method="POST">
                    <div>
<!--                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>-->
                        <div class="mt-2">
                            <input
                                    id="email"
                                   name="email"
                                   type="email"
                                   autocomplete="email"
                                   required
                                    placeholder="Email Address"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            >
                            <?php if(isset($errors['email'])) : ?>
                                <p class="text-red-500 text-xs mt-2">
                                    <?= $errors['email']?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
<!--                        <div class="flex items-center justify-between">-->
<!--                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label> -->
<!--                        </div>-->
                        <div class="mt-2">
                            <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    placeholder="Password"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            >
                            <?php if(isset($errors['password'])) : ?>
                                <p class="text-red-500 text-xs mt-2">
                                    <?= $errors['password']?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Register</button>
                    </div>
                </form>


            </div>
        </div>
    </main>

<?php require base_bath('views/partials/footer.php') ?>

```

## views/session/create.view.php
```php

<?php require base_bath('views/partials/head.php')?>
<?php require base_bath('views/partials/nav.php');?>
<?php //require base_bath('views/partials/banner.php')?>
<main>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Login in !</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="/session" method="POST">
                <div>
                    <!--                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>-->
                    <div class="mt-2">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            placeholder="Email Address"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        >
                        <?php if(isset($errors['email'])) : ?>
                            <p class="text-red-500 text-xs mt-2">
                                <?= $errors['email']?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <!--                        <div class="flex items-center justify-between">-->
                    <!--                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label> -->
                    <!--                        </div>-->
                    <div class="mt-2">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            placeholder="Password"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        >
                        <?php if(isset($errors['password'])) : ?>
                            <p class="text-red-500 text-xs mt-2">
                                <?= $errors['password']?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Login</button>
                </div>
            </form>


        </div>
    </div>
</main>

<?php require base_bath('views/partials/footer.php') ?>
```

## views/index.view.php
```php
<?php require base_bath('views/partials/head.php')?>
<?php require base_bath('views/partials/nav.php');?>
<?php require base_bath('views/partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p>
            Hello, <?= $_SESSION['user']['email'] ?? 'Guest' // I'll change it later?> . Welcome to the home page. 
        </p>
    </div>
</main>
<?php require base_bath('views/partials/footer.php')?>
```

## routes.php
```php

<?php require base_bath('views/partials/head.php')?>
<?php require base_bath('views/partials/nav.php');?>
<?php require base_bath('views/partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p>
            Hello, <?= $_SESSION['user']['email'] ?? 'Guest' // I'll change it later?> . Welcome to the home page. 
        </p>
    </div>
</main>
<?php require base_bath('views/partials/footer.php')?>
```

## some updates till ep 45
- update public/index.php
- update views/session/create.view.php
- update Http/controllers/session/create.php
- update Http/controllers/session/store
- create core/Session.php class
- update core/Authenticator.php class
- "Adding some updates to function.php"

## public/index.php
```php

<?php

use core\Session;

session_start();
const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'core/function.php';



spl_autoload_register(function ($class) {
    $class= str_replace('\\',DIRECTORY_SEPARATOR,$class);
   require base_bath("{$class}.php");
});

require base_bath('bootstrap.php');

$router=new core\Router();

$routes = require base_bath('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

$router->route($uri,$method);

//unset($_SESSION['flash']); we use class Session on it
Session::unflash();
```
## core/Authenticator.php
```php
<?php

namespace core;

class Authenticator
{
    public function attempt($email, $password)
    {

        $user = App::resolve(Database::class)
            ->query('select * from users where email = :email',[
            'email' => $email
        ])->find();

        if ($user) {
            if (password_verify($password,$user['password'])) {
                $this->login([
                    'email' => $email
                ]);

                return true;
            }
        }
        return false;


    }

    public function login($user) {
        $_SESSION['user'] = [
            'email' => $user['email']
        ];

        session_regenerate_id(true);
    }

    public function logout() {
        Session::destroy();
    }

}
```

## core/function.php
- IT NEEDS MORE EDIT
- LOGIN & LOGOUT DIDN'T WORK SO I KEPT THEM HERE
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

function abort($code = 404)
{
    http_response_code($code);
    require base_bath("views/{$code}.php");
    die();

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

function redirect($path) {
    header("location:{$path}");
    exit();
}

function login($user) {
    $_SESSION['user'] = [
        'email' => $user['email']
    ];

    session_regenerate_id(true);
}
function logout() {
    $_SESSION = [];
    session_destroy();

    $parms = session_get_cookie_params();
    setcookie('PHPSESSID', '', time() - 3600, $parms['path'], $parms['domain'], $parms['secure'], $parms['httponly']);
}

function old($key, $default = '') {
    return core\Session::get('old')[$key] ?? $default ;
}
```

## core/Session.php
```php
<?php

namespace core;

class Session
{
    public static function has($key) {
        return (bool) static::get($key);
    }

    public static function put($key, $value){
        $_SESSION['key'] = $value;
    }

    public  static function get($key, $default = null) {
//        if (isset($_SESSION['_flash'][$key])) {
//            return $_SESSION['_flash'][$key];
//        }
//        return $_SESSION['key'] ?? $default; we can make it with shorthand
        return $_SESSION['_flash'][$key] ?? $_SESSION[$key] ?? $default;
    }

    public static function flash($key, $value) {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function unflash() {
        unset($_SESSION['_flash']);

    }

    public static function flush() {
        $_SESSION = [];
    }

    public static function destroy() {
        //        $_SESSION = []; we can use here flush method form session
        static::flush();
        session_destroy();

        $parms = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $parms['path'], $parms['domain'], $parms['secure'], $parms['httponly']);
    }

}
```

## Http/controllers/session/create.php
```php
<?php
view('session/create.view.php',[
    'errors' => $_SESSION['_flash']['errors'] ?? []
]);
```
## Http/controllers/session/store.php
```php
<?php

use core\Authenticator;
use core\Session;
use Http\Forms\LoginForm;

// login the user if the credentials match.
$email = $_POST['email'];
$password = $_POST['password'];

$form = new LoginForm();
if ($form->validate($email,$password)) {
    if ((new Authenticator)->attempt($email,$password))
    {
        redirect('/');
    //    header('location: /');
    //    exit();
    }
$form->error('email','No matching account found for that email address and password');
}


//$_SESSION['errors'] = $form->errors(); we need it to be flashed
//$_SESSION['_flash']['errors'] = $form->errors(); //  we did session class to help it
Session::flash('errors',$form->errors());
Session::flash('old', [
    'email' => $_POST['email']
]);

return redirect('/login');
//return view('session/create.view.php',[
//    'errors' => $form->errors()
//]);

```

## views/session/create.view.php
```php

<?php require base_bath('views/partials/head.php')?>
<?php require base_bath('views/partials/nav.php');?>
<?php //require base_bath('views/partials/banner.php')?>
<main>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Login in !</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="/session" method="POST">
                <div>
                    <!--                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>-->
                    <div class="mt-2">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            placeholder="Email Address"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            value=" <?= old('email') ?>"
                        >
                        <?php if(isset($errors['email'])) : ?>
                            <p class="text-red-500 text-xs mt-2">
                                <?= $errors['email']?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <!--                        <div class="flex items-center justify-between">-->
                    <!--                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label> -->
                    <!--                        </div>-->
                    <div class="mt-2">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            placeholder="Password"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        >
                        <?php if(isset($errors['password'])) : ?>
                            <p class="text-red-500 text-xs mt-2">
                                <?= $errors['password']?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Login</button>
                </div>
            </form>


        </div>
    </div>
</main>

<?php require base_bath('views/partials/footer.php') ?>
```
## some edit on ep 46
- update Forms/LoginForm.php class
- update core/Router.php class
- create core/ValidationException.php class
- update Http/controllers/session/store.php
- update public/index.php

## Forms/LoginForm.php 
```php
<?php

namespace Http\Forms;

use core\ValidationException;
use core\Validator;

class LoginForm
{
    protected $errors = [];

    public function __construct(public array $attributes)
    {
        if (! Validator::email($attributes['email'])) {
            $this->errors['email'] = 'Please provide valid email address';
        }
        if (! Validator::string($attributes['password'])) {
            $this->errors['password'] = 'Please provide a valid password.';
        }

    }

    public static function validate($attributes)
    {
        $instance = new static($attributes);

        return $instance->faild() ? $instance->throw() : $instance;
//        if ($instance->faild()) {
////            throw new ValidationException();
//            $instance->throw();
//        }
//
//        return $instance;

    }

    public function throw ()
    {
        ValidationException::throw($this->errors(), $this->attributes);

    }

    public function  faild() {
        return count($this->errors);
    }
    public function errors() {
        return $this->errors;
    }
    public function error($field, $message) {
        $this->errors[$field] = $message;
        return $this;
    }

}
```
## core/Router.php
 * add the previousUrl function
```php
<?php

namespace core;

use core\Middleware\Middleware;

class  Router {
    protected $routes = [];

    public function add($method, $uri,$controller) {
        $this->routes[]= [
            'uri'=> $uri,
            'controller' =>$controller,
            'method' => $method,
            'middleware' => null
        ];
        return $this;
    }

    public function get($uri,$controller){
        return $this->add('GET', $uri, $controller);
    }

    public function post($uri,$controller){
        return $this->add('POST', $uri, $controller);
    }

    public function delete($uri,$controller){
       return  $this->add('DELETE', $uri, $controller);
    }

    public function patch($uri,$controller){
       return  $this->add('BATCH', $uri, $controller);
    }

    public function put($uri,$controller){
       return  $this->add('PUT', $uri, $controller);
    }

    public function only($key) {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;
        return $this;
    }
    public function route($uri,$method){
        foreach ($this->routes as $route) {
            if ($route['uri'] == $uri && $route['method'] == strtoupper($method)) {

                Middleware::resolve($route['middleware']);
                return require base_bath('Http/controllers/' . $route['controller']);
            }
        }
        $this->abort();
    }

    public function previousUrl() {
        return $_SERVER['HTTP_REFERER'];
    }

    public function abort($code=404){
    http_response_code($code);
    require base_bath("views/{$code}.php");
    die();
    }



}

```

## ValidationException.php
```php
<?php

namespace core;

class ValidationException extends \Exception
{
    public readonly array $errors;
    public readonly array $old ;
    public static function throw($errors, $old) {
        $instance =  new static;

        $instance->errors = $errors;
        $instance->old = $old;

        throw $instance;
    }
}
```
## Http/session/store.php
```php
<?php

use core\Authenticator;
//use core\Session;
//use core\ValidationException;
use Http\Forms\LoginForm;

// login the user if the credentials match.
//$email = $_POST['email'];
//$password = $_POST['password'];


$form = LoginForm::validate($attributes = [
    'email' => $_POST['email'],
    'password' => $_POST['password']
]);


$signedIn = (new Authenticator)->attempt(
    $attributes['email'], $attributes['password']
);

if ($signedIn) {
    $form->error(
        'email','No matching account found for that email address and password'
    )->throw();
}

    redirect('/');


//$_SESSION['errors'] = $form->errors(); we need it to be flashed
//$_SESSION['_flash']['errors'] = $form->errors(); //  we did session class to help it
//Session::flash('errors',$form->errors());
//Session::flash('old', [
//    'email' => $_POST['email']
//]);

//return redirect('/login');
//return view('session/create.view.php',[
//    'errors' => $form->errors()
//]);

```

## public/index.php
```php
<?php

use core\Session;
use core\ValidationException;

session_start();
const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'core/function.php';



spl_autoload_register(function ($class) {
    $class= str_replace('\\',DIRECTORY_SEPARATOR,$class);
   require base_bath("{$class}.php");
});

require base_bath('bootstrap.php');

$router=new core\Router();

$routes = require base_bath('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

try {
    $router->route($uri,$method);
} catch (ValidationException $exception) {
    Session::flash('errors',$exception->errors);
    Session::flash('old',$exception->old);

    return redirect($router->previousUrl());
}

//unset($_SESSION['flash']); we use class Session on it
Session::unflash();

```
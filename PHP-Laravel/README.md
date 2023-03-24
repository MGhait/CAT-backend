# php notes
```php
// echo -Output stringsm numbers, html, etc
// echo 123, 'html', 10.5;

//print - Works like echo, but can jonly take in a single argument 
// print 123;
// print 'html';

// print_r() - print single values and arrays 
 //print_r([1,2,3]);

 //var_dump() - Returns more ingo like data type and length 
//var_dump("hello");
//var_dump(ture);

 // var_export() - Similar to var_dump(). outputs a string representation of a variable
//var_export('hello');

<?php 
    echo "MOhamedGhait";
    echo "welcom php";
    echo "<br>";
    print "hi php ";
    print '<br>'
    // hi there is a comment 
/* 
this is alsoo  a comment 
*/
# guess what this is also comment ^-^


?>  
<!-- Short tag -->
<?= "We love php From Short tag <br>";?>

<?=  'hellow world From Short Tag';?>
<?php
/*
 
===========================================
=data types
=..........
=Type Juggling + Automatic Type Conversion
===========================================

*/
echo '<br>';
echo 1+'2';// 3
echo '<br>';
echo 1+"2";// 3
echo '<br>';
echo  gettype(1+'2');//integer
echo '<br>';
echo  gettype(1+"2");//integer
echo '<br>';
echo true;//1
echo '<br>';
echo  gettype(true);//Boolean
echo '<br>';
echo  gettype(true+true);// integer
echo '<br>';
echo true+true;//2
echo '<br>';
echo 5+'5 lessons';//10 => warining
echo '<br>';
echo gettype(5+'5 Lessons');//integer =>warining
echo '<br>';
echo 10+15.6;//25.6
echo '<br>';
echo gettype(10+15.6);//double=>Float
echo '<br>';
// notice that this php tag didn't closed [best practice at the end of page ..]


===========================================
=data types
=..........
=Type Juggling + Automatic Type Conversion
===========================================

*/
echo '<br>';
echo 1+'2';// 3
echo '<br>';
echo 1+"2";// 3
echo '<br>';
echo  gettype(1+'2');//integer
echo '<br>';
echo  gettype(1+"2");//integer
echo '<br>';
echo true;//1
echo '<br>';
echo  gettype(true);//Boolean
echo '<br>';
echo  gettype(true+true);// integer
echo '<br>';
echo true+true;//2
echo '<br>';
echo 5+'5 lessons';//10 => warining
echo '<br>';
echo gettype(5+'5 Lessons');//integer =>warining
echo '<br>';
echo 10+15.6;//25.6
echo '<br>';
echo gettype(10+15.6);//double=>Float
echo '<br>';
// notice that this php tag didn't closed [best practice at the end of page ..]

```
## variables

```php
//variable starts with dollarSign ($)
$name ='Mohamed';// Stirng 
$age = 20;/ Int
$has_kids=false;// Bool
$cash_on_hand=20.50;// Float
// use var_dump to see Boolean Value 
// in PHP true => 1       False =>(Empty||nothing)=>" "

echo ${name}.'is '${age}.'years old';
// concatination we can use (+) or dot(.)

//constant in PHP we use define();
define('HOST', 'localhost');
define('DB_NAME','dev_db');
echo HOST;
echo  DB_NAME;


<?php
echo "new lesson * Variables* <br>";
// variable in php start with $
// can't include special character lik"e @#$%^&*
$username="mohamed";
$Username="Saad";// php is case-sensitive
echo "hello $username<br>";
echo "hi $Username<br>";
echo 'hello $username';// in single quote we can't use variable


echo "<br>========================== <br>";
$name="mohamed";
$$name="GHitOOt";
echo "<b>$name</b>"." Famous for <b>$mohamed</b> <br>";
echo $GHitOOt;
echo "<br>";
echo $name;
echo "<br>";
echo $$name;
echo "<br>";
echo $mohamed;
echo "<br>";
echo $GHitOOt;
echo "<br>";
echo "mohamed is known as ${$name}";// GHitOOt


echo "<br>=========================================================<br>";
$a="mohamed";
$b=$a;// assign by value
echo $a .$b;
echo "<br>";
$c="Gheit";
$d=&$c;// assign by reference
echo $c.$d;
echo "<br>";
$a="Saad";
echo $b.$a;
echo "<br>";
$c="Abo-Gheit";
echo $c.$d;
echo "<br>";
```

## Arrays

```php
<?php
    /* data type */
    echo "hi";   ?>
<?= "Short PHP tag<br>"?>
<?php
// echo we don't close php tage if it is last thing in page
    echo "hello World!";
    echo "hi Php<br>";
    echo 'array<br>';
    //
    echo "<pre>";
    print_r(
        [
            'A' =>"ahmed",
            'B' => "Wael",
            1=>"mostfa",// show first before index 0
            0=>'yasien',
            5=>'Elzero5',
            3=>'Elzero3',
            "elzeroo",// index 6 because last index above is 5
            "elzeroo2",// index 7
            "mohamed" =>// array didn't have index in outer array
                [
                    'Saad',
                    "said",
                    "Ibrahim"
                ],
            false =>"override Yasien",// override index 0
            //  ( true override index 1)
            "mamdouh" // took index 8 as element

        ]
    );
    echo "</pre>";
    
    // arrays has index and kay
print_r([
	0=> "sameh",// index 0
	"A"=>"Ahamed",// kay
	"B"=>"Mohamed",
	true=>"Wael",// index 1
	"Mostafa",// no index complete from last indx (1) => index 2

	
]);

```


## Constants
```php

<?php
echo "new lesson * Variables* <br>";
// variable in php start with $
// can't include special character lik"e @#$%^&*
$username="mohamed";
$Username="Saad";// php is case-sensitive
echo "hello $username<br>";
echo "hi $Username<br>";
echo 'hello $username';// in single quote we can't use variable


echo "<br>========================== <br>";
$name="mohamed";
$$name="GHitOOt";
echo "<b>$name</b>"." Famous for <b>$mohamed</b> <br>";
echo $GHitOOt;
echo "<br>";
echo $name;
echo "<br>";
echo $$name;
echo "<br>";
echo $mohamed;
echo "<br>";
echo $GHitOOt;
echo "<br>";
echo "mohamed is known as ${$name}";// GHitOOt


echo "<br>=========================================================<br>";
$a="mohamed";
$b=$a;// assign by value
echo $a .$b;
echo "<br>";
$c="Gheit";
$d=&$c;// assign by reference
echo $c.$d;
echo "<br>";
$a="Saad";
echo $b.$a;
echo "<br>";
$c="Abo-Gheit";
echo $c.$d;
echo "<br>";


/* 
    Pre-Defined Constants
*/


<?php
echo php_uname();// constant method that give info about the operating system
/*
 *  Pre-Defined Constants (cans-sensitive)
 * - PHP_VERSION
 * - PHP_OS_FAMILY
 * - PHP_INT_MAX
 * - DEFAULT_INCLUDE_PATH


 *  Magic Constants (cas-insensitive)
 * - __LINE__   // refers to the current line
 * - __FILE__   // get file bath
 * - __DIR__    // get file directory


 *Search For Topics
 * runtime vs compile time
 * List Of Reserved Keywords
 * PHP defined constants
 

 */
ECHO" <br>";
echo __LINE__;
ECHO" <br>";
echo __FILE__;
ECHO" <br>";
echo __DIR__;
ECHO" <br>";
```


## Operators

```php
<?php
/*
 * Operators
 * Arthmitic Operator
 * $a [+] $b => add
 * $a [-] $b => subtraction
 * $a [*] $b => multiplication
 * $a [/] $b => Division
 * $a [**] $b =>Exponential
 * $a [%] $b => modulus
 *
 *
 * +$a==========>Identity
 * -$a==========>Negation
 */
 echo 10+20;
 echo '<br>';
 echo gettype(10+20);
 echo '<br>';
 echo 9.5+10.5;
 echo '<br>';
 echo gettype(9.5+10.5);
 echo '<br>';
 echo '<br>';


echo 10-20;
echo '<br>';
echo gettype(10-20);
echo '<br>';
echo 9.5-20.5;
echo '<br>';
echo gettype(9.5-20.5);
echo '<br>';
echo '<br>';


echo 10*20;
echo '<br>';
echo gettype(10*20);// Integer
echo '<br>';
echo 9.5*20.5;
echo '<br>';
echo gettype(9.5*20.5);// Double
echo '<br>';
echo '<br>';


echo 10/20;
echo '<br>';
echo gettype(10/20);// double ******
echo '<br>';
echo 40/20;
echo '<br>';
echo gettype(40/20);// integer *****
echo '<br>';
echo 40/7;
echo '<br>';
echo gettype(40/7);// double *******
echo '<br>';
echo '<br>';

echo "100";
echo'<br>';
echo gettype("100");// String
echo'<br>';
echo +"100";
echo'<br>';
echo gettype(+"100");// Integer
echo'<br>';echo'<br>';

echo "-100";
echo'<br>';
echo gettype("-100");// string
echo'<br>';
echo -"100";
echo'<br>';
echo gettype(-"100");// integer
echo'<br>';echo'<br>';
echo -"-100";
echo'<br>';
echo gettype(-"-100");// integer
echo'<br>';echo'<br>';


/*
 * 
 * Assignment Operators
 * 
 * $a [+=] $b => Addition 
 * $a [-=] $b => Subtraction
 * $a [*=] $b => Multiplication 
 * $a [/=] $b => Division 
 * $a [%=] $b => Modulus 
 * $a [**=] $b => Exponentiation 
 * 
 * 
 */

$a=10;
$a +=30;// ==40== $a=$a+40
echo $a;
$b=20;
$b-=5;// ==15== $b=$b-5
echo $b;

/*
 * Comparison Operators
 * // to compare two values
 *
 * -  ==    =>Equal  =>same value
 * -  !=    =>Not Equal  => Not same value
 * -  <>    =>Not Equal  => Not same value ===same as !=
 * -  ===    =>Identical  => same value and same data type
 * -  !==    =>Not Identical  =>Not same value or not same data type
 */

// test Equal
var_dump(100==100);// true
echo '<br>';
var_dump(100=="100");//true
echo '<br>';
var_dump(100.0=="100");//true
echo '<br>';
var_dump(100.001=='100');// false
echo '<br>';
var_dump(100.1!='100');//true
echo '<br>';
var_dump(100.1<>'100');//true
echo '<br>';
echo '<br>';
echo '#####################';
echo '<br>';

// test Identical
var_dump(100===100);// true
echo '<br>';
var_dump(100==="100");//true
echo '<br>';
var_dump(100.0==="100");//true
echo '<br>';
var_dump(100.001==='100');// false
echo '<br>';
var_dump(100.1!=='100');//true
echo '<br>';
echo '<br>';
echo '#####################';
echo '<br>';



// spaceship operator <=>  ( Less than[-1], Equal [0], Greater[1]  )

var_dump(100<=>100);// 0 same value
echo '<br>';
var_dump(100<=>200);// -1 less than
echo '<br>';
var_dump(100<=>30);// 1 greater than
echo '<br>';

// concatinat Operator

/*
		we concatinate wiht [.] or [.=]

*/

$my_name="mohamed ";
$my_name .="saad";
$my_name .= " Gheit ";
echo $my_name;


/*
post and pre increamet and decrement


*/

$a=5;
echo ++$a;//6
echo '<br>';
echo $a;//6
echo '<br>';

$b=5;
echo $b++;// 5
echo '<br>';
echo $b;//6
echo '<br>';
/* logical operator*/
$a=5;
echo ++$a;//6
echo '<br>';
echo $a;//6
echo '<br>';

$b=5;
echo $b++;// 5
echo '<br>';
echo $b;//6
echo '<br>';


/*
array operatior
*/
$arr1=[1=>"mohamed",2=>'saad'];
$arr2=[2=>"Saad",3=>"ghait",0=>"eng"];
echo '<pre>';
print_r($arr1+$arr2);// take first vlaue if there is two keys
$arr10=[1=>"mohamed",2=>'saad'];
$arr20=[0=>"eng",3=>"ghait"];
echo '<pre>';
print_r($arr10+$arr20);
echo '</pre>';
echo '<br>';
$arr3=[1=>5,2=>'elzero',3=>"mohamed"];
$arr4=[1=>5,3=>"mohamed",2=>'elzero'];
var_dump($arr3==$arr4);// same value same key ==> true
echo '<br>';
$ar=[1=>5,2=>'mohamed',3=>"saad",5=>6];
$ar1=[1=>5,3=>"saad",5=>6,2=>'mohamed'];
$ar2=[1=>5,2=>'mohamed',3=>"saad",5=>6];
var_dump($ar===$ar1);// not identical not same order
echo'<br>';
var_dump($ar===$ar2);// identical same value same key same order


/*
 * foreach
 * #######
 * 
 * foreach(Array_name as $value){ // code }
 * 
 * foreach (Array_name as $key => $value ){// code }
 * 
 */

$country=['EG','Qt','USA','UK'];
echo '<pre>';
print_r($country);
echo '</pre>';
foreach ($country as $name)
    {
        echo "$name <br>";
    }

$coun_with_dis=['Eg'=>50,'Qt'=>40,'USA'=>90,'UK'=>80];
echo '<pre>';
print_r($coun_with_dis);
echo '</pre>';

foreach ($coun_with_dis as $name=>$value):
    echo "Country sympol is $name and Discount Is $value <br>";
endforeach;
```










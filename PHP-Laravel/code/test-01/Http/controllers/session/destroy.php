<?php

// log the user out
use core\Session;
Session::destroy();
//logout function didn't work for me
redirect('/');

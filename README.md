# 🏜 maginot
Maginot is a php tool class for managing lines into php files

## Installation
`composer require novia713/maginot`

## Usage
Instance Maginot
```php
use Novia713\Maginot\Maginot;

$maginot = new Maginot();
````

then you can do the following things:

 📍 _comment a line into a file_
 ```php
 $maginot->commentLine("this is an example line. can be whatever", $myFile);
 ```
 📍 _uncomment a line into a file_
 ```php
$maginot->unCommentLine("this is an example line. can be whatever", $myFile));
 ```
 📍 _set the first line of a file_
 ```php
$maginot->setFirstLine("this is an example line. can be whatever", $myFile));
 ```
 📍 set the last line of a file_
 ```php
$maginot->setLastLine("this is an example line. can be whatever",  $myFile);
 ``` 
 📍 _get the first line of a file_
 ```php
$maginot->getFirstLine( $myFile);
 ```
 📍 _get the last line of a file_
 ```php
$maginot->getLastLine( $myFile);
```

 📍 _get n line of a file_
 ```php

$maginot->getNLine( $myFile, 2);
 ```

 📍 _get the line number of a given line into a file_
 ```php
$maginot->getLineNumber("this is an example line. can be whatever", $myFile);
```

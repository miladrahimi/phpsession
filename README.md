# PHPSession
Free PHP session tools for neat and powerful projects!

## Documentation
PHPSession is a tiny package for using sessions in Object Oriented and more secured approach.
There is a singleton class named `Session` which you interact with to access an manipulate sessions.


### Installation
#### Using Composer
It's strongly recommended to use [Composer](http://getcomposer.org) to add PHPSession to your application.
If you are not familiar with Composer, The article
[How to use composer in php projects](http://www.miladrahimi.com/blog/2015/04/12/how-to-use-composer-in-php-projects)
can be useful.
After installing Composer, go to your project directory and run following command there:
```
php composer.phar require miladrahimi/phpsession
```
Or if you have `composer.json` file already in your application,
you may add this package to your application requirements
and update your dependencies:
```
"require": {
    "miladrahimi/phpsession": "dev-master"
}
```
```
php composer.phar update
```
#### Manually
You can use your own autoloader as long as it follows [PSR-0](http://www.php-fig.org/psr/psr-0) or
[PSR-4](http://www.php-fig.org/psr/psr-4) standards.
In this case you can put `src` directory content in your vendor directory.

### Getting Started
It's so easy to work with!
```
use MiladRahimi\PHPSession\Session;

$session = Session::getInstance();
$session->set("Singer", "Pink Floyd");
echo $session->get("Singer");
```

*   Because of singleton pattern, you cannot instantiate `Session` class with `new` keyword.
*   The `get()` method will return `null` whenever the value doesn't exist.

### Expiration
In default, the session data is permanent.
For security reasons, you should define lifetime for your session.
PHPSession would expire the data when their lifetime had finished.
```
use MiladRahimi\PHPSession\Session;

$session = Session::getInstance();
$session->setLifeTime(10); // 10 minutes lifetime!
$session->set("Singer", "Bon Jovi");
```
*   Lifetime unit is minute.
*   0 minute lifetime means unlimited time (it's default value).
*   Whenever you change lifetime its lifetime starts to get spent.

### UntrustedSession Exception
For security reasons, PHPSession holds user IP and Agent (web browser tool).
It always check this info in any `get()` call to make sure current user is the real session owner.
`UntrustedSession` exception will thrown if the user is suspicious.
```
use MiladRahimi\PHPSession\Session;
use MiladRahimi\PHPSession\UntrustedSession;

$session = Session::getInstance();
$session->set("Singer", "Selena Gomez");
try {
    echo $session->get("Singer");
} catch (UntrustedSession $e) {
    echo "You must sign in again!";
    // Log the information...
}
```

### Refresh
PHPSession cannot recognize where exactly need to refresh the user info like IP and agent.
To prevent Fixation Session Attack you should refresh sessions every successful sign in request.
```
$session->refresh();
```

### Session Hijacking and Fixation
PHPSessions is tiny package right now.
It will be better next versions, I promise!
There are some security considerations in this version.
It prevent Session Fixation if you call `refresh()` method after every successful sing in.
It prevent simple hijackings with holding user info (IP and agent).
In person, I think it's user job to prevent other threats.
But your a able to implement your security approaches with PHPSession,
if you see it like PHP native APIs.
If you have an idea to make PHPSession more secure,
I will appreciate it if you share it with us!

## Contributors
*	[Milad Rahimi](http://miladrahimi.com)

## Official homepage
*   [PHPSession](http://miladrahimi.github.io/phpsession)

## License
PHPSession is released under the [MIT License](http://opensource.org/licenses/mit-license.php).
ROPH PHP Framework
=============================

## What's ROPH

The name ROPH is abbrievated from `Rodin's Object-Oriented PHP Framework`. And, [罗敷](http://baike.baidu.com/view/117149.htm#sub5036867) (pron. `/'lauo'fu:/`) is a smart beauty in ancient Chinese.

ROPH is a lightweight flow-based PHP framework, which aims at simple, truely flexible. And also, ROPH compatible with the PHP class naming conventions and, any PHP library or framework which conform to the convention can work with ROPH together. 

## Features

- Truly flexible flow definations. Actually, ROPH is not only a web framework, it's a meta framework which can customize a new domain-specific framework easily.
- Flexible and simple class loader. The [RO_Loader](https://github.com/slawdan/ROPH/blob/master/Loader.php) compatible with the common PHP class naming convention, and more, it support load different library from different locations. It's useful when need replace some library with new one and compatible with other frameworks ( Smarty, Zend, etc ) simply. It also provide class caching which can detect which class is loaded and dumps to combined, densed file to fasten loading next time.
- Simple and replacable [web flow](https://github.com/slawdan/ROPH/blob/master/Flow). Users can replace any builtin component with customized one. And the simple business logic provides efficiency execution.
- A simple [Table Gateway](https://github.com/slawdan/ROPH/blob/master/Content) model approach is included. 
- Human readable [JSON format configuration](https://github.com/slawdan/ROPH/blob/master/Config) is supported. Chinese characters is allowable.
- [PHP-Serpent](http://code.google.com/p/serpent-php-template-engine/) template engine is recommended, v1.3.0 compatible.
- Writen in PHP 5.2, and PHP 5.3+ compatible.
- Other sparkle features.

## TODO 

- An event-driven framework.
- Use namespace to provide better PHP 5.3+ version
- An event-driven concurrent stream / curl wrapper.
- BigPipe (akka Facebook) render support.

## License

2008-2018, [New BSD License](https://github.com/slawdan/ROPH/blob/master/LICENSE)

## Author

- Rodin Shih 
  ![Mail](http://rodin.rizili.com/wp-content/mail_image/mail_gmail.png)
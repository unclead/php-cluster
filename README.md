# php-cluster

## About

Implementation of simple web service to work with `Cmd` object. 

```php
class Cmd {
    private $count = 0;
    public function getCount ()
    {
        return $this->count;
    }
    public function setCount($v)
    {
        $this->count = $v;
    }
    public function increment ()
    {
        return ++$this->count;
    }
}
```

You can run several instances to build a cluster. Every instances in cluster keeps its own copy of object `Cmd` 
but every time when you call increment operation via HTTP request all instances in cluster synchronize with each other.
Thus, all instances have the same state.

When you start a new instance it also synchronizes with other instances.


## Installation

1) Unsure that you have composer on your machine. If you do not already have Composer installed, you may do so by 
following the instructions at [getcomposer.org](https://getcomposer.org/download/)

2) Run `composer install` command

## Using

1) Run a web service instance

```
php server.php --port=8081 --host='127.0.0.1' --partner-ports='8083,8084'
```

You must specify the following options:

- *port* - the port on which the application runs
- *host* - the IP address on which the application runs
- *partner-ports* - the list of ports on which the other applications in cluster runs

2) If you want to add another one instance to cluster you should run web service with modified options as in example below:

```
php server.php --port=8083 --host='127.0.0.1' --partner-ports='8081,8084'
```

3) Now you can use browser or cURL command to send a HTTP request for getting data.

```
curl 'http://127.0.0.1:8081/cmd1'
12
curl 'http://127.0.0.1:8083/cmd1'
13
curl 'http://127.0.0.1:8081/cmd1'
14
curl 'http://127.0.0.1:8083/cmd1'
15
```

You can run another one instance as in example below

```
php server.php --port=8084 --host='127.0.0.1' --partner-ports='8081,8083'
```

and after that if you perform HTTP request you should get 

```
curl 'http://127.0.0.1:8084/cmd1'
16
```


> Note: You can use any digit in URL e.g. cmd2, cmd5 and etc. Every time when you call command the system checks 
> whether instance of `Cmd` class already initialized or not. If no, the system will create new instance and will return value 1.
> After that the system will perform synchronization procedure with other web services in cluster. If you send HTTP request again 
> on other web service in cluster you will get value 2 and so on


Also you can find some information in console log

```
php server.php --port=8084 --host='127.0.0.1' --partner-ports='8083,8081'
2016-02-06 01:21:58 [DEBUG...] Sync collection with other instances in cluster
2016-02-06 01:21:58 [DEBUG...] Current collection state: {"2":5,"1":15}
2016-02-06 01:21:58 [DEBUG...] Application is started on 127.0.0.1:8084
2016-02-06 01:21:58 [DEBUG...] Partner application ports: 8083,8081
2016-02-06 01:22:01 [INFO....] IncrementCountCommand completed successfully. Result: 16
2016-02-06 01:22:01 [INFO....] Synchronization with instance http://127.0.0.1:8083 completed successfully.
2016-02-06 01:22:01 [INFO....] Synchronization with instance http://127.0.0.1:8081 completed successfully.
```


## TODO

- prevent race condition. For example use mutex for this purpose.
- implement web services manager to start/stop/add instance
- improve `partner-port` option to be able use a range of ports, e.g. 8081-8085
- implement ability to start web service instances of different ip's


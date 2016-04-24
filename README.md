# ULog
PHP日志组件，可以使用多种存储方式记录日志，并且可以自由扩展。

## 使用方法
```php
use AGarage\ULog\ULog as ULog;
use AGarage\ULog\Writer\DoctrineStorage as DoctrineStorage;

$config = [
    'host' => 'localhost',
    'service' => 'ULog test',
    'storages' => [
        [
            'class' => 'AGarage\ULog\Storage\SingleFileStorage',
            'level' => ULog::INFO,
            'path' => '/tmp/ulog.log'
        ]
    ]
];

//初始化单例模式ULog
ULog::initialize($config);
//获取单例模式ULog
$logger = ULog::getLogger();

$anotherLogger = new ULog($config);

//初始化一个数据库连接用于ULog
$conn = \Doctrine\DBAL\DriverManager::getConnection([
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'user' => 'root',
    'password' => '123456',
    'dbname' => 'app',
    'charset' => 'utf8'
]);

//创建一个DoctrineStorage
$storage = new DoctrineStorage([
    'level' => ULog::INFO
]);
//为DoctrineStorage设置数据库连接
$storage->setConnection($conn);

//向ULog添加Storage
$logger->addStorage($storage);

$logger->info('TAG', 'This log will be written by storages in $logger.');
$anotherLogger->debug('TAG', 'This log will not written by storages in $anotherLogger.');
```

## 配置说明
### ULog
```php
[
    'host' => 'localhost',  //产生日志的主机名（默认localhost）
    'service' => 'ULog'     //产生日志的服务名（默认ULog）
    'storages' => [          //Writers
        [
            'class' => 'AGarage\ULog\Storage\SingleFileStorage',  //Storage的类名（必须）
            'level' => ULog::INFO   //大于该等级的日志将被记录（默认DEBUG）
            'path' => '/tmp/ulog.log'   //日志文件路径（必须）
        ]
    ]
]
```

### Storages
#### SingleFileStorage
使用单个文本日志文件记录日志
```php
[
    'class' => 'AGarage\ULog\Storage\SingleFileStorage',  //Writer的类名（必须）
    'level' => ULog::INFO   //大于该等级的日志将被记录（默认DEBUG）
    'path' => '/tmp/ulog.log'   //日志文件路径（必须）
]
```

#### DoctrineStorage
使用Doctrine记录日志
```php
[
    'class' => 'AGarage\ULog\Storage\DoctrineStorage',
    'level' => ULog::INFO   //默认DEBUG
]
```
初始化该类型Storage后需要调用`setConnection`方法为其设置数据库连接
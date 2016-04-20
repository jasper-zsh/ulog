# ULog
PHP日志组件，可以使用多种存储方式记录日志，并且可以自由扩展。

## 使用方法
```php
use AGarage\ULog\ULog as ULog;
use AGarage\ULog\Writer\DoctrineWriter as DoctrineWriter;

$config = [
    'host' => 'localhost',
    'service' => 'ULog test',
    'writers' => [
        [
            'class' => 'AGarage\ULog\Writer\SingleFileWriter',
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
$conn = new Doctrine\DBAL\Driver\PDOConnection('mysql:host=127.0.0.1;dbname=app', 'root', 'password');

//创建一个DoctrineWriter
$writer = new DoctrineWriter([
    'level' => ULog::INFO
]);
//为DoctrineWriter设置数据库连接
$writer->setConnection($conn);

//向ULog添加Writer
$logger->addWriter($writer);

$logger->info('TAG', 'This log will be written by writers in $logger.');
$anotherLogger->debug('TAG', 'This log will not written by writers in $anotherLogger.');
```

## 配置说明
### ULog
```php
[
    'host' => 'localhost',  //产生日志的主机名（默认localhost）
    'service' => 'ULog'     //产生日志的服务名（默认ULog）
    'writers' => [          //Writers
        [
            'class' => 'AGarage\ULog\Writer\SingleFileWriter',  //Writer的类名（必须）
            'level' => ULog::INFO   //大于该等级的日志将被记录（默认DEBUG）
            'path' => '/tmp/ulog.log'   //日志文件路径（必须）
        ]
    ]
]
```

### Writers
#### SingleFileWriter
使用单个文本日志文件记录日志
```php
[
    'class' => 'AGarage\ULog\Writer\SingleFileWriter',  //Writer的类名（必须）
    'level' => ULog::INFO   //大于该等级的日志将被记录（默认DEBUG）
    'path' => '/tmp/ulog.log'   //日志文件路径（必须）
]
```

#### DoctrineWriter
使用Doctrine记录日志
```php
[
    'class' => 'AGarage\ULog\Writer\DoctrineWriter',
    'level' => ULog::INFO   //默认DEBUG
]
```
初始化该类型Writer后需要调用`setConnection`方法为其设置数据库连接
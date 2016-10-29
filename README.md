---
title: 定时任务包
date: 2016/10/14 22:26:15
category: package
---

### 使用方法
    
``` bash

    vim ~/.bashrc 
    export PATH=$PATH:~/.composer/vendor/bin/
    soucre ~/.bashrc

    composer global require "menory/timer"
    
    menory-timer -s start # 运行测试 demo

    Usage：menory-timer-service -s [start | stop] [options]

```

### 相关依赖

    # yaml
    wget http://pecl.php.net/get/yaml-2.0.0.tgz
    tar zxvf yaml-2.0.0.tgz
    phpize
    ./configure
    make && make install

    
    # 修改 php.ini

        [swoole]
        extension=swoole.so
        swoole.use_namespace=On

        extension=redis.so
        extension=seaslog.so
        extension=yaml.so
        extension=phalcon.so
        extension=eio.so

        [xhprof]
        extension=xhprof.so;
        xhprof.output_dir=/tmp/xhprof

### demo
    
``` bash

    namespace Menory\Test;

    class Test
    {
        public static function run()
        {
            try {
                file_put_contents(
                    '/tmp/test.log',
                    '[log] '.date('Y-m-d H:i:s').' : ##########'."\n",
                    FILE_APPEND
                );
            } catch (\Exception $e) {
                // 异常处理
            } finally {
                // 清理工作
            }
        }
    }

```









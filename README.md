---
title: 定时任务包
date: 2016/10/14 22:26:15
category: package
---

### help

    Usage：timer.php -s [start | stop] [options]
    
    options：
        --config-path=/var/timer.yaml # 定时任务配置文件

    composer global require "menory/timer*"

### 使用
    
``` bash

    vim ~/.bashrc 
    export PATH=$PATH:~/.composer/vendor/bin/

    composer global require "menory/timer"
    
    menory-timer -s start # 运行测试 demo

```

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


        








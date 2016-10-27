<?php
namespace Menory\Test;

class Test2
{
    public static function run()
    {
        try {
            file_put_contents('/tmp/test2.log', '[log] '.date('Y-m-d H:i:s').' : ##########'."\n", FILE_APPEND);
        } catch (\Exception $e) {
        } finally {
            //  php -r "echo ((strtotime('2016-10-25 03:21:10') - strtotime('2016-10-23 17:52:10')) / 3600);"
        }
    }
}

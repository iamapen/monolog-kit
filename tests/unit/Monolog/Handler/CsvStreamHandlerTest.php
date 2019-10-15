<?php
declare(strict_types=1);
namespace Iamapen\MonologKit\Tests;

use Monolog\Logger;

class CsvStreamHandlerTest extends \PHPUnit\Framework\TestCase
{
    function test_streamWrite() {
        $fp = fopen('php://temp', 'rb+');
        $sut = new \Iamapen\MonologKit\Monolog\Handler\CsvStreamHandler($fp);

        $logger = new Logger('test', [$sut]);
        $logger->info('hey');

        rewind($fp);
        $b = stream_get_contents($fp);
        fclose($fp);
        var_dump($b);

        $this->markTestIncomplete('TODO 実装');
    }
}

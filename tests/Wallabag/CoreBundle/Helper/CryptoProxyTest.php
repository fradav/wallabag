<?php

namespace Tests\Wallabag\CoreBundle\Helper;

use Psr\Log\NullLogger;
use Monolog\Logger;
use Monolog\Handler\TestHandler;
use Wallabag\CoreBundle\Helper\CryptoProxy;

class CryptoProxyTest extends \PHPUnit_Framework_TestCase
{
    public function testCrypto()
    {
        $logHandler = new TestHandler();
        $logger = new Logger('test', [$logHandler]);

        $crypto = new CryptoProxy(sys_get_temp_dir().'/'.uniqid('', true).'.txt', $logger);
        $crypted = $crypto->crypt('test');
        $decrypted = $crypto->decrypt($crypted);

        $this->assertSame('test', $decrypted);

        $records = $logHandler->getRecords();
        $this->assertCount(2, $records);
        $this->assertContains('Crypto: crypting value', $records[0]['message']);
        $this->assertContains('Crypto: decrypting value', $records[1]['message']);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Decrypt fail
     *
     * @return [type] [description]
     */
    public function testDecryptBadValue()
    {
        $crypto = new CryptoProxy(sys_get_temp_dir().'/'.uniqid('', true).'.txt', new NullLogger());
        $crypto->decrypt('badvalue');
    }
}

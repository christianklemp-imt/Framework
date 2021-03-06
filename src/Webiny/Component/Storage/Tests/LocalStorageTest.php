<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */

namespace Webiny\Component\Storage\Tests;

use Webiny\Component\Storage\Storage;
use Webiny\Component\Storage\StorageTrait;

class LocalStorageTest extends \PHPUnit_Framework_TestCase
{
    use StorageTrait;

    const CONFIG = '/ExampleConfig.yaml';

    private $key = 'testFile.txt';
    private $newKey = 'newKey.txt';

    /**
     * @dataProvider driverSet
     */
    public function testConstructor($storage)
    {
        $this->assertInstanceOf('Webiny\Component\Storage\Storage', $storage);
    }

    /**
     * @dataProvider driverSet
     */
    public function testSave(Storage $storage)
    {
        $storage->setContents($this->key, 'Test contents');
        $this->assertSame('Test contents', $storage->getContents($this->key));
        $storage->setContents($this->key, 'Appended contents', true);
        $contents = $storage->getContents($this->key);
        $this->assertTrue(strpos($contents, 'Test contents') === 0);
        $this->assertTrue(strpos($contents, 'Appended contents') > 0);
    }

    /**
     * @dataProvider driverSet
     */
    public function testRename(Storage $storage)
    {
        $storage->renameKey($this->key, $this->newKey);
        $this->assertTrue($storage->keyExists($this->newKey));
    }

    /**
     * @dataProvider driverSet
     */
    public function testDelete(Storage $storage)
    {

        $storage->deleteKey($this->newKey);
        $this->assertFalse($storage->keyExists($this->newKey));

    }

    public function driverSet()
    {
        Storage::setConfig(realpath(__DIR__ . '/' . self::CONFIG));
        
        return [
            [$this->storage('LocalStorage')]
        ];
    }

}
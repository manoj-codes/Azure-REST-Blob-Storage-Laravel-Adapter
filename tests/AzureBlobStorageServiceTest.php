<?php

namespace AzureBlobStorage\Tests;

use PHPUnit\Framework\TestCase;
use AzureBlobStorage\Services\AzureBlobStorageService;
use AzureBlobStorage\Adapters\AzureBlobStorageAdapter;
use Mockery;

class AzureBlobStorageServiceTest extends TestCase
{
    protected $service;
    protected $adapter;

    protected function setUp(): void
    {
        $this->adapter = Mockery::mock(AzureBlobStorageAdapter::class);
        $this->service = new AzureBlobStorageService($this->adapter);
    }

    public function testWrite()
    {
        $this->adapter->shouldReceive('write')->andReturn(['path' => 'test.txt', 'contents' => 'content']);
        
        $result = $this->service->write('test.txt', 'content');
        $this->assertEquals('test.txt', $result['path']);
    }

    public function testRead()
    {
        $this->adapter->shouldReceive('read')->andReturn(['contents' => 'content']);
        
        $result = $this->service->read('test.txt');
        $this->assertEquals('content', $result['contents']);
    }

    public function testDelete()
    {
        $this->adapter->shouldReceive('delete')->andReturn(true);
        
        $result = $this->service->delete('test.txt');
        $this->assertTrue($result);
    }

    public function testSetMetadata()
    {
        $this->adapter->shouldReceive('setMetadata')->andReturn(true);
        
        $metadata = ['author' => 'John Doe'];
        $result = $this->service->setMetadata('test.txt', $metadata);
        $this->assertTrue($result);
    }
}

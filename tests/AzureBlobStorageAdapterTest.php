<?php

namespace AzureBlobStorage\Tests;

use PHPUnit\Framework\TestCase;
use AzureBlobStorage\Adapters\AzureBlobStorageAdapter;
use AzureBlobStorage\Services\OAuthService;
use League\Flysystem\Config;
use AzureBlobStorage\Exceptions\AzureBlobStorageException;
use Mockery;

class AzureBlobStorageAdapterTest extends TestCase
{
    protected $adapter;
    protected $oauthService;

    protected function setUp(): void
    {
        $this->oauthService = Mockery::mock(OAuthService::class);
        $this->adapter = new AzureBlobStorageAdapter('account_name', 'container', $this->oauthService);
    }

    public function testWrite()
    {
        $this->oauthService->shouldReceive('getAccessToken')->andReturn('token');
        
        $config = Mockery::mock(Config::class);

        $result = $this->adapter->write('test.txt', 'content', $config);
        $this->assertEquals('test.txt', $result['path']);
        $this->assertEquals('content', $result['contents']);
    }

    public function testRead()
    {
        $this->oauthService->shouldReceive('getAccessToken')->andReturn('token');
        
        $result = $this->adapter->read('test.txt');
        $this->assertArrayHasKey('contents', $result);
    }

    public function testDelete()
    {
        $this->oauthService->shouldReceive('getAccessToken')->andReturn('token');
        
        $result = $this->adapter->delete('test.txt');
        $this->assertTrue($result);
    }

    public function testSetMetadata()
    {
        $this->oauthService->shouldReceive('getAccessToken')->andReturn('token');
        
        $metadata = ['author' => 'John Doe'];
        $result = $this->adapter->setMetadata('test.txt', $metadata);
        $this->assertTrue($result);
    }
}

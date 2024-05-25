<?php

namespace AzureBlobStorage\Tests;

use PHPUnit\Framework\TestCase;
use AzureBlobStorage\Services\OAuthService;
use AzureBlobStorage\Exceptions\AzureBlobStorageException;
use Illuminate\Support\Facades\Http;
use Mockery;

class OAuthServiceTest extends TestCase
{
    protected $oauthService;

    protected function setUp(): void
    {
        $this->oauthService = new OAuthService('tenant_id', 'client_id', 'client_secret');
    }

    public function testGetAccessToken()
    {
        Http::fake([
            'https://login.microsoftonline.com/*' => Http::response(['access_token' => 'token'], 200)
        ]);

        $token = $this->oauthService->getAccessToken();
        $this->assertEquals('token', $token);
    }

    public function testGetAccessTokenFailure()
    {
        $this->expectException(AzureBlobStorageException::class);

        Http::fake([
            'https://login.microsoftonline.com/*' => Http::response([], 400)
        ]);

        $this->oauthService->getAccessToken();
    }
}

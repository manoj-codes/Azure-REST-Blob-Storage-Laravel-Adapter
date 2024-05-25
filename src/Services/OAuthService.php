<?php

namespace AzureBlobStorage\Services;

use Illuminate\Support\Facades\Http;
use AzureBlobStorage\Exceptions\AzureBlobStorageException;

class OAuthService
{
    protected $tenantId;
    protected $clientId;
    protected $clientSecret;

    public function __construct(string $tenantId, string $clientId, string $clientSecret)
    {
        $this->tenantId = $tenantId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get the OAuth 2.0 access token.
     *
     * @return string
     * @throws AzureBlobStorageException
     */
    public function getAccessToken(): string
    {
        try {
            $tokenUrl = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";
            $response = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'https://storage.azure.com/.default'
            ]);

            if ($response->failed()) {
                throw new AzureBlobStorageException('Failed to obtain access token: ' . $response->body());
            }

            return $response->json()['access_token'];
        } catch (Exception $e) {
            throw new AzureBlobStorageException('Error obtaining access token: ' . $e->getMessage());
        }
    }
}

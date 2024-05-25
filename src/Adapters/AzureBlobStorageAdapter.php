<?php

namespace AzureBlobStorage\Adapters;

use AzureBlobStorage\Contracts\BlobStorageAdapterInterface;
use League\Flysystem\Config;
use Illuminate\Support\Facades\Http;
use AzureBlobStorage\Services\OAuthService;
use AzureBlobStorage\Exceptions\AzureBlobStorageException;
use Exception;

class AzureBlobStorageAdapter implements BlobStorageAdapterInterface
{
    protected $accountName;
    protected $container;
    protected $baseUri;
    protected $httpClient;
    protected $oauthService;

    public function __construct(string $accountName, string $container, OAuthService $oauthService)
    {
        $this->accountName = $accountName;
        $this->container = $container;
        $this->baseUri = "https://{$accountName}.blob.core.windows.net/{$container}";
        $this->oauthService = $oauthService;
        $this->httpClient = Http::withOptions([
            'headers' => [
                'x-ms-version' => '2020-10-02',
                'x-ms-date' => gmdate('D, d M Y H:i:s T')
            ]
        ]);
    }

    /**
     * Write a new file to Azure Blob Storage.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array
     * @throws AzureBlobStorageException
     */
    public function write($path, $contents, Config $config): array
    {
        try {
            $url = $this->baseUri . '/' . $path;
            $headers = [
                'Content-Type' => 'application/octet-stream',
                'x-ms-blob-type' => 'BlockBlob',
                'Authorization' => $this->generateAuthorizationHeader()
            ];

            $response = $this->httpClient->put($url, $contents, ['headers' => $headers]);

            if ($response->failed()) {
                throw new AzureBlobStorageException('Failed to write file: ' . $response->body());
            }

            return compact('path', 'contents');
        } catch (Exception $e) {
            throw new AzureBlobStorageException('Error writing file: ' . $e->getMessage());
        }
    }

    /**
     * Write a new file to Azure Blob Storage using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config
     * @return array
     * @throws AzureBlobStorageException
     */
    public function writeStream($path, $resource, Config $config): array
    {
        try {
            $contents = stream_get_contents($resource);
            return $this->write($path, $contents, $config);
        } catch (Exception $e) {
            throw new AzureBlobStorageException('Error writing stream: ' . $e->getMessage());
        }
    }

    /**
     * Read a file from Azure Blob Storage.
     *
     * @param string $path
     * @return array
     * @throws AzureBlobStorageException
     */
    public function read($path): array
    {
        try {
            $url = $this->baseUri . '/' . $path;
            $headers = [
                'Authorization' => $this->generateAuthorizationHeader()
            ];

            $response = $this->httpClient->get($url, ['headers' => $headers]);

            if ($response->failed()) {
                throw new AzureBlobStorageException('Failed to read file: ' . $response->body());
            }

            return ['contents' => $response->body()];
        } catch (Exception $e) {
            throw new AzureBlobStorageException('Error reading file: ' . $e->getMessage());
        }
    }

    /**
     * Read a file from Azure Blob Storage as a stream.
     *
     * @param string $path
     * @return array
     * @throws AzureBlobStorageException
     */
    public function readStream($path): array
    {
        try {
            $result = $this->read($path);
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $result['contents']);
            rewind($stream);

            return ['stream' => $stream];
        } catch (Exception $e) {
            throw new AzureBlobStorageException('Error reading stream: ' . $e->getMessage());
        }
    }

    /**
     * Delete a file from Azure Blob Storage.
     *
     * @param string $path
     * @return bool
     * @throws AzureBlobStorageException
     */
    public function delete($path): bool
    {
        try {
            $url = $this->baseUri . '/' . $path;
            $headers = [
                'Authorization' => $this->generateAuthorizationHeader()
            ];

            $response = $this->httpClient->delete($url, ['headers' => $headers]);

            if ($response->failed()) {
                throw new AzureBlobStorageException('Failed to delete file: ' . $response->body());
            }

            return true;
        } catch (Exception $e) {
            throw new AzureBlobStorageException('Error deleting file: ' . $e->getMessage());
        }
    }

    /**
     * Set metadata for a blob in Azure Blob Storage.
     *
     * @param string $path
     * @param array $metadata
     * @return bool
     * @throws AzureBlobStorageException
     */
    public function setMetadata($path, array $metadata): bool
    {
        try {
            $url = $this->baseUri . '/' . $path;
            $headers = [
                'x-ms-blob-type' => 'BlockBlob',
                'Authorization' => $this->generateAuthorizationHeader()
            ];

            foreach ($metadata as $key => $value) {
                $headers['x-ms-meta-' . $key] = $value;
            }

            $response = $this->httpClient->put($url, null, ['headers' => $headers]);

            if ($response->failed()) {
                throw new AzureBlobStorageException('Failed to set metadata: ' . $response->body());
            }

            return $response->successful();
        } catch (Exception $e) {
            throw new AzureBlobStorageException('Error setting metadata: ' . $e->getMessage());
        }
    }

    /**
     * Generate the Authorization header for Azure Blob Storage requests.
     *
     * @return string
     * @throws AzureBlobStorageException
     */
    protected function generateAuthorizationHeader(): string
    {
        try {
            $token = $this->oauthService->getAccessToken();
            return 'Bearer ' . $token;
        } catch (Exception $e) {
            throw new AzureBlobStorageException('Error generating authorization header: ' . $e->getMessage());
        }
    }
}

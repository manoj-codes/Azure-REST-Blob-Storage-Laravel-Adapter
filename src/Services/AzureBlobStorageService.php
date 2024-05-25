<?php

namespace AzureBlobStorage\Services;

use AzureBlobStorage\Contracts\BlobStorageAdapterInterface;
use League\Flysystem\Config;
use AzureBlobStorage\Exceptions\AzureBlobStorageException;

class AzureBlobStorageService
{
    protected $adapter;

    public function __construct(BlobStorageAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @return array
     * @throws AzureBlobStorageException
     */
    public function write($path, $contents)
    {
        try {
            $config = new Config();
            return $this->adapter->write($path, $contents, $config);
        } catch (AzureBlobStorageException $e) {
            throw new AzureBlobStorageException('Failed to write file: ' . $e->getMessage());
        }
    }

    /**
     * Read a file.
     *
     * @param string $path
     * @return array
     * @throws AzureBlobStorageException
     */
    public function read($path)
    {
        try {
            return $this->adapter->read($path);
        } catch (AzureBlobStorageException $e) {
            throw new AzureBlobStorageException('Failed to read file: ' . $e->getMessage());
        }
    }

    /**
     * Delete a file.
     *
     * @param string $path
     * @return bool
     * @throws AzureBlobStorageException
     */
    public function delete($path)
    {
        try {
            return $this->adapter->delete($path);
        } catch (AzureBlobStorageException $e) {
            throw new AzureBlobStorageException('Failed to delete file: ' . $e->getMessage());
        }
    }

    /**
     * Set metadata for a blob.
     *
     * @param string $path
     * @param array $metadata
     * @return bool
     * @throws AzureBlobStorageException
     */
    public function setMetadata($path, array $metadata)
    {
        try {
            return $this->adapter->setMetadata($path, $metadata);
        } catch (AzureBlobStorageException $e) {
            throw new AzureBlobStorageException('Failed to set metadata: ' . $e->getMessage());
        }
    }
}

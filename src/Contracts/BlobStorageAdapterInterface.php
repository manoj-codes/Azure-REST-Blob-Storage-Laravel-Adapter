<?php

namespace AzureBlobStorage\Contracts;

use League\Flysystem\Config;

interface BlobStorageAdapterInterface
{
    public function write($path, $contents, Config $config);
    public function writeStream($path, $resource, Config $config);
    public function read($path);
    public function readStream($path);
    public function delete($path);
    public function setMetadata($path, array $metadata);
}

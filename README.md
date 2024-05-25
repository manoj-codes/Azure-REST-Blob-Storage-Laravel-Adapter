# 🌟 Laravel - Azure Blob Storage

[![Latest Version on Packagist](https://img.shields.io/packagist/v/yourname/azure-blob-storage.svg?style=flat-square)](https://packagist.org/packages/yourname/azure-blob-storage)
[![Total Downloads](https://img.shields.io/packagist/dt/yourname/azure-blob-storage.svg?style=flat-square)](https://packagist.org/packages/yourname/azure-blob-storage)
[![Build Status](https://img.shields.io/travis/yourname/azure-blob-storage/master.svg?style=flat-square)](https://travis-ci.org/yourname/azure-blob-storage)
[![Quality Score](https://img.shields.io/scrutinizer/g/yourname/azure-blob-storage.svg?style=flat-square)](https://scrutinizer-ci.com/g/yourname/azure-blob-storage)
[![License](https://img.shields.io/packagist/l/yourname/azure-blob-storage.svg?style=flat-square)](https://packagist.org/packages/yourname/azure-blob-storage)

A Laravel package for interacting with Azure Blob Storage using OAuth 2.0 for secure authentication.

## 📦 Installation

You can install the package via Composer:

`bash
composer require yourname/azure-blob-storage
`

## ⚙️ Configuration

Publish the configuration file to customize Azure Blob Storage settings:

`bash
php artisan vendor:publish --provider="AzureBlobStorage\Providers\AzureBlobStorageServiceProvider" --tag=config
`

Set the necessary environment variables in your `.env` file:

`plaintext
AZURE_STORAGE_ACCOUNT_NAME=your_account_name
AZURE_STORAGE_CONTAINER=your_container_name
AZURE_TENANT_ID=your_tenant_id
AZURE_CLIENT_ID=your_client_id
AZURE_CLIENT_SECRET=your_client_secret
`

Add a new disk configuration for azureblobstorage-driver in config/filesystems.php:

`php

'disks' => [
    // Other disk configurations...

    'azureblobstorage-driver' => [
        'driver' => 'azureblobstorage-driver',
        'account_name' => env('AZURE_STORAGE_ACCOUNT_NAME'),
        'container' => env('AZURE_STORAGE_CONTAINER'),
        'tenant_id' => env('AZURE_TENANT_ID'),
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
    ],
],
`

## 🚀 Usage

Here's how you can use the package to interact with Azure Blob Storage using the Storage facade:
Writing a File

To write a file to Azure Blob Storage, use the put method:

`php

use Illuminate\Support\Facades\Storage;

Storage::disk('azureblobstorage-driver')->put('lorem.txt', 'Lorem ipsum dolor sit amet.');
`

### Reading a File

To read a file from Azure Blob Storage, use the get method:

`php

use Illuminate\Support\Facades\Storage;

$content = Storage::disk('azureblobstorage-driver')->get('lorem.txt');
echo $content;
`

### Deleting a File

To delete a file from Azure Blob Storage, use the delete method:

`php

use Illuminate\Support\Facades\Storage;

Storage::disk('azureblobstorage-driver')->delete('lorem.txt');
`

## Example in a Controller

Here's a complete example of how to use the Storage facade in a Laravel controller to upload a file to Azure Blob Storage:

`php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|file',
        ]);

        // Get the uploaded file
        $file = $request->file('file');

        // Define the file path in the storage
        $filePath = 'uploads/' . $file->getClientOriginalName();

        // Store the file in Azure Blob Storage
        Storage::disk('azureblobstorage-driver')->put($filePath, file_get_contents($file));

        return response()->json(['message' => 'File uploaded successfully.']);
    }
}
`

## 🚀 Usage

Here's how you can use the package to interact with Azure Blob Storage:

### Write a File

`php
use AzureBlobStorage\Services\AzureBlobStorageService;

$azureBlobService = app(AzureBlobStorageService::class);
$result = $azureBlobService->write('path/to/file.txt', 'file contents');
`

### Read a File

`php
$result = $azureBlobService->read('path/to/file.txt');
`

### Delete a File

`php
$result = $azureBlobService->delete('path/to/file.txt');
`

### Set Metadata for a Blob

`php
$metadata = ['author' => 'John Doe'];
$result = $azureBlobService->setMetadata('path/to/file.txt', $metadata);
`

## 🛠️ Testing

To run the tests, use the following command:

`bash
vendor/bin/phpunit
`


## 🌟 Features

- 📄 Write, read, and delete files in Azure Blob Storage.
- 🔒 Secure OAuth 2.0 authentication.
- 🏷️ Set metadata for blobs.
- 🛠️ Easy configuration and usage.
- 📦 Comprehensive tests.

## 🚀 Getting Started

### Prerequisites

- PHP 7.4 or higher
- Laravel 8 or higher
- Azure Blob Storage account

### Installation Steps

1. Install the package via Composer.
2. Publish the configuration file.
3. Set up environment variables.
4. Use the service in your application.

## 📚 Additional Resources

- [Azure Blob Storage Documentation](https://docs.microsoft.com/en-us/azure/storage/blobs/)
- [Laravel Documentation](https://laravel.com/docs)
- [OAuth 2.0 Documentation](https://oauth.net/2/)

## 📬 Support

For support, please open an issue on GitHub.

## ❤️ Acknowledgements

Special thanks to all contributors and the Laravel community.
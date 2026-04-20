<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;

class ApkVerifier
{
    public static function hash(UploadedFile $file)
    {
        return hash_file('sha256', $file->getRealPath());
    }

    public static function verify($downloadedPath, $expectedHash)
    {
        return hash_file('sha256', $downloadedPath) === $expectedHash;
    }
}

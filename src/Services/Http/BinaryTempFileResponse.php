<?php

namespace App\Services\Http;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BinaryTempFileResponse extends BinaryFileResponse
{
    public function __destruct()
    {
        unlink($this->getFile()->getRealPath());
    }
}

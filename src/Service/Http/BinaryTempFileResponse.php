<?php

namespace App\Service\Http;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BinaryTempFileResponse extends BinaryFileResponse
{
    public function __destruct()
    {
        unlink($this->getFile()->getRealPath());
    }
}

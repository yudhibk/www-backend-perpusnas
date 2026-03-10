<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class XFrameOptions implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        header('X-Frame-Options: ');
        header("Content-Security-Policy: frame-ancestors *;");
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}

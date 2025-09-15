<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ClientCacheFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip cache untukk user yang sudah terautentikasi
        if (session()->has('user_name')) {
            return;
        }

        // Cek ETag validation
        $etag = $request->getHeaderLine('If-None-Match');
        if ($etag && $this->isValidEtag($etag)) {
            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_NOT_MODIFIED)
                ->setJSON();
        }

        // Cek Last-Modified validation
        $ifModifiedSince = $request->getHeaderLine('If-Modified-Since');
        if ($ifModifiedSince && $this->isNotModified($ifModifiedSince)) {
            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_NOT_MODIFIED)
                ->setJSON();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Skip cache untuk certain conritions
        if (
            $request->getMethod() !== 'GET' ||
            $response->getStatusCode() !== 200 ||
            session()->has('user_name')
        ) {

            return $response;
        }

        $maxAge = $arguments[0] ?? 3600; //Default cache 1 jam

        if (!empty($arguments)) {
            $maxAge = (int) $arguments[0] ?? 3600;
        }

        $response->setCache([
            'max-age' => $maxAge,
            's-max-age' => $maxAge,
            'public' => true,
            'private' => false
        ]);

        // Generate ETag dari content
        $content = $response->getBody();
        if (!empty($content)) {
            $response->setHeader('Etag', md5($content));
        }

        return $response;
    }

    private function isValidEtag($clientEtag)
    {
        // Implement your ETag validation logic
        return $clientEtag === $this->generateCurrentEtag();
    }

    private function isNotModified($ifModifiedSince)
    {
        // Implement your last-modified validation logic
        $lastModified = strtotime('2024-01-01 00:00:00'); // Contoh
        return strtotime($ifModifiedSince) >= $lastModified;
    }

    private function generateCurrentEtag()
    {
        // Generate ETag berdasarkan konten atau timestamp
        return md5(time() . '--' . date('Y-m-d H:00:00')); // Change every hour
    }
}

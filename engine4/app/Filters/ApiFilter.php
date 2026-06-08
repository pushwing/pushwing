<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * API 공통 필터
 * - CORS 헤더 설정
 * - Content-Type 강제 application/json
 */
class ApiFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // preflight OPTIONS 요청 즉시 응답
        if ($request->getMethod() === 'options') {
            $response = service('response');
            return $response
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->setStatusCode(200);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Content-Type', 'application/json; charset=UTF-8');
    }
}

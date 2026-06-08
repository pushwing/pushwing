<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Rate Limit 필터
 *
 * 분당 최대 요청 수를 초과하면 429를 반환한다.
 * arguments[0] = 분당 허용 횟수 (기본 60)
 */
class RateLimitFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $maxPerMinute = (int) ($arguments[0] ?? 60);

        $throttler = service('throttler');
        $ip        = $request->getIPAddress();

        // 버킷 키에 URI를 포함시켜 엔드포인트별 독립 제한
        $key = 'ratelimit:' . md5($request->getUri()->getPath()) . ':' . $ip;

        if (! $throttler->check($key, $maxPerMinute, MINUTE)) {
            return service('response')
                ->setStatusCode(429)
                ->setJSON([
                    'message' => 'Too many requests. Please try again later.',
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

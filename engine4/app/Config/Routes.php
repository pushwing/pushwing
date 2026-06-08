<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Pushwing API v1
$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
    // 앱 시작 시 플래그 조회 (광고 on/off, 앱 버전, 업데이트 여부)
    $routes->get('go', 'Api::go');

    // 디바이스 등록 (휴대폰번호 + FCM/GCM token)
    $routes->post('sd', 'Api::sd');

    // 푸시 내용 조회
    $routes->get('fd/(:num)', 'Api::fd/$1');

    // 광고 노출 리포트
    $routes->post('vr', 'Api::vr');

    // 광고 클릭 리포트
    $routes->post('cr', 'Api::cr');

    // 공지사항 목록
    $routes->get('nl', 'Api::nl');

    // 공지사항 상세
    $routes->get('nv/(:num)', 'Api::nv/$1');
});

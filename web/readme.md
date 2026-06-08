# web/ — 관리자 사이트 보안 점검 결과

CodeIgniter 2.x 기반의 관리자 사이트(`web/`)에 대한 보안 점검 및 수정 내역입니다.

---

## 목차

1. [점검 개요](#점검-개요)
2. [발견된 취약점 및 수정 내역](#발견된-취약점-및-수정-내역)
   - [config.php — 보안 설정 강화](#1-configphp--보안-설정-강화)
   - [database.php — DB 드라이버 및 디버그 설정](#2-databasephp--db-드라이버-및-디버그-설정)
   - [admin/client.php — SQL 인젝션](#3-adminclientphp--sql-인젝션)
   - [migrate.php — 인증 없는 DB 마이그레이션 실행](#4-migratephp--인증-없는-db-마이그레이션-실행)
   - [push.php — 인증 없는 푸시 전송 실행](#5-pushphp--인증-없는-푸시-전송-실행)
   - [push_m.php — SSL 검증 비활성화](#6-push_mphp--ssl-검증-비활성화)
   - [로그 파일 Git 추적](#7-로그-파일-git-추적)
3. [취약점 요약표](#취약점-요약표)
4. [잔여 검토 사항](#잔여-검토-사항)

---

## 점검 개요

| 항목 | 내용 |
|------|------|
| 대상 | `web/` — CodeIgniter 2.x 관리자 사이트 |
| PHP 버전 | 8.x (레거시 CI2 코드, 일부 비호환 요소 존재) |
| 점검 일시 | 2026-06-08 |
| 발견 건수 | 🔴 심각 3건 / 🟠 경고 3건 / 🟡 주의 1건 |

---

## 발견된 취약점 및 수정 내역

### 1. `config.php` — 보안 설정 강화

**경로:** `web/application/config/config.php`

| 설정 항목 | 수정 전 | 수정 후 | 설명 |
|-----------|---------|---------|------|
| `encryption_key` | `'Rhctkfvmfhwprxm'` | `''` | 하드코딩된 암호화 키 제거. 배포 시 환경별 키 설정 필요 |
| `log_threshold` | `0` | `1` | 에러 로그 활성화 (기존 로깅 비활성화로 이상 징후 탐지 불가) |
| `sess_encrypt_cookie` | `FALSE` | `TRUE` | 세션 쿠키 암호화 활성화 |
| `sess_use_database` | `FALSE` | `TRUE` | 세션을 DB에 저장 (서버 측 세션 관리) |
| `sess_match_ip` | `FALSE` | `TRUE` | 세션-IP 바인딩으로 세션 탈취 방지 |
| `cookie_secure` | `FALSE` | `TRUE` | HTTPS 전용 쿠키 전송 강제 |
| `global_xss_filtering` | `FALSE` | `TRUE` | 전역 XSS 필터링 활성화 |
| `csrf_protection` | `FALSE` | `TRUE` | CSRF 토큰 검증 활성화 |

> **주의:** `encryption_key`는 빈 문자열로 설정됐으므로 배포 환경의 `config.php`에 안전한 랜덤 키를 직접 입력해야 합니다.

---

### 2. `database.php` — DB 드라이버 및 디버그 설정

**경로:** `web/application/config/database.php`  
**등급:** 🟠 경고

| 설정 항목 | 수정 전 | 수정 후 | 설명 |
|-----------|---------|---------|------|
| `dbdriver` | `'mysql'` | `'mysqli'` | `mysql` 드라이버는 PHP 7에서 제거됨. `mysqli`로 변경 |
| `db_debug` | `TRUE` | `FALSE` | DB 에러 메시지가 브라우저에 노출되어 스키마/쿼리 정보 유출 가능 |

---

### 3. `admin/client.php` — SQL 인젝션

**경로:** `web/application/controllers/admin/client.php`  
**등급:** 🔴 심각

GRANT SQL 구문에 사용자 입력값을 검증 없이 직접 삽입하여 DB 권한 탈취가 가능한 취약점이었습니다.

**수정 전:**
```php
$sql = "GRANT INSERT ON pushwing.push_wait TO `{$post['mysql_id']}`@`{$post['ip_address']}`
        IDENTIFIED BY '{$post['mysql_pass']}' WITH GRANT OPTION";
$this->db->query($sql);
```

**수정 후:**
```php
$mysql_id   = preg_replace('/[^a-zA-Z0-9_]/', '', $post['mysql_id']);
$ip_address = filter_var($post['ip_address'], FILTER_VALIDATE_IP) ? $post['ip_address'] : '';
$mysql_pass = $this->db->escape_str($post['mysql_pass']);

if ($mysql_id && $ip_address) {
    $sql = "GRANT INSERT ON pushwing.push_wait TO `{$mysql_id}`@`{$ip_address}`
            IDENTIFIED BY '{$mysql_pass}' WITH GRANT OPTION";
    $this->db->query($sql);
    $this->db->query("FLUSH PRIVILEGES");
}
```

- `mysql_id`: 영문·숫자·언더스코어만 허용 (화이트리스트)
- `ip_address`: `FILTER_VALIDATE_IP`로 유효한 IP만 허용
- `mysql_pass`: `escape_str()`로 이스케이프

추가로 하드코딩된 이메일 주소(`blumine@naver.com`)를 포함한 `test_mail()` 공개 메서드를 제거했습니다.

---

### 4. `migrate.php` — 인증 없는 DB 마이그레이션 실행

**경로:** `web/application/controllers/migrate.php`  
**등급:** 🔴 심각

`__construct()`가 없어 누구든 `/migrate`에 접근하면 DB 마이그레이션이 실행되는 취약점이었습니다.

**수정 전:**
```php
class Migrate extends CI_Controller {
    public function index()
    {
        $this->load->library('migration');
        // ...
    }
}
```

**수정 후:**
```php
class Migrate extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        is_admin_login('9'); // 최고 관리자(auth_code=9)만 접근 허용
    }
    // ...
}
```

---

### 5. `push.php` — 인증 없는 푸시 전송 실행

**경로:** `web/application/controllers/push.php`  
**등급:** 🔴 심각

`send_all()`이 인증 없이 외부에서 호출 가능하여 전체 사용자에게 임의 푸시 전송이 가능한 취약점이었습니다.

**수정 전:**
```php
public function send_all()
{
    $this->load->model('push_m');
    $this->push_m->send_all();
}
```

**수정 후:**
```php
public function send_all()
{
    if ( ! $this->input->is_cli_request()) {
        is_admin_login('9');
    }
    $this->load->model('push_m');
    $this->push_m->send_all();
}
```

CLI(크론잡)에서의 호출은 허용하고, 웹 요청은 최고 관리자만 실행 가능하도록 제한했습니다.

---

### 6. `push_m.php` — SSL 검증 비활성화

**경로:** `web/application/models/push_m.php`  
**등급:** 🟠 경고

GCM/FCM 서버로의 cURL 요청에서 SSL 인증서 검증이 비활성화되어 있어 중간자 공격(MITM)에 노출될 수 있었습니다.

**수정 전:**
```php
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
```

**수정 후:**
```php
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
```

---

### 7. 로그 파일 Git 추적

**경로:** `web/application/logs/log-2013-12-02.php`  
**등급:** 🟡 주의

애플리케이션 로그 파일이 Git 저장소에 커밋되어 있어 서버 내부 경로, 에러 메시지 등이 코드 히스토리에 노출됐습니다.

**조치:**
- `log-2013-12-02.php` 파일 `git rm`으로 제거
- `web/application/logs/.gitignore` 추가하여 향후 로그 파일이 추적되지 않도록 설정

```gitignore
*.php
!index.html
```

---

## 취약점 요약표

| 등급 | 파일 | 취약점 | 조치 |
|------|------|--------|------|
| 🔴 심각 | `admin/client.php` | GRANT SQL 인젝션 | 화이트리스트 + IP 검증 + escape 처리 |
| 🔴 심각 | `migrate.php` | 인증 없는 DB 마이그레이션 실행 | 생성자에 관리자 인증 추가 |
| 🔴 심각 | `push.php` | 인증 없는 전체 푸시 전송 | CLI 판별 + 관리자 인증 추가 |
| 🟠 경고 | `config.php` | CSRF/XSS/세션 보안 설정 미적용 | 8개 보안 설정 활성화 |
| 🟠 경고 | `database.php` | DB 에러 외부 노출, 구식 드라이버 | db_debug=FALSE, mysqli 전환 |
| 🟠 경고 | `push_m.php` | SSL 검증 비활성화 (MITM 취약) | CURLOPT_SSL_VERIFYPEER=true |
| 🟡 주의 | `logs/` | 로그 파일 Git 추적 | git rm + .gitignore 추가 |

---

## 잔여 검토 사항

아래 항목은 이번 점검 범위에서 제외되었으나 추후 검토가 필요합니다.

| 항목 | 설명 |
|------|------|
| **GCM → FCM 마이그레이션** | `push_m.php`가 GCM(2019년 종료) 엔드포인트를 사용. FCM HTTP v1 API로 전환 필요 |
| **`engine4/`로 점진적 이전** | CI2 `web/`을 CI4 기반 `engine4/`로 단계적으로 이전하는 계획 수립 권장 |
| **GitHub Dependabot 경고** | 푸시 시 4 high + 1 moderate 의존성 취약점 감지됨. [GitHub Security 탭](https://github.com/pushwing/pushwing/security) 확인 필요 |
| **APNS 인증서 관리** | `apns-dev.pem`, `apns-pro.pem`이 저장소에 포함됨. 인증서는 별도 관리 권장 |
| **`tank_auth` 라이브러리 업데이트** | CI2용 인증 라이브러리로 장기 미유지. 보안 패치 여부 확인 필요 |

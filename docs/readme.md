# Pushwing 문서

자체 앱이 없는 웹사이트(동호회, 커뮤니티 등)가 공용 앱을 통해 스마트폰 푸시 알림을 발송할 수 있게 해주는 서비스입니다.
파트너 웹사이트에서 클라이언트 등록을 통해 해당 사이트 회원들에게 푸시를 보낼 수 있습니다.

---

## 목차

1. [아키텍처 구조](#아키텍처-구조)
2. [컴포넌트별 설명](#컴포넌트별-설명)
3. [푸시 발송 플로우](#푸시-발송-플로우)
4. [기술 스택](#기술-스택)
5. [설치 및 설정 (레거시 engine/)](#설치-및-설정-레거시-engine)
6. [설치 및 설정 (engine4/ — 권장)](#설치-및-설정-engine4--권장)
7. [API 엔드포인트](#api-엔드포인트)
8. [보안 및 성능 점검 내역](#보안-및-성능-점검-내역)
9. [주의사항 및 보안](#주의사항-및-보안)
10. [기여자](#기여자)

---

## 아키텍처 구조

```
pushwing/
├── android/        # Android 클라이언트 앱 (Eclipse 프로젝트)
├── engine/         # RESTful API 서버 (순수 PHP — 레거시)
├── engine4/        # RESTful API 서버 (CodeIgniter 4 — 권장)
├── web/            # 관리 웹사이트 (CodeIgniter 2.x 기반)
└── plgins/
    ├── codeigniter/ # CI용 푸시 발송 라이브러리
    └── gnu/         # 그누보드 연동 플러그인
```

---

## 컴포넌트별 설명

### Android 앱 (`android/`)

- **GCM (Google Cloud Messaging)** 기반 푸시 수신 — 현재는 FCM으로 교체 필요
- 앱 설치 시 휴대폰 번호 + GCM registration ID를 서버에 등록
- 푸시 수신 시 SQLite에 저장하고 알림 표시 (소리/진동 설정 가능)
- Google AdMob 광고 탑재

### REST API 엔진 — 레거시 (`engine/`)

순수 PHP REST API. SQL Injection, 크리덴셜 하드코딩 등 보안 이슈가 있으므로 `engine4/` 사용을 권장합니다.

### REST API 엔진 — 권장 (`engine4/`)

CodeIgniter 4.7.3 기반 REST API. 레거시 `engine/`의 보안 이슈를 모두 수정한 버전입니다.

| 개선 항목 | 내용 |
|----------|------|
| SQL Injection | CI4 Query Builder (prepared statements) 적용 |
| 크리덴셜 관리 | DB 접속정보·FCM 키를 `.env` 환경변수로 분리 |
| 입력 유효성 검사 | CI4 Validation으로 모든 파라미터 검증 |
| `magic_quotes_gpc` | PHP 8 + CI4 입력 처리로 완전 제거 |
| CORS | `ApiFilter`로 공통 처리 |

```
engine4/
├── app/
│   ├── Controllers/Api.php       # 7개 엔드포인트 (go/sd/fd/vr/cr/nl/nv)
│   ├── Models/
│   │   ├── PushModel.php         # 디바이스 등록, 푸시 조회
│   │   ├── ChecksModel.php       # 플래그/버전 조회
│   │   ├── AdReportModel.php     # 광고 노출/클릭 리포트
│   │   └── NoticeModel.php       # 공지사항
│   ├── Filters/ApiFilter.php     # CORS + Content-Type 공통 처리
│   └── Config/Routes.php         # RESTful 라우팅
├── .env.example                  # 설정 템플릿
└── .env                          # 실제 설정 (gitignore)
```

### 웹 관리 서버 (`web/`)

- **CodeIgniter 2.x** 프레임워크
- 파트너 웹사이트 관리, 문의 접수, 푸시 발송
- `push_wait` 테이블 큐 방식으로 iOS(APNS) 및 Android(GCM) 발송
- tank_auth 기반 관리자 인증

### 파트너용 플러그인 (`plgins/`)

파트너 웹사이트에서 한 메서드로 푸시를 발송할 수 있는 라이브러리.

```php
$this->pushwing->send_push($hp, $subject, $contents, $url);
```

---

## 푸시 발송 플로우

```
파트너 웹사이트
 → pushwing 라이브러리로 push_wait 테이블에 INSERT
 → /push/send_all 크론 실행
 → push_m 모델이 push_wait를 읽어 GCM/APNS 발송
 → push_end 테이블로 이동 (발송 기록)
 → 앱이 수신 → 알림 표시
```

---

## 기술 스택

| 레이어 | 기술 |
|--------|------|
| Android 앱 | Java, GCM, Volley, AdMob, SQLite |
| REST API (레거시) | PHP 5.x, MySQL (순수 PHP) |
| REST API (권장) | PHP 8.x, CodeIgniter 4, MySQL |
| 웹 서버 | CodeIgniter 2.x, PHP, MySQL, tank_auth |
| iOS 푸시 | APNS (인증서 방식) |
| Android 푸시 | GCM → FCM으로 마이그레이션 필요 |

---

## 설치 및 설정 (레거시 `engine/`)

> 신규 구축 시에는 아래 [engine4/ 설정](#설치-및-설정-engine4--권장)을 사용하세요.

### 1. DB 설정

`engine/eg.php` 에서 DB 접속 정보를 직접 수정합니다.

```php
$this->db = new MySQL('데이터베이스명', '아이디', '비밀번호', 'localhost');
```

`web/application/config/database.php` 에서 CodeIgniter DB 설정을 합니다.

### 2. GCM 키 설정

`web/application/models/push_m.php` 의 `init_curl()` 에서 GCM API 키를 입력합니다.

```php
$headers[] = 'Authorization:key=YOUR_GCM_KEY';
```

### 3. iOS APNS 설정

`web/application/config/ios.php` 에서 APNS 인증서 경로를 설정합니다.

`web/apns-dev.pem`, `web/apns-pro.pem` 위치에 인증서를 배치합니다.

### 4. 크론 등록

```
* * * * * curl http://your-domain.com/push/send_all
```

### 5. 파트너 웹사이트 연동 (CodeIgniter)

`plgins/codeigniter/pushwing/` 디렉토리를 CI 프로젝트에 적용하고 설정합니다.

```php
// config/pushwing.php
$config['pushwing_server']   = 'pushwing DB 서버 주소';
$config['pushwing_id']       = 'DB 아이디';
$config['pushwing_password'] = 'DB 비밀번호';
$config['client_id']         = '발급받은 클라이언트 ID';
```

---

## 설치 및 설정 (`engine4/` — 권장)

### 1. 의존성 설치

```bash
cd engine4
composer install
```

### 2. 환경 설정 파일 생성

```bash
cp .env.example .env
```

`.env` 파일을 열고 실제 값으로 수정합니다.

```dotenv
CI_ENVIRONMENT = production

app.baseURL = 'https://your-domain.com/engine4/public/'

database.default.hostname = localhost
database.default.database = pushwing
database.default.username = DB_USERNAME
database.default.password = DB_PASSWORD

# FCM Server Key (Firebase Console > 프로젝트 설정 > 클라우드 메시징)
pushwing.fcmServerKey = YOUR_FCM_SERVER_KEY_HERE
```

### 3. 웹 서버 설정

DocumentRoot를 `engine4/public/` 으로 지정합니다.

Apache `.htaccess` 는 `engine4/public/.htaccess` 에 이미 포함되어 있습니다.

### 4. 크론 등록

```
* * * * * curl https://your-domain.com/engine4/public/push/send_all
```

---

## API 엔드포인트

모든 응답은 `application/json` 형식입니다.

### `GET /api/go` — 앱 시작 플래그 조회

앱 실행 시 광고 on/off 여부, 앱 버전, 업데이트 필요 여부를 반환합니다.

**응답 예시**

```json
{
  "code_value": "1",
  "and_ver": "1.0.0",
  "ios_ver": "1.0.0",
  "update": "0"
}
```

---

### `POST /api/sd` — 디바이스 등록

앱 설치 또는 토큰 갱신 시 호출합니다. 최초 등록 시 환영 푸시가 `push_wait`에 등록됩니다.

**파라미터**

| 이름 | 필수 | 설명 |
|------|------|------|
| `hp` | Y | 휴대폰 번호 |
| `cd` | Y | FCM/GCM device token |
| `os` | Y | OS 종류 (`1`=iOS, `2`=Android) |

**응답 예시**

```json
{ "message": "success" }
```

---

### `GET /api/fd/{id}` — 푸시 내용 조회

device token 소유권 검증 후 반환합니다. `cd`가 없거나 소유자가 다르면 404를 반환합니다.

| 파라미터 | 위치 | 필수 | 설명 |
|---------|------|------|------|
| `id` | URL | Y | push_end ID |
| `cd` | Query | Y | FCM device token (소유권 검증용) |

**응답 예시**

```json
{
  "id": "42",
  "subject": "새 댓글이 달렸습니다",
  "contents": "...",
  "url": "https://example.com/...",
  "send_timestamp": "1686000000"
}
```

---

### `POST /api/vr` — 광고 노출 리포트

**파라미터**

| 이름 | 필수 | 설명 |
|------|------|------|
| `sid` | Y | 광고 번호 |
| `cd`  | Y | 클라이언트 코드 |
| `ty`  | Y | 광고 위치 (`1`=초기화면, `2`=푸시내용) |
| `av`  | Y | 앱 버전 |
| `hp`  | Y | 휴대폰 번호 |
| `sq`  | Y | 푸시 번호 |

---

### `POST /api/cr` — 광고 클릭 리포트

파라미터는 `/api/vr` 과 동일합니다.

---

### `GET /api/nl` — 공지사항 목록

최근 10건을 반환합니다.

**응답 예시**

```json
[
  { "id": "5", "subject": "서비스 점검 안내", "hit": "120", "reg_date": "2014-03-01 10:00:00" }
]
```

---

### `GET /api/nv/{id}` — 공지사항 상세

```json
{
  "id": "5",
  "subject": "서비스 점검 안내",
  "contents": "...",
  "hit": "120",
  "reg_date": "2014-03-01 10:00:00"
}
```

---

## 보안 및 성능 점검 내역

`engine4/` 에 대해 보안·성능 점검을 수행하여 아래 이슈를 수정했습니다.

### 🔴 보안 (High)

| 항목 | 내용 | 수정 방법 |
|------|------|----------|
| **IDOR** | `/api/fd/{id}` — 인증 없이 타인의 푸시 내용 열람 가능 | `cd`(device token) 파라미터 필수화, `push_db` 역조회로 소유자 검증 |
| **보안 헤더 누락** | X-Frame-Options, X-Content-Type-Options 등 미설정 | `secureheaders` 필터 전역 활성화 |
| **비정상 문자 미차단** | NUL 바이트 등 비정상 문자 허용 | `invalidchars` 필터 전역 활성화 |
| **HTTPS 미강제** | 평문 HTTP 요청 허용 | `forcehttps` 활성화, 개발 시 `.env`로 우회 |

### 🟠 보안 (Medium)

| 항목 | 내용 | 수정 방법 |
|------|------|----------|
| **Rate Limiting 없음** | `/api/sd` DB 플러딩, `/api/vr·cr` 리포트 어뷰징 가능 | `RateLimitFilter` 생성 — `/api/sd` 분당 10회, `/api/vr·cr` 분당 30회 제한 |
| **전화번호 포맷 미검증** | `hp` 파라미터에 임의 문자열 허용 | `regex_match[/^01[016789][0-9]{7,8}$/]` 규칙 추가 |
| **Race condition (TOCTOU)** | SELECT → INSERT/UPDATE 사이 동시 요청 시 중복 INSERT 가능 | `INSERT ON DUPLICATE KEY UPDATE` 단일 원자 쿼리로 교체 |

### 🟡 성능

| 항목 | 내용 | 수정 방법 |
|------|------|----------|
| **쿼리 중복** | `ChecksModel::getFlags()` 동일 테이블에 쿼리 3회 | `WHERE IN` 1회 쿼리 + `array_column()` 매핑 |
| **캐시 없음** | `/api/go`, `/api/nl` 매 요청마다 DB 조회 | `/api/go` 5분, `/api/nl` 10분 캐싱 적용 |
| **모델 반복 생성** | 컨트롤러 메서드마다 `new Model()` 호출 | `initController()` 에서 1회 생성으로 통합 |

### 필터 적용 현황

```
모든 요청:   invalidchars (before) → secureheaders (after)
api/*:       apif — CORS + Content-Type (before/after)
POST api/sd: ratelimit:10 — 분당 10회
POST api/vr: ratelimit:30 — 분당 30회
POST api/cr: ratelimit:30 — 분당 30회
전체:        forcehttps (HTTPS 강제)
```

---

## 주의사항 및 보안

### GCM → FCM 마이그레이션

GCM(Google Cloud Messaging)은 2019년 4월 종료되었습니다. Android 푸시 발송을 위해 **FCM(Firebase Cloud Messaging)** 으로 마이그레이션이 필요합니다.

- Firebase Console에서 프로젝트 생성 후 서버 키 발급
- `engine4/.env` 의 `pushwing.fcmServerKey` 에 설정
- Android 앱의 GCMBaseIntentService → Firebase SDK로 교체 필요

### 레거시 `engine/` 보안 이슈

`engine/`은 오픈소스 공개용 코드이며 운영 환경에서 직접 사용 시 아래 이슈를 반드시 수정해야 합니다.

| 이슈 | 위치 | 설명 |
|------|------|------|
| SQL Injection | `engine/eg.php` 전반 | 쿼리에 변수를 직접 문자열 연결 |
| API 키 노출 | `web/application/controllers/osy.php:59` | GCM API 키 하드코딩 |
| DB 크리덴셜 평문 | `engine/eg.php:21` | DB 접속 정보 소스 내 포함 |

`engine4/`는 위 이슈를 모두 수정한 버전입니다.

### 민감 정보 관리 원칙

- `.env` 파일은 절대 버전 관리에 포함하지 않습니다 (`.gitignore` 처리)
- `.env.example` 만 커밋하고 실제 값은 서버에서 직접 설정합니다
- APNS 인증서(`.pem`)도 마찬가지로 버전 관리에서 제외하세요

---

## 기여자

- 웅파 (cikorea.net, blumine@gmail.com) — 기획, 백엔드
- 불의회상 (cikorea.net) — Android, iOS

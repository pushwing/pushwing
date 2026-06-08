# Pushwing

자체 앱이 없는 웹사이트(동호회, 커뮤니티 등)가 공용 앱을 통해 스마트폰 푸시 알림을 발송할 수 있게 해주는 서비스입니다.
파트너 웹사이트에서 클라이언트 등록을 통해 해당 사이트 회원들에게 푸시를 보낼 수 있습니다.

## 소스 오픈 배경

2년 가까이 운영을 해왔으나 멤버들 사정으로 인해 사업을 접고 소스를 오픈하기로 결정.

## 기여자

- 웅파 (cikorea.net, blumine@gmail.com) — 기획, 백엔드
- 불의회상 (cikorea.net) — Android, iOS

---

## 아키텍처 구조

```
pushwing/
├── android/        # Android 클라이언트 앱 (Eclipse 프로젝트)
├── engine/         # RESTful API 서버 (순수 PHP)
├── web/            # 관리 웹사이트 (CodeIgniter 2.x 기반)
└── plgins/
    ├── codeigniter/ # CI용 푸시 발송 라이브러리
    └── gnu/         # 그누보드 연동 플러그인
```

---

## 컴포넌트별 설명

### Android 앱 (`android/`)

- **GCM (Google Cloud Messaging)** 기반 푸시 수신
- 앱 설치 시 휴대폰 번호 + GCM registration ID를 서버에 등록
- 푸시 수신 시 SQLite에 저장하고 알림 표시 (소리/진동 설정 가능)
- Google AdMob 광고 탑재

### REST API 엔진 (`engine/`)

순수 PHP REST API (CodeIgniter 미사용).

| 엔드포인트 | 설명 |
|-----------|------|
| `sd` | 디바이스 등록 (휴대폰번호 + GCM ID) |
| `go` | 앱 시작 시 광고 on/off 플래그 조회 |
| `fd` | 푸시 내용 조회 |
| `nl` / `nv` | 공지사항 목록 / 상세 |
| `vr` / `cr` | 광고 노출 / 클릭 리포트 |

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
| REST API | PHP 5.x, MySQL |
| 웹 서버 | CodeIgniter 2.x, PHP, MySQL, tank_auth |
| iOS 푸시 | APNS (인증서 방식) |
| Android 푸시 | GCM (구버전, 현 FCM으로 대체됨) |

---

## 설치 및 설정

### 1. DB 설정

`engine/eg.php` 에서 DB 접속 정보를 설정합니다.

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

`push_wait` 테이블에 쌓인 푸시를 주기적으로 발송하려면 크론을 등록합니다.

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

## 주의사항

- GCM은 2019년 종료되어 **FCM(Firebase Cloud Messaging)** 으로 마이그레이션이 필요합니다.
- `engine/eg.php` 의 SQL 쿼리는 변수를 직접 연결하므로 운영 시 SQL Injection 대비가 필요합니다.
- API 키, DB 크리덴셜 등 민감 정보는 소스에 직접 포함하지 않도록 환경 변수로 분리하세요.

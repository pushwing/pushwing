# Pushwing Engine4

레거시 `engine/`(순수 PHP)을 CodeIgniter 4 기반으로 재작성한 REST API 서버입니다.
SQL Injection, 크리덴셜 하드코딩, `magic_quotes_gpc` 등 기존 보안 이슈를 모두 수정하고,
보안·성능 점검을 통해 추가 강화했습니다.

---

## 시스템 요구사항

- PHP 8.2 이상
- MySQL 5.7 이상
- PHP 확장: `intl`, `mbstring`, `mysqlnd`, `json`
- Composer 2.x

---

## 설치

```bash
composer install
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

웹 서버의 DocumentRoot를 `public/` 으로 지정합니다. Apache `.htaccess`는 `public/.htaccess`에 포함되어 있습니다.

---

## API 엔드포인트

모든 응답은 `application/json` 형식입니다.

| Method | URI | 설명 |
|--------|-----|------|
| GET | `/api/go` | 앱 시작 플래그 조회 (광고 on/off, 앱 버전) |
| POST | `/api/sd` | 디바이스 등록 / token 갱신 |
| GET | `/api/fd/{id}` | 푸시 내용 조회 |
| POST | `/api/vr` | 광고 노출 리포트 |
| POST | `/api/cr` | 광고 클릭 리포트 |
| GET | `/api/nl` | 공지사항 목록 |
| GET | `/api/nv/{id}` | 공지사항 상세 |

### GET `/api/go`

앱 실행 시 광고 on/off, 앱 버전, 업데이트 필요 여부를 반환합니다.

```json
{
  "code_value": "1",
  "and_ver": "1.0.0",
  "ios_ver": "1.0.0",
  "update": "0"
}
```

### POST `/api/sd` — 디바이스 등록

| 파라미터 | 필수 | 설명 |
|---------|------|------|
| `hp` | Y | 휴대폰 번호 |
| `cd` | Y | FCM device token |
| `os` | Y | `1`=iOS, `2`=Android |

최초 등록 시 환영 푸시가 `push_wait` 테이블에 자동 등록됩니다.

```json
{ "message": "success" }
```

### GET `/api/fd/{id}` — 푸시 내용 조회

device token 소유권 검증 후 반환합니다. `cd` 파라미터가 없거나 소유자가 다르면 404를 반환합니다.

| 파라미터 | 위치 | 필수 | 설명 |
|---------|------|------|------|
| `id` | URL | Y | push_end ID |
| `cd` | Query | Y | FCM device token (소유권 검증용) |

```json
{
  "id": "42",
  "subject": "새 댓글이 달렸습니다",
  "contents": "...",
  "url": "https://example.com/..."
}
```

### POST `/api/vr` / POST `/api/cr` — 광고 리포트

| 파라미터 | 필수 | 설명 |
|---------|------|------|
| `sid` | Y | 광고 번호 |
| `cd` | Y | 클라이언트 코드 |
| `ty` | Y | 위치 (`1`=초기화면, `2`=푸시내용) |
| `av` | Y | 앱 버전 |
| `hp` | Y | 휴대폰 번호 |
| `sq` | Y | 푸시 번호 |

### GET `/api/nl` — 공지사항 목록

최근 10건을 반환합니다.

```json
[
  { "id": "5", "subject": "서비스 점검 안내", "hit": "120", "reg_date": "2014-03-01 10:00:00" }
]
```

### GET `/api/nv/{id}` — 공지사항 상세

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

## 프로젝트 구조

```
engine4/
├── app/
│   ├── Controllers/
│   │   └── Api.php             # 7개 API 엔드포인트
│   ├── Models/
│   │   ├── PushModel.php       # 디바이스 등록(UPSERT), 푸시 조회(소유권 검증)
│   │   ├── ChecksModel.php     # 플래그/버전 조회 (단일 쿼리)
│   │   ├── AdReportModel.php   # 광고 노출/클릭 리포트
│   │   └── NoticeModel.php     # 공지사항
│   ├── Filters/
│   │   ├── ApiFilter.php       # CORS + Content-Type 공통 처리
│   │   └── RateLimitFilter.php # Rate Limiting (IP + URI 단위)
│   └── Config/
│       ├── Routes.php          # RESTful 라우팅
│       └── Filters.php         # 필터 등록
├── public/                     # DocumentRoot (index.php, .htaccess)
├── writable/                   # 캐시, 로그, 세션
├── .env.example                # 설정 템플릿
└── .env                        # 실제 설정 (gitignore)
```

---

## 레거시 `engine/` 대비 개선 사항

| 항목 | `engine/` (레거시) | `engine4/` (현재) |
|------|-------------------|------------------|
| DB 쿼리 | 문자열 직접 연결 → SQL Injection 위험 | CI4 Query Builder (prepared statements) |
| 크리덴셜 | 소스 코드에 하드코딩 | `.env` 환경변수로 분리 |
| 입력 검증 | `strip_tags` + 수동 체크 | CI4 Validation + 전화번호 정규식 |
| `magic_quotes_gpc` | 레거시 코드 포함 | PHP 8 + CI4로 완전 제거 |
| CORS | 미처리 | `ApiFilter`로 공통 처리 |
| 보안 헤더 | 없음 | `secureheaders` 전역 적용 |
| 비정상 문자 | 미차단 | `invalidchars` 전역 적용 |
| HTTPS 강제 | 없음 | `forcehttps` 활성화 |
| IDOR | 없음 | device token 소유권 검증 |
| Rate Limiting | 없음 | IP+URI 단위 분당 횟수 제한 |
| Race condition | SELECT→INSERT TOCTOU | INSERT ON DUPLICATE KEY UPDATE |
| 쿼리 수 (flags) | 3회 | 1회 (WHERE IN + array_column) |
| 캐싱 | 없음 | `/api/go` 5분, `/api/nl` 10분 |
| auto-routing | 사용 | 비활성화, 명시적 라우팅만 허용 |
| PHP 버전 | PHP 5.x | PHP 8.2 이상 |

---

## 크론 등록

`push_wait` 테이블에 쌓인 푸시를 주기적으로 발송하려면 크론을 등록합니다.

```
* * * * * curl https://your-domain.com/engine4/public/push/send_all
```

---

## 참고

- 전체 프로젝트 문서: [docs/readme.md](../docs/readme.md)
- CodeIgniter 4 공식 문서: https://codeigniter.com/user_guide/
- FCM 마이그레이션: GCM은 2019년 종료, Firebase Console에서 서버 키 발급 후 `.env`에 설정

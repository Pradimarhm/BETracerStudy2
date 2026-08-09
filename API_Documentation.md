# API Documentation Backend — Tracer Study (BETracerStudy2)

Dokumentasi ini menjelaskan endpoint API backend Tracer Study, alur data, struktur respons (berdasarkan Laravel Resources), serta relasi antar model yang membentuk skema tracer.

> Catatan: Dokumentasi ini bersifat **mengikuti implementasi kode** di project ini (routes + controller + model + resource).

---

## 1) Konsep Umum

### Auth
- Backend menggunakan **Laravel Sanctum**.
- Token dikirim via header:
  - `Authorization: Bearer <access_token>`

### Peran (Role)
- Terdapat middleware `role:admin`.
- Role alumni dipakai pada request tertentu (mis. `SubmitAnswerRequest@authorize()`).

---

## 2) Base URL & Format
- Base route: `/api`
- Semua endpoint berada di `routes/api.php`.
- Format respons umum:
  - `success: true/false`
  - `data: ...` (untuk payload utama)
  - `message: ...` (untuk pesan operasi)

---

## 3) Ringkasan Endpoint dari `routes/api.php`

### Public Routes (tanpa autentikasi)
1. **Login**
   - `POST /api/login`

2. **News**
   - `GET /api/news`
   - `GET /api/news/{slug}`

3. **Job vacancies**
   - `GET /api/jobs`

---

### Authenticated Routes (dengan `auth:sanctum`)

#### Alumni Routes (`/api/alumni/...`)
- `GET /api/alumni/me`
- `POST /api/alumni/update`
- `GET /api/alumni/`
- `GET /api/alumni/questionnaires`
- `POST /api/alumni/questionnaires/submit`
- `GET /api/alumni/questionnaires/{id}/my-answers`

#### Admin Routes (`/api/admin/...` dengan middleware `role:admin`)
- `GET /api/admin/tracer-statistics`
- `GET /api/admin/users` (+ CRUD via `apiResource('users', ...)`)
- `GET /api/admin/tracer-completion-summary`

- **News CRUD**
  - `POST /api/admin/news`
  - `POST /api/admin/news/{id}`
  - `DELETE /api/admin/news/{id}`

- **Jobs CRUD**
  - `POST /api/admin/jobs`
  - `POST /api/admin/jobs/{id}`
  - `DELETE /api/admin/jobs/{id}`

- **Questionnaires CRUD (manajemen paket kuesioner)**
  - `GET /api/admin/questionnaires`
  - `POST /api/admin/questionnaires`
  - `GET /api/admin/questionnaires/{id}`
  - `PUT /api/admin/questionnaires/{id}`
  - `DELETE /api/admin/questionnaires/{id}`

- **Questions**
  - `GET /api/admin/questions/{id}`
  - `POST /api/admin/questions`
  - `PUT /api/admin/questions/{id}`
  - `DELETE /api/admin/questions/{id}`
  - `PUT /api/admin/questions/order`

- **Question options**
  - `POST /api/admin/question-options`
  - `PUT /api/admin/question-options/{id}`
  - `DELETE /api/admin/question-options/{id}`

- **Reminder notification**
  - `POST /api/admin/send-reminders`

- **Export ke Excel**
  - `GET /api/admin/questionnaires/{id}/export`

---

## 4) Autentikasi (Sanctum)

### 4.1 Login
**Endpoint**: `POST /api/login`

**Controller**: `AuthController@login`

**Request**
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response (sukses)**
```json
{
  "success": true,
  "message": "Login berhasil",
  "access_token": "<token>",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "username": "...",
    "role": "admin|alumni"
  }
}
```

### 4.2 Logout
**Endpoint**: `POST /api/logout`

**Controller**: `AuthController@logout`

**Mechanism**: menghapus semua token milik user saat ini.

**Response**
```json
{ "message": "Berhasil logout" }
```

---

## 5) Skema Data (Model & Relasi)

Berikut model yang dipakai untuk membentuk API tracer.

### 5.1 User
**Model**: `App\Models\User`

- `id`
- `username`
- `email`
- `password` (hidden, cast: hashed)
- `role` (`admin`/`alumni`)

**Relasi**:
- `User -> Alumni` : `hasOne(Alumni::class, 'user_id')`

### 5.2 Alumni
**Model**: `App\Models\Alumni`

**fillable** (sesuai implementasi):
- `user_id`
- `nim`
- `nik`
- `npwp`
- `name`
- `phone_number`
- `img_profile`
- `privacy_settings` (cast ke array JSON)
- `tahun_lulus`
- `kdpstmsmh`
- `status`

**Relasi**:
- `Alumni -> User` : `belongsTo(User::class)`

### 5.3 Questionnaire (Paket Kuesioner)
**Model**: `App\Models\Questionnaire`

**fillable**:
- `title`
- `year`
- `is_active`

**Relasi**:
- `Questionnaire -> Question` : `hasMany(Question::class)`

### 5.4 Question (Pertanyaan)
**Model**: `App\Models\Question`

**fillable**:
- `questionnaire_id`
- `parent_id` (untuk pertanyaan bercabang)
- `kode`
- `text`
- `type`
- `order`
- `is_required`

**Relasi**:
- `Question -> Questionnaire` : `belongsTo(Questionnaire::class)`
- `Question -> children` : `hasMany(Question::class, 'parent_id')`
- `Question -> parent` : `belongsTo(Question::class, 'parent_id')`
- `Question -> options` : `hasMany(QuestionOption::class, 'question_id')->orderBy('order')`

### 5.5 QuestionOption (Opsi Jawaban)
**Model**: `App\Models\QuestionOption`

**fillable**:
- `question_id`
- `option_text`
- `value`
- `order`

**Relasi**:
- `QuestionOption -> Question` : `belongsTo(Question::class)`

### 5.6 Answer (Jawaban)
**Model**: `App\Models\Answer`

**fillable**:
- `question_id`
- `alumni_id`
- `question_option_id` (nullable)
- `answer_text` (nullable)

**Relasi**:
- `Answer -> Alumni` : `belongsTo(Alumni::class)`
- `Answer -> Question` : `belongsTo(Question::class)`
- `Answer -> QuestionOption` : `belongsTo(QuestionOption::class, 'question_option_id')`

### 5.7 News
**Model**: `App\Models\News`

**fillable**:
- `user_id`
- `title`
- `slug`
- `content`
- `thumbnail`
- `category`
- `is_published`

**Behavior**:
- Saat create: `slug` otomatis di-generate dari `title` (via `Str::slug`).

**Relasi**:
- `News -> User` : `belongsTo(User::class)`

### 5.8 JobVacancy (Lowongan Kerja)
**Model**: `App\Models\JobVacancy`

**fillable**:
- `user_id`
- `title`
- `company`
- `description`
- `location`
- `poster_image`
- `category`
- `is_active`
- `expired_at`

**Relasi**:
- `JobVacancy -> User` : `belongsTo(User::class)`

---

## 6) Bentuk Response (Laravel Resources)

### 6.1 AlumniResource
**file**: `app/Http/Resources/AlumniResource.php`

**Output** (ringkas):
- `id`
- `nim`
- `name` (diambil dari `alumni.user.username`)
- `email` (diambil dari `alumni.user.email`)
- `nik`, `npwp`
- `phone_number`:
  - jika `privacy_settings.show_phone === false` => `'Private'`
  - jika tidak ada setting => tampilkan nomor
- `img_profile`:
  - jika ada: `Storage::url(img_profile)`
- `privacy_settings`
- `tahun_lulus`, `kdpstmsmh`, `status`
- `created_at` (datetime string)

> Artinya, privacy mengontrol exposure nomor HP di API.

### 6.2 QuestionnaireResource
**file**: `app/Http/Resources/QuestionnaireResource.php`

**Output**:
- `id`
- `title`
- `year`
- `is_active` (boolean)
- `questions`: `QuestionResource::collection(whenLoaded('questions'))`
- `created_at`

### 6.3 QuestionResource
**file**: `app/Http/Resources/QuestionResource.php`

**Output**:
- `id`, `parent_id`, `kode`, `text`, `type`, `order`, `is_required`
- `options`: `QuestionOptionResource::collection(whenLoaded('options'))`
- `children`: `QuestionResource::collection(whenLoaded('children'))`

> Jadi struktur pertanyaan mendukung rekursif (parent/children) dan opsi.

### 6.4 QuestionOptionResource
**file**: `app/Http/Resources/QuestionOptionResource.php`

**Output**:
- `id`, `question_id`, `option_text`, `value`, `order`

### 6.5 AnswerResource
**file**: `app/Http/Resources/AnswerResource.php`

**Output**:
- `id`
- `alumni_id`, `question_id`
- `question_text` (jika relasi `question` di-load)
- `question_option_id`
- `selected_option` (berdasarkan `questionOption.option_text` jika di-load)
- `answer_text`
- `created_at`

### 6.6 NewsResource
**file**: `app/Http/Resources/NewsResource.php`

**Output**:
- `id`, `title`, `slug`, `content`, `category`
- `thumbnail` => `Storage::url(thumbnail)` atau `null`
- `is_published` (boolean)
- `author`:
  - `name` ditentukan dari `user.role`:
    - admin => 'Administrator'
    - alumni => ambil `user.alumni.name` (fallback `user.name`)
- `created_at` format `d F Y`
- `updated_at`

### 6.7 JobVacancyResource
**file**: `app/Http/Resources/JobVacancyResource.php`

**Output**:
- `id`, `title`, `company`, `description`, `location`, `category`
- `poster_image` => `Storage::url(...)` atau `null`
- `is_active` (boolean)
- `expired_at` => format `Y-m-d` atau `null`
- `posted_by`:
  - id + name dari `user.role`
- `created_at`

### 6.8 UserResource
**file**: `app/Http/Resources/UserResource.php`

**Output**:
- `id`, `username`, `email`, `role`
- `last_login_at` (jika ada di model)
- `created_at`

---

## 7) Dokumentasi Endpoint per Modul

### 7.1 Alumni

#### A) Ambil profil alumni
- **Endpoint**: `GET /api/alumni/me`
- **Controller**: `AlumniController@me`
- **Auth**: wajib

**Alur**:
1. Ambil `Auth::id()`
2. Service mengambil profil alumni untuk user tersebut
3. Response memakai `AlumniResource`

**Response**
```json
{
  "success": true,
  "data": { /* AlumniResource */ }
}
```

#### B) Update profil alumni
- **Endpoint**: `POST /api/alumni/update`
- **Controller**: `AlumniController@update`

**Request**
Payload mengikuti `UpdateAlumniRequest` (belum dibuka di sesi ini), namun keluaran dipetakan oleh `AlumniResource`.

**Response**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": { /* AlumniResource */ }
}
```

#### C) List alumni
- **Endpoint**: `GET /api/alumni/`
- **Controller**: `AlumniController@index`
- **Catatan**: implementasi controller mengembalikan semua alumni tanpa check eksplisit di controller.

**Response**
```json
{
  "success": true,
  "data": [ /* AlumniResource */ ]
}
```

#### D) List kuesioner aktif untuk alumni
- **Endpoint**: `GET /api/alumni/questionnaires`
- **Controller**: `QuestionnaireController@index`

**Response**
```json
{
  "success": true,
  "data": [ /* QuestionnaireResource */ ]
}
```

#### E) Submit jawaban kuesioner
- **Endpoint**: `POST /api/alumni/questionnaires/submit`
- **Controller**: `QuestionnaireController@storeAnswers`

**Request Validation (Authorize + Rules)**
- authorize: hanya `user()->role === 'alumni'`
- rules:
  - `answers` wajib array
  - `answers.*.question_id` wajib & exists
  - `answers.*.question_option_id` nullable & exists
  - `answers.*.answer_text` nullable string

**Request**
```json
{
  "answers": [
    {
      "question_id": 1,
      "question_option_id": null,
      "answer_text": "Jawaban teks alumni"
    },
    {
      "question_id": 2,
      "question_option_id": 5,
      "answer_text": null
    }
  ]
}
```

**Alur**:
1. `Auth::id()` dipakai sebagai `alumni_id` oleh service
2. `submitAnswers(alumniId, answers)`

**Response**
```json
{
  "success": true,
  "message": "Jawaban kuesioner berhasil disimpan."
}
```

#### F) Lihat jawaban “my answers” untuk kuesioner
- **Endpoint**: `GET /api/alumni/questionnaires/{id}/my-answers`
- **Controller**: `QuestionnaireController@myAnswers`

**Response**
```json
{
  "success": true,
  "data": [ /* AnswerResource */ ]
}
```

---

### 7.2 News

#### A) Public list news
- **Endpoint**: `GET /api/news`
- **Controller**: `NewsController@index`

**Response**
```json
{
  "success": true,
  "data": [ /* NewsResource */ ]
}
```

#### B) Public detail news by slug
- **Endpoint**: `GET /api/news/{slug}`
- **Controller**: `NewsController@show`

**Response**
```json
{
  "success": true,
  "data": { /* NewsResource */ }
}
```

#### C) Admin create/update/delete
Endpoint admin sesuai routing:
- `POST /api/admin/news` => `NewsController@store`
- `POST /api/admin/news/{id}` => `NewsController@update`
- `DELETE /api/admin/news/{id}` => `NewsController@destroy`

Response pada controller:
- create/update mengembalikan `NewsResource`

---

### 7.3 Job Vacancies

#### A) Public list jobs aktif
- **Endpoint**: `GET /api/jobs`
- **Controller**: `JobVacancyController@index`
- Response: `JobVacancyResource::collection`

**Response**
```json
{
  "success": true,
  "data": [ /* JobVacancyResource */ ]
}
```

#### B) Admin CRUD
- `POST /api/admin/jobs` => create
- `POST /api/admin/jobs/{id}` => update
- `DELETE /api/admin/jobs/{id}` => delete

---

### 7.4 Manajemen Kuesioner (Admin)

#### A) List questionnaires (paket)
- **Endpoint**: `GET /api/admin/questionnaires`
- **Controller**: `QuestionnaireManagementController@index`

**Response**
```json
{
  "success": true,
  "data": [ /* QuestionnaireResource */ ]
}
```

#### B) Create questionnaire
- **Endpoint**: `POST /api/admin/questionnaires`
- **Controller**: `QuestionnaireManagementController@store`

**Response**: 201 + `QuestionnaireResource`

#### C) Detail questionnaire
- **Endpoint**: `GET /api/admin/questionnaires/{id}`

**Response**: `QuestionnaireResource`

#### D) Update questionnaire
- **Endpoint**: `PUT /api/admin/questionnaires/{id}`

#### E) Delete questionnaire
- **Endpoint**: `DELETE /api/admin/questionnaires/{id}`

#### F) Export Excel hasil jawaban
- **Endpoint**: `GET /api/admin/questionnaires/{id}/export`
- **Controller**: `QuestionnaireManagementController@exportToExcel`

**Alur**:
1. Ambil questionnaire by id
2. Buat nama file: `Hasil_<title>.xlsx` (spaces diganti `_`)
3. `Excel::download(new AnswersExport($id), $fileName)`

> Format kolom Excel ditentukan oleh `App\Exports\AnswersExport` (file ini tidak dibuka pada sesi ini).

---

### 7.5 Pertanyaan & Opsi (Admin)

#### A) Question
- `GET /api/admin/questions/{id}`
- `POST /api/admin/questions`
- `PUT /api/admin/questions/{id}`
- `DELETE /api/admin/questions/{id}`
- `PUT /api/admin/questions/order`

Controller menggunakan `QuestionResource` untuk menampilkan detail.

**Update order** (`questions/order`):
- request divalidasi:
  - `orders` required array
  - setiap `orders.*.id` exists
  - setiap `orders.*.order` required integer

#### B) QuestionOption
- `POST /api/admin/question-options`
- `PUT /api/admin/question-options/{id}`
- `DELETE /api/admin/question-options/{id}`

Controller menggunakan `QuestionOptionResource`.

---

### 7.6 Statistik & Reminder (Admin)

#### A) Tracer statistics
- **Endpoint**: `GET /api/admin/tracer-statistics`
- **Controller**: `QuestionnaireController@getStatistics`

**Request validation**:
- butuh `question_id`:
  - `question_id` required
  - `exists:questions,id`

**Response**
```json
{
  "success": true,
  "message": "Data statistik tracer study berhasil ditarik.",
  "data": { ... }
}
```

> Struktur `data` ditentukan oleh service `QuestionnaireService@getTracerStatistics`.

#### B) Completion summary
- **Endpoint**: `GET /api/admin/tracer-completion-summary`
- **Controller**: `QuestionnaireController@getAlumniCompletionSummary`
- **Service**: `NotificationService@getCompletionStatus()`

#### C) Send reminders
- **Endpoint**: `POST /api/admin/send-reminders`
- **Controller**: `NotificationController@broadcastReminder`

**Request**
- validasi: `questionnaire_id` required exists in `questionnaires`

**Response**
```json
{
  "success": true,
  "message": "Notifikasi pengingat kuesioner berhasil dikirim ke {totalSent} alumni."
}
```

> Detail mekanisme reminder (mis. channel, template, filter alumni yang belum mengisi) berada di `NotificationService` dan `App\Notifications\QuestionnaireReminder`.

---

## 8) Detail Alur Data Utama

### 8.1 Hubungan antar entitas
1. **User** punya **Alumni** (1-1)
2. **Alumni** mengisi **Answer**
3. **Questionnaire** punya banyak **Question**
4. **Question** punya banyak **QuestionOption**
5. **Answer** menyimpan:
   - pertanyaan (`question_id`)
   - jawaban pilihan (`question_option_id`) **nullable**
   - jawaban teks (`answer_text`) **nullable**

### 8.2 Proses submit jawaban (Alumni)
1. Alumni login => dapat `access_token`
2. `POST /api/alumni/questionnaires/submit` dengan payload `answers[]`
3. Backend memvalidasi:
   - `question_id` valid
   - `question_option_id` jika ada harus valid
4. Service menyimpan jawaban ke tabel `answers` dengan `alumni_id = Auth::id() => Alumni.user_id`
5. Output endpoint `my-answers` menampilkan daftar jawaban menggunakan `AnswerResource`.

### 8.3 Privacy nomor HP (AlumniResource)
- `AlumniResource` membaca `privacy_settings['show_phone']`.
- Jika `false`, `phone_number` disamarkan menjadi `'Private'`.

---

## 9) Contoh Curl (Bearer Token)

### Login
```bash
curl -X POST "http://<host>/api/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

### Ambil profil alumni
```bash
curl -X GET "http://<host>/api/alumni/me" \
  -H "Authorization: Bearer <access_token>" \
  -H "Accept: application/json"
```

---

## 10) Hal yang ditentukan oleh Service/Export (tidak ada di controller)
Beberapa bentuk `data` bergantung pada implementasi service berikut:
- `AlumniService` (profil, list alumni)
- `QuestionnaireService` (active questionnaires, submit answers, get my answers, statistics)
- `NotificationService` (completion summary, reminder)
- `AnswersExport` (format Excel)

Dokumentasi kolom/struktur `data` persisnya dapat ditingkatkan lagi jika file service & export dibuka dan dipetakan.


# Employee Clearance System

Sistem Employee Clearance merupakan aplikasi berbasis web yang digunakan untuk membantu proses clearance karyawan secara terstruktur dan terintegrasi. Sistem ini menggantikan proses clearance manual yang sebelumnya dilakukan menggunakan dokumen fisik dan proses persetujuan secara terpisah.

Sistem ini memungkinkan karyawan mengajukan proses clearance dengan dua jenis pengajuan, yaitu **Resign** dan **Mutasi Internal**. Seluruh proses clearance dilakukan melalui tahapan pengajuan, pengembalian aset, approval berdasarkan pengelola aset, hingga proses finalisasi oleh HRD.

---

## Fitur Utama

* Pengajuan clearance oleh karyawan
* Pemilihan jenis clearance:
    * Resign
    * Mutasi Internal
* Pengajuan dan verifikasi bukti pengembalian aset
* Approval clearance berdasarkan aset yang dimiliki karyawan
* Approval clearance secara berjenjang berdasarkan role pengelola aset
* Finalisasi clearance oleh HRD
* Penonaktifan akun secara otomatis setelah clearance resign selesai
* Perubahan departemen secara otomatis setelah clearance mutasi internal selesai

Sedangkan bagian seperti:

* Pengelolaan data karyawan
* Pengelolaan data aset dan kategori aset
* Pengelolaan assignment aset kepada karyawan

tidak dimasukkan sebagai fitur utama, karena sistem tidak berfokus pada pengelolaan tersebut. Data-data itu hanya digunakan sebagai data pendukung dalam proses clearance.

---

## Role Pengguna

Sistem memiliki beberapa role pengguna dengan tanggung jawab yang berbeda.

### Karyawan

Karyawan dapat:

* Mengajukan clearance
* Memilih jenis clearance:
  * Resign
  * Mutasi Internal
* Mengisi informasi yang diperlukan dalam proses clearance
* Mengunggah atau memberikan bukti pengembalian aset
* Melihat status proses clearance

### HOD

HOD berperan sebagai pengelola atau pemegang aset fisik.

HOD dapat:

* Memeriksa pengembalian aset fisik
* Menyetujui pengembalian aset
* Mengajukan revisi jika aset belum dikembalikan atau terdapat masalah

### MIS

MIS berperan sebagai pengelola aset kredensial dan akses sistem.

MIS dapat:

* Memeriksa pengembalian atau penutupan akses
* Menyetujui aset kredensial
* Mengajukan revisi jika masih terdapat akses yang belum diselesaikan

### HRD

HRD berperan sebagai pengelola aset fasilitas dan pihak yang melakukan finalisasi proses clearance.

HRD dapat:

* Memeriksa aset fasilitas karyawan
* Menyetujui pengembalian aset fasilitas
* Mengajukan revisi jika diperlukan
* Melakukan finalisasi clearance
* Menyelesaikan proses clearance karyawan

---

## Alur Proses Clearance

### 1. Karyawan Mengajukan Clearance

Proses dimulai ketika karyawan mengajukan clearance melalui sistem.

Karyawan dapat memilih salah satu dari dua jenis clearance:

* **Resign**
* **Mutasi Internal**

Untuk pengajuan **Mutasi Internal**, karyawan juga menentukan departemen tujuan.

---

### 2. Karyawan Menyelesaikan Kebutuhan Clearance

Setelah pengajuan dibuat, karyawan perlu menyelesaikan seluruh kebutuhan clearance yang berkaitan dengan aset yang dimilikinya.

Contoh kebutuhan clearance meliputi:

* Pengembalian aset fisik
* Penonaktifan atau pengembalian akses kredensial
* Pengembalian fasilitas perusahaan
* Pengunggahan bukti pengembalian apabila diperlukan

Setiap aset yang dimiliki oleh karyawan akan menjadi bagian dari proses clearance.

---

### 3. Approval Berdasarkan Aset yang Dimiliki

Proses approval dilakukan berdasarkan jenis aset yang dimiliki oleh karyawan.
Sistem akan menentukan role yang perlu melakukan approval berdasarkan pengelola kategori aset.

Contoh:
Karyawan memiliki:
- Aset Kredensial
- Aset Fasilitas

Maka proses approval:
MIS
↓
HRD

Karena:
* Aset Kredensial dikelola oleh MIS
* Aset Fasilitas dikelola oleh HRD

Dalam kondisi ini, HOD tidak perlu melakukan approval karena karyawan tidak memiliki aset fisik yang dikelola oleh HOD.

---

## Urutan Approval Clearance

Urutan approval ditentukan berdasarkan aset yang dimiliki oleh karyawan.

### Approval Lengkap

Jika karyawan memiliki seluruh jenis aset:

HOD
↓
MIS
↓
HRD
↓
Finalisasi

Urutan approval:
1. HOD menyetujui aset fisik
2. MIS menyetujui aset kredensial
3. HRD menyetujui aset fasilitas
4. HRD melakukan finalisasi clearance

### Hanya Memiliki Aset Fisik

HOD
↓
HRD
↓
Finalisasi

---

### Hanya Memiliki Aset Kredensial

MIS
↓
HRD
↓
Finalisasi

---

### Hanya Memiliki Aset Fasilitas

HRD
↓
Finalisasi

Dengan sistem ini, proses approval tidak selalu harus melewati seluruh role. Approval hanya dilakukan oleh role yang memiliki tanggung jawab terhadap aset yang dimiliki oleh karyawan.

---

## Finalisasi Clearance

Setelah seluruh aset berhasil disetujui oleh role yang bertanggung jawab, proses akan diteruskan kepada HRD.

HRD akan mendapatkan akses untuk melakukan **Finalisasi Clearance**.

Ketika HRD melakukan finalisasi:

Status Clearance
Pending / Process
        ↓
Approved / Selesai

Pada tahap ini, proses clearance dianggap telah selesai.

---

## Dampak Finalisasi Berdasarkan Jenis Clearance

### Resign

Jika jenis clearance adalah **Resign**, maka setelah HRD melakukan finalisasi:

Clearance selesai
        ↓
Karyawan dinonaktifkan
        ↓
Akun user dinonaktifkan

Data yang diperbarui:
karyawan.active = false
users.is_active = false

Dengan demikian, karyawan tidak dapat melakukan login kembali ke dalam sistem.

---

### Mutasi Internal

Jika jenis clearance adalah **Mutasi Internal**, maka setelah HRD melakukan finalisasi:

Clearance selesai
        ↓
Departemen karyawan diperbarui
        ↓
Karyawan dipindahkan ke departemen tujuan

Data departemen karyawan akan diubah berdasarkan departemen tujuan yang telah dipilih pada saat pengajuan clearance.

depart_id
    ↓
depart_tujuan_id

Akun karyawan tetap aktif karena karyawan masih bekerja di dalam perusahaan.

---

## Alur Sistem Secara Keseluruhan

┌─────────────────────────┐
│ Karyawan Mengajukan     │
│ Clearance               │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ Pilih Jenis Clearance   │
│                         │
│ - Resign                │
│ - Mutasi Internal       │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ Menyelesaikan seluruh   │
│ kebutuhan clearance     │
│ dan pengembalian aset   │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ Sistem menentukan role  │
│ approval berdasarkan    │
│ aset yang dimiliki      │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ Approval Berjenjang     │
│                         │
│ HOD → MIS → HRD         │
│                         │
│ Berdasarkan kebutuhan   │
│ aset karyawan           │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ HRD Finalisasi          │
│ Clearance               │
└────────────┬────────────┘
             │
             ▼
       ┌─────┴─────┐
       │           │
       ▼           ▼
   RESIGN      MUTASI INTERNAL
       │           │
       ▼           ▼
  Nonaktifkan   Ubah Departemen
  Akun User     Karyawan

---

## Teknologi yang Digunakan

* **Laravel**
* **PHP**
* **MySQL**
* **Blade Template**
* **Tailwind CSS**
* **JavaScript**
* **Laragon**
* **Figma**

---

## Instalasi

### 1. Clone Repository
git clone <repository-url>

Masuk ke direktori project:

cd employee-clearance

---

### 2. Install Dependency
Install dependency PHP:
composer install

Jika project menggunakan dependency frontend:
npm install

---

### 3. Konfigurasi Environment
Duplikat file `.env.example` menjadi `.env`:

cp .env.example .env

Kemudian sesuaikan konfigurasi database:

DB_DATABASE=hris
DB_USERNAME=root
DB_PASSWORD=

---

### 4. Generate Application Key
php artisan key:generate

---

### 5. Menjalankan Migration dan Seeder
Untuk membuat database dari awal:
php artisan migrate:fresh --seed

Perintah tersebut akan:
1. Menghapus tabel lama
2. Menjalankan seluruh migration
3. Mengisi data awal melalui `DatabaseSeeder`

---

### 6. Menjalankan Project
php artisan serve

Project dapat diakses melalui:
http://127.0.0.1:8000

---

## Akun Demo
Seluruh akun demo menggunakan password:
123123

Contoh akun:
| Role     | Badge ID | Password |
| -------- | -------- | -------- |
| HRD      | HRD001   | 123123   |
| MIS      | MIS001   | 123123   |
| HOD      | HOD001   | 123123   |
| Karyawan | KRY001   | 123123   |
| Karyawan | KRY002   | 123123   |
| Karyawan | KRY003   | 123123   |

---

## Status Clearance
Secara umum, clearance memiliki beberapa status:

| Status     | Deskripsi                                     |
| ---------- | --------------------------------------------- |
| `pending`  | Clearance belum diproses                      |
| `process`  | Clearance sedang dalam proses approval        |
| `revision` | Terdapat aset atau data yang perlu diperbaiki |
| `approved` | Clearance telah disetujui dan difinalisasi    |

Setelah proses finalisasi selesai, clearance dianggap telah selesai diproses.

---

## Tujuan Sistem
Sistem Employee Clearance dikembangkan untuk:

* Mengurangi penggunaan dokumen fisik dalam proses clearance
* Mempercepat proses pengembalian dan verifikasi aset
* Mengatur proses approval berdasarkan tanggung jawab pengelola aset
* Meningkatkan transparansi status clearance
* Mengurangi risiko aset atau akses yang belum dikembalikan
* Mengotomatisasi proses setelah clearance selesai
* Memastikan akun karyawan yang resign dapat dinonaktifkan secara otomatis
* Memudahkan proses mutasi internal melalui perubahan departemen secara otomatis
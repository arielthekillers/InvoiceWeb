# Migration to Bootstrap 5 & Removal of AdminLTE + DataTables

Proyek ini saat ini menggunakan Bootstrap 3, template AdminLTE 2, dan plugin jQuery DataTables. Permintaan Anda adalah mengubah framework CSS menjadi Bootstrap terbaru (Bootstrap 5), menghapus AdminLTE, dan menghapus fitur DataTables (sehingga tabel tampil sederhana tanpa fitur *search/pagination* bawaan plugin).

Perubahan ini bersifat **skala besar (Major UI Overhaul)** karena menyentuh struktur hampir di seluruh halaman aplikasi. Berikut adalah rencana implementasinya:

## Proposed Changes

### 1. Struktur Layout Utama (Header & Footer)
#### [MODIFY] [header.php](file:///e:/xampp/htdocs/invoice/header.php) & [header-login.php](file:///e:/xampp/htdocs/invoice/header-login.php)
- **Hapus Library Lama:** Menghapus referensi ke `AdminLTE.css`, `bootstrap.min.css` (v3), `jquery.dataTables.css`, dan script terkait.
- **Tambahkan Bootstrap 5:** Memuat Bootstrap 5 CSS & Bundle JS via CDN.
- **Ubah Layout Sidebar → Navbar Atas:** AdminLTE menggunakan desain *Sidebar*. Pada Bootstrap 5 murni, kita akan mengubahnya menjadi *Top Navigation Bar* (Navbar) yang lebih modern dan sederhana.
- Mengganti icon FontAwesome/Ionicons dengan **Bootstrap Icons** (lebih relevan dan terintegrasi baik dengan Bootstrap 5).

#### [MODIFY] [footer.php](file:///e:/xampp/htdocs/invoice/footer.php)
- Menghapus penutup tag `content-wrapper` milik AdminLTE.
- Menutup tag container standar Bootstrap 5.

### 2. Modifikasi Komponen UI & Grid System
Bootstrap 5 menggunakan class yang berbeda dari Bootstrap 3 (contoh: `.panel` diubah menjadi `.card`, `.col-xs-*` diubah menjadi `.col-*`, `.form-group` diubah menjadi `.mb-3`, padding/margin utilities). Semua file berikut akan disesuaikan:

- **[MODIFY] [dashboard.php](file:///e:/xampp/htdocs/invoice/dashboard.php):** Mengganti elemen `.small-box` AdminLTE menjadi komponen `.card` Bootstrap 5 dengan desain yang lebih *clean* dan *premium*.
- **[MODIFY] [invoice-create.php](file:///e:/xampp/htdocs/invoice/invoice-create.php) & [invoice-edit.php](file:///e:/xampp/htdocs/invoice/invoice-edit.php):** Menyesuaikan layout form pembuatan/edit invoice ke grid Bootstrap 5, termasuk memperbaiki jarak antar elemen menggunakan *spacing utilities* (`mt-`, `mb-`, `p-`).
- **[MODIFY] Halaman CRUD lainnya:** `customer-add.php`, `customer-edit.php`, `user-add.php`, `user-edit.php`.

### 3. Penghapusan DataTables
Sesuai permintaan Anda, fungsionalitas DataTables (pencarian, pengurutan, pagination) akan dimatikan. Tabel hanya akan menampilkan data mentah dengan desain bawaan tabel Bootstrap 5.

#### [MODIFY] [js/scripts.js](file:///e:/xampp/htdocs/invoice/js/scripts.js)
- Menghapus inisialisasi DataTables: `$("#data-table").dataTable();`

#### [MODIFY] [functions.php](file:///e:/xampp/htdocs/invoice/functions.php)
- Menghapus ID `data-table` pada setiap tag `<table>` di fungsi `getInvoices()`, `getCustomers()`, dan `getUsers()`.
- Menambahkan class `.table .table-bordered .table-hover` bawaan Bootstrap 5.

#### [MODIFY] Halaman List
- `invoice-list.php`, `customer-list.php`, `user-list.php`
- Menyesuaikan pembungkus tabel menjadi `.table-responsive` standar Bootstrap 5.

## User Review Required

> [!CAUTION]
> Mengubah desain dari AdminLTE (Sidebar) ke Bootstrap 5 murni (Navbar Atas) akan membuat **tampilan aplikasi berubah secara drastis**. Menu-menu yang tadinya ada di samping kiri akan pindah ke bagian atas. 
> 
> Tabel data tidak akan memiliki fitur pencarian/halaman lagi, sehingga jika data pelanggan atau invoice Anda sudah mencapai ratusan, Anda harus men-scroll ke bawah secara manual (kecuali kita menambahkan fitur pencarian PHP manual di kemudian hari).

## Verification Plan

### Manual Verification
Setelah implementasi, saya akan memverifikasi:
- Halaman bisa diakses tanpa error di console log.
- Tampilan Dashboard, form Invoice, dan form Customer terlihat rapi dan proporsional.
- Tabel tampil dengan desain sederhana Bootstrap 5 tanpa error inisialisasi DataTables.

---
Silakan berikan persetujuan jika Anda setuju dengan rencana ini, atau sampaikan jika ada detail desain yang ingin Anda pertahankan.

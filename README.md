ArisanYuk adalah platform web modern yang dirancang untuk memodernisasi dan mengelola kegiatan arisan secara transparan dan efisien. Aplikasi ini menghilangkan kebutuhan pencatatan manual, memungkinkan pengguna untuk membuat, bergabung, dan mengelola grup arisan, serta memantau semua transaksi iuran dan pengundian pemenang secara real-time. ArisanYuk menyediakan antarmuka terpisah untuk anggota, bendahara (pemilik grup), dan administrator sistem, memastikan tata kelola keuangan yang rapi dan minim kecurangan.
ArisanYuk System Features
1. User Data Management (Role: Admin) Managing member account data, including the creation, update, and deletion of user records.
2. Arisan Group Data Management (Roles: Admin & Treasurer) Configuration and maintenance of arisan group information, member structure, and operational parameters for each group.
3. Arisan Registration and Participation (Role: Member) Enables users to self-register and join available arisan groups.
4. Intelligent User and Group Search Provides advanced search functionality to efficiently locate specific user and group data across the system.
5. Round Management and Draw Scheduling (Role: Treasurer) Organizing the breakdown of arisan rounds and structuring the schedule for the winner draw.
6. Arisan Fee Payment Feature (Role: Member) Allows members to upload proof of fee payment and track their individual payment status.
7. Payment Verification Feature (Role: Treasurer) The process of validating and confirming submitted payment proofs by the designated group manager (Treasurer).
8. Arisan Winner Draw System (Role: Treasurer) Determining the arisan winner through either an automated system or manual selection mechanism.
9. Dashboard and Arisan Financial Statistics Presents visual graphs detailing contribution progress, current payment statuses, and a full recapitulation of arisan activities.
10. Notification and Information System Delivers timely alerts regarding payment deadlines, verification results, draw schedules, and important public announcements.
11. User Profile Management Allows users to update their personal information and manage their account settings.

Teknologi yang digunakan yaitu :
PHP, Laravel 12, Laravel Breeze, Composer, MySQL, HTML5, CSS, Tailwind CSS, JavaScript, Livewire, Vite, Git, dan Laragon.

Cara Installasi :
Pertama-tama pasti melakukan installasi setupnya seperti installasi Laragon, Php, laravel, composer, mysql, nodejs, menginstal package-package seperti breeze, livewire, npm dan semisalnya.
Selanjutnya Penyusun memikirkan logika bisnis di dalam website ini dengan menyusun erd dan diagram case.
merancang fitur-fitur yang akan dibuat di dalam project tersebut.
membuat data migration bersama dengan model beserta relationshipnya.
membuat data dummy untuk akun dengan role yang berbeda lewat Factory dan Seeder.
Membuat landing page, dashboard masing-masing role.
menghubungkan authentication dan middleware pada setiap role user yang ada.
membuat fitur-fitur yang sudah dirancang dengan controller/komponen livewire sebagai penglogikaan dan blade sebagai tampilan.
selanjutnya mengatur route semua yang terkait.

Cara Menjalankan Project : 
Tahap pertama untuk menjalankan project ini yaitu mengaktifkan aset styling seperti css, javascript, livewire, dengan menjalankan prompt npm run dev.
Tahap kedua adalah menjalankan server lokal dengan memanggil prompt php artisan serve.

Preview Tampilan : 
![Dashboard Bendahara - Statistik Keuangan](assets/screenshots/landing.png)


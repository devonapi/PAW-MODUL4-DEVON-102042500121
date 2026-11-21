<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission

        // 6. Mata Kuliah Tambahan dengan Semester Berbeda
        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF302'],
            [
                'nama_mk' => 'Basis Data Lanjut',
                'deskripsi' => 'Mata kuliah yang membahas perancangan basis data, normalisasi, query SQL tingkat lanjut, dan optimasi.',
                'sks' => 3,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF201'],
            [
                'nama_mk' => 'Pemrograman Berorientasi Objek',
                'deskripsi' => 'Mata kuliah yang membahas konsep OOP: class, object, inheritance, polymorphism, dan encapsulation.',
                'sks' => 3,
                'semester' => 'Ganjil 2025/2026',
                'dosen_id' => $dosen->id,
            ]
        );

        // 7. Mahasiswa Tambahan.
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'mahasiswa2@celoe.test'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // 8. Daftarkan Mahasiswa ke Mata Kuliah (status approved).
        $course1->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        // 9. Dua Tugas Contoh pada Mata Kuliah Pertama.
        $assignment1 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 1: Konsep MVC pada Laravel'],
            [
                'deskripsi' => 'Jelaskan konsep MVC pada Laravel dan peran masing-masing komponen (Model, View, Controller) beserta contohnya.',
                'deadline' => now()->addDays(7),
            ]
        );

        $assignment2 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 2: Middleware dan Hak Akses'],
            [
                'deskripsi' => 'Jelaskan fungsi middleware pada Laravel dan bagaimana middleware dipakai untuk membedakan hak akses dosen dan mahasiswa.',
                'deadline' => now()->addDays(14),
            ]
        );

        // 10. Contoh Data Submission (ada yang sudah dinilai, ada yang belum).
        Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'MVC memisahkan aplikasi menjadi Model untuk data, View untuk tampilan, dan Controller untuk logika yang menghubungkan keduanya.',
                'submitted_at' => now()->subDays(1),
                'nilai' => 85,
                'catatan_dosen' => 'Penjelasan sudah baik dan jelas.',
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'MVC adalah pola desain yang dipakai di Laravel untuk mengatur struktur kode aplikasi.',
                'submitted_at' => now()->subHours(5),
                'nilai' => 60,
                'catatan_dosen' => 'Tambahkan contoh dari tiap komponen.',
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $assignment2->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Middleware berfungsi sebagai penyaring request sebelum masuk ke controller, misalnya untuk mengecek role pengguna.',
                'submitted_at' => now()->subHours(2),
            ]
        );
    }
}

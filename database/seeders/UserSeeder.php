<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'KLK',
            'email' => 'klk@gmail.com',
            'pokja' => 'Kelembagaan & Kemitraan',
            'akses' => 'Admin',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'name' => 'SI',
            'email' => 'si@gmail.com',
            'pokja' => 'Sistem Informasi & PDDikti',
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'name' => 'Humas',
            'email' => 'humas@gmail.com',
            'pokja' => 'Kehumasan',
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'name' => 'HKT',
            'email' => 'hkt@gmail.com',
            'pokja' => 'Hukum, Kepegawaian, & Tata Laksana',
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'name' => 'RPM',
            'email' => 'rpm@gmail.com',
            'pokja' => 'Riset & Pengabdian kepada Masyarakat',
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'name' => 'Belmawa',
            'email' => 'belmawa@gmail.com',
            'pokja' => 'Pembelajaran, Kemahasiswaan, & Prestasi',
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'name' => 'Penjamu',
            'email' => 'penjamu@gmail.com',
            'pokja' => 'Penjaminan Mutu',
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'name' => 'SDP',
            'email' => 'sdp@gmail.com',
            'pokja' => 'Sumber Daya Pendidik',
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'name' => 'ADIA',
            'email' => 'adia@gmail.com',
            'pokja' => 'Anti Dosa Pendidikan & Integritas Akademik',
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);

        User::create([
            'name' => 'PP',
            'email' => 'pp@gmail.com',
            'pokja' => 'Perencanaan dan Penganggaran',
            'akses' => 'User',
            'password' => Hash::make('lldikti3'),
        ]);
    }
}

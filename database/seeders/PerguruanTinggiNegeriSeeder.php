<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PerguruanTinggiNegeri;

class PerguruanTinggiNegeriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PerguruanTinggiNegeri::create([
            'kode_pt' => '031035',
            'nama_pt' => 'Universitas Pembangunan Nasional Veteran Jakarta',
        ]);

        PerguruanTinggiNegeri::create([
            'kode_pt' => '005002',
            'nama_pt' => 'Politeknik Negeri Jakarta',
        ]);

        PerguruanTinggiNegeri::create([
            'kode_pt' => '005027',
            'nama_pt' => 'Polimedia Negeri Media Kreatif',
        ]);
    }
}

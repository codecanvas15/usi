<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterHrdAssessmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('master_hrd_assessments')->delete();
        
        \DB::table('master_hrd_assessments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Latar belakang pendidikan',
                'description' => 'Apakah kandidat memiliki kualifikasi pendidikan atau pelatihan yang sesuai untuk posisi ini?',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Pengalaman Kerja Sebelumnya',
                'description' => 'Apakah kandidat memperoleh keterampilan atau kualifikasi melalui pengalaman kerja sebelumnya?',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            2 => 
            array (
                'id' => 3,
                'title' => 'Kualifikasi/Pengalaman Teknis',
                'description' => 'Apakah kandidat memiliki keterampilan teknis yang diperlukan untuk posisi ini? ',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            3 => 
            array (
                'id' => 4,
                'title' => 'Komunikasi Verbal',
                'description' => 'Bagaimana keterampilan komunikasi kandidat selama wawancara?',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            4 => 
            array (
                'id' => 5,
                'title' => 'Minat Kandidat',
                'description' => 'Seberapa besar minat kandidat terhadap posisi dan organisasi?',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            5 => 
            array (
                'id' => 6,
                'title' => 'Pengetahuan tentang Organisasi',
                'description' => 'Apakah kandidat meneliti organisasi sebelum wawancara?',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            6 => 
            array (
                'id' => 7,
                'title' => 'Keterampilan Tim/Interpersonal',
                'description' => 'Apakah kandidat menunjukkan keterampilan membangun tim/interpersonal yang baik?',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            7 => 
            array (
                'id' => 8,
                'title' => 'Inisiatif',
                'description' => 'Apakah kandidat menunjukkan inisiatif tingkat tinggi?',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            8 => 
            array (
                'id' => 9,
                'title' => 'Manajemen Waktu',
                'description' => 'Apakah kandidat menunjukkan keterampilan manajemen waktu yang baik?',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            9 => 
            array (
                'id' => 10,
                'title' => 'Layanan Pelanggan',
                'description' => 'Apakah kandidat menunjukkan keterampilan/kemampuan layanan pelanggan tingkat tinggi?',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
            10 => 
            array (
                'id' => 11,
                'title' => 'Kesan dan Rekomendasi Secara Keseluruhan',
                'description' => 'Ringkasan persepsi Anda tentang kekuatan/kelemahan kandidat',
                'deleted_at' => NULL,
                'created_at' => '2023-02-15 22:15:40',
                'updated_at' => '2023-02-15 22:15:40',
            ),
        ));
        
        
    }
}
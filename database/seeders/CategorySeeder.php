<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Matematika',
                'description' => 'Materi pembelajaran matematika dasar hingga lanjutan'
            ],
            [
                'name' => 'Ilmu Pengetahuan Alam',
                'description' => 'Materi pembelajaran IPA termasuk fisika, kimia, dan biologi'
            ],
            [
                'name' => 'Bahasa Indonesia',
                'description' => 'Materi pembelajaran bahasa Indonesia dan sastra'
            ],
            [
                'name' => 'Bahasa Inggris',
                'description' => 'Materi pembelajaran bahasa Inggris'
            ],
            [
                'name' => 'Sejarah',
                'description' => 'Materi pembelajaran sejarah Indonesia dan dunia'
            ],
            [
                'name' => 'Geografi',
                'description' => 'Materi pembelajaran geografi dan ilmu bumi'
            ],
            [
                'name' => 'Ekonomi',
                'description' => 'Materi pembelajaran ekonomi dan bisnis'
            ],
            [
                'name' => 'Teknologi Informasi',
                'description' => 'Materi pembelajaran komputer dan teknologi'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

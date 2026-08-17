<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    /**
     * Daftar skill lengkap dari semua bidang, dikelompokkan per kategori.
     * Upsert berdasarkan slug agar aman dijalankan ulang (tidak duplikat,
     * id lama tetap dipertahankan sehingga relasi job_skills tidak rusak).
     */
    public function run(): void
    {
        $skills = [
            'teknologi' => [
                'HTML', 'CSS', 'JavaScript', 'TypeScript', 'PHP', 'Python', 'Java', 'C++', 'C#', 'Go', 'Kotlin', 'Swift',
                'Laravel', 'CodeIgniter', 'React', 'Vue.js', 'Next.js', 'Nuxt.js', 'Angular', 'Svelte',
                'Tailwind CSS', 'Bootstrap', 'Node.js', 'Express', 'Django', 'Flask', 'Spring Boot',
                'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'SQL', 'Database Design',
                'Flutter', 'React Native', 'Android Studio', 'Xcode',
                'Docker', 'Kubernetes', 'AWS', 'Google Cloud', 'Azure', 'Linux', 'Nginx', 'CI/CD', 'DevOps',
                'Git', 'GitHub', 'GitLab', 'REST API', 'GraphQL', 'Microservices', 'Testing', 'Cybersecurity',
                'Machine Learning', 'Data Analysis', 'Big Data', 'Data Visualization',
            ],
            'desain' => [
                'Figma', 'UI/UX Design', 'Adobe Photoshop', 'Adobe Illustrator', 'Adobe After Effects',
                'Adobe Premiere Pro', 'CorelDRAW', 'Canva', 'Blender', '3D Modeling', 'Motion Graphic',
                'Branding', 'Typography', 'Prototyping', 'Design System', 'Ilustrasi Digital',
            ],
            'marketing' => [
                'SEO', 'SEM', 'Digital Marketing', 'Copywriting', 'Google Ads', 'Meta Ads', 'TikTok Ads',
                'Social Media Marketing', 'Content Marketing', 'Email Marketing', 'Market Research',
                'Sales', 'Public Relations', 'Influencer Marketing', 'Google Analytics', 'CRM', 'Brand Strategy',
            ],
            'keuangan' => [
                'Excel', 'Akuntansi', 'Analisis Keuangan', 'Pajak', 'Perpajakan', 'Audit', 'Financial Modeling',
                'SAP', 'QuickBooks', 'Budgeting', 'Treasury', 'Manajemen Risiko', 'Investasi',
            ],
            'sdm' => [
                'Rekrutmen', 'HRIS', 'Payroll', 'Training & Development', 'Employee Relations',
                'Manajemen Kinerja', 'Psikotes', 'Job Analysis', 'Kompensasi & Benefit',
            ],
            'administrasi' => [
                'Microsoft Word', 'Microsoft PowerPoint', 'Google Workspace', 'Administrasi Perkantoran',
                'Arsip & Dokumentasi', 'Notulen Rapat', 'Customer Service', 'Data Entry', 'Sekretaris',
            ],
            'pendidikan' => [
                'Mengajar', 'Kurikulum', 'Desain Pembelajaran', 'E-learning', 'Bahasa Inggris', 'Bahasa Indonesia',
                'Bahasa Asing', 'Bimbingan Konseling', 'Penelitian', 'Menulis Akademik', 'Microteaching',
            ],
            'media' => [
                'Content Creator', 'Editing Video', 'Fotografi', 'Videografi', 'Penulisan Artikel',
                'Jurnalisme', 'Podcast', 'Storytelling', 'Scriptwriting', 'Broadcasting', 'Kamera',
            ],
            'hukum' => [
                'Hukum Bisnis', 'Kontrak & Perjanjian', 'Legal Drafting', 'Kepatuhan (Compliance)',
                'Hukum Ketenagakerjaan', 'Hukum Korporasi', 'Perizinan Usaha',
            ],
            'kesehatan' => [
                'Keperawatan', 'Kesehatan Masyarakat', 'Gizi', 'Farmasi', 'Laboratorium Medis',
                'First Aid', 'Kebidanan', 'Fisioterapi',
            ],
            'konstruksi' => [
                'AutoCAD', 'Revit', 'Manajemen Proyek', 'Estimasi Biaya', 'K3 (Keselamatan Kerja)',
                'Teknik Sipil', 'Teknik Elektro', 'Teknik Mesin', 'Surveying', 'Arsitektur', 'Material Bangunan',
            ],
            'umum' => [
                'Komunikasi', 'Public Speaking', 'Leadership', 'Team Management', 'Problem Solving',
                'Critical Thinking', 'Manajemen Waktu', 'Kerja Tim', 'Adaptabilitas', 'Kreativitas',
                'Negosiasi', 'Empati', 'Manajemen Stres', 'Pengambilan Keputusan', 'Mindset Growth',
            ],
        ];

        foreach ($skills as $kategori => $daftar) {
            foreach ($daftar as $nama) {
                Skill::updateOrCreate(
                    ['slug' => Str::slug($nama)],
                    ['nama' => $nama, 'kategori' => $kategori],
                );
            }
        }
    }
}

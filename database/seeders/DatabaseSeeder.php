<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'Барномасозӣ' => 'programming',
            'Математика' => 'mathematics',
            'Физика' => 'physics',
            'Забонҳо' => 'languages',
            'Дигар' => 'other',
        ])->mapWithKeys(fn (string $slug, string $name) => [
            $slug => Category::updateOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]),
        ]);

        $accounts = [
            ['name' => 'Demo Student', 'email' => 'student@example.com', 'role' => 'student'],
            ['name' => 'Demo Peer', 'email' => 'peer@example.com', 'role' => 'student'],
            ['name' => 'Demo Moderator', 'email' => 'moderator@example.com', 'role' => 'moderator'],
            ['name' => 'Demo Admin', 'email' => 'admin@example.com', 'role' => 'admin'],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [...$account, 'status' => 'active', 'locale' => 'tj', 'password' => Hash::make('password')],
            );
        }

        $student = User::where('email', 'student@example.com')->firstOrFail();
        $question = Question::updateOrCreate(
            ['title' => 'Чӣ тавр дар PHP массивро аз рӯи арзиш ҷудо кунам?'],
            [
                'user_id' => $student->id,
                'category_id' => $categories['programming']->id,
                'body' => 'Ман мехоҳам массиви ассотсиативиро бо нигоҳ доштани калидҳо аз рӯи арзишҳо ҷудо кунам. Кадом функсияро истифода барам?',
                'status' => 'published',
            ],
        );

        Answer::firstOrCreate(
            ['question_id' => $question->id, 'user_id' => User::where('email', 'peer@example.com')->value('id')],
            ['body' => 'Функсияи asort() массивро аз рӯи арзиш ҷудо мекунад ва калидҳои аслиро нигоҳ медорад.'],
        );
    }
}

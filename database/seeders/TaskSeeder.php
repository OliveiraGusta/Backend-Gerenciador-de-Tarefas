<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::create([
            'title' => 'Task Teste 1',
            'description' => 'Descrição da Task Teste 1',
            'status' => 'pending',
            'userOwner' => 1, 
        ]);

        Task::create([
            'title' => 'Task Teste 2',
            'description' => 'Descrição da Task Teste 2',
            'status' => 'completed',
            'userOwner' => 1, 
        ]);
    }
}

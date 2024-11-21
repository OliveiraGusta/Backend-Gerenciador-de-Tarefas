<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@example.com',
            'password' => Hash::make('senha123'),  
            'githubUsername' => 'usuario-github',
        ]);
    }
}

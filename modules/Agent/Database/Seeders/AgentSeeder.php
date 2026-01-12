<?php

namespace Modules\Agent\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Agent\Models\Agent;
use Modules\User\Models\User;

use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        info('Creating Agents with linked Users...');

        progress(
            label: 'Agents creation status',
            steps: 10,
            callback: function () {
                $agent = Agent::factory()->approved()->create();

                User::factory()->create([
                    'name' => $agent->contact_person,
                    'email' => $agent->email,
                    'password' => Hash::make('password'),
                    'profile_type' => 'agent',
                    'profile_id' => $agent->id,
                ]);

                return $agent;
            },
        );

        info('10 Agents with linked Users created.');

        Schema::enableForeignKeyConstraints();
    }
}

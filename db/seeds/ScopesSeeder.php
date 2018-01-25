<?php

use Phinx\Seed\AbstractSeed;

class ScopesSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $scope = $this->table('scopes');
        $scope->truncate();

        $seedData = [
            [
                'name' => 'email',
                'description' => 'a email scope.'
            ],
            [
                'name' => 'profile',
                'description' => 'a profile scope.'
            ],
            [
                'name' => 'general',
                'description' => 'a general scope with very little permissions.'
            ],
        ];

        $scope->insert($seedData)->save();
    }
}

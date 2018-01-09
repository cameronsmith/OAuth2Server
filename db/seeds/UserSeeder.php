<?php

use Phinx\Seed\AbstractSeed;
use Carbon\Carbon;

class UserSeeder extends AbstractSeed
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
        $users = $this->table('users');
        $users->truncate();

        $users->insert([
            'username' => 'user1',
            'password' => password_hash('Password1!', PASSWORD_BCRYPT),
            'created' =>  Carbon::now()
        ])->save();
    }
}

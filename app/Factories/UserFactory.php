<?php namespace App\Factories;

use Carbon\Carbon;

class UserFactory extends Factory
{
    const TABLE_NAME = 'users';

    /**
     * Factory fields.
     *
     * @return array
     */
    protected static function factory() {
        return [
            'username' => 'user1',
            'password' => password_hash('Password1!', PASSWORD_BCRYPT),
            'created' =>  Carbon::now()->toDateTimeString()
        ];
    }
}
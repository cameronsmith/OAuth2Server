<?php namespace App\Factories;

use Faker\Factory as Faker;
use Carbon\Carbon;

class ClientFactory extends Factory
{
    const TABLE_NAME = 'clients';

    /**
     * Factory fields.
     *
     * @return array
     */
    protected static function factory() {
        $faker = Faker::create();

        return [
            'name' => $faker->domainWord,
            'secret' => password_hash('secret1!', PASSWORD_BCRYPT),
            'is_confidential' => true,
            'redirect_uri' => '/',
            'created' =>  Carbon::now()->toDateTimeString()
        ];
    }
}
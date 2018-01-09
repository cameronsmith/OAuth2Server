<?php


use Phinx\Seed\AbstractSeed;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ClientSeeder extends AbstractSeed
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
        $client = $this->table('clients');
        $client->truncate();

        $faker = Faker::create();

        $seedData = [];

        for ($i = 0; $i < 3; $i++) {
            $seedData[] = [
                'name' => $faker->domainWord,
                'secret' => password_hash('secret1!', PASSWORD_BCRYPT),
                'is_confidential' => true,
                'redirect_uri' => '/',
                'created' =>  Carbon::now()
            ];
        }

        $client->insert($seedData)->save();
    }
}

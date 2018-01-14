<?php

use Tests\BaseTest;
use App\Factories\ClientFactory;
use App\Factories\UserFactory;

class PasswordControllerTest extends BaseTest
{
    public function testTrueIsTrue() {

        $this->factory(ClientFactory::class);
        $this->factory(UserFactory::class);

        $this->post('/password', [
            'grant_type' => 'password',
            'client_id' => 1,
            'client_secret' => 'secret1!',
            'username' => 'user1',
            'password' => 'Password1!'

        ]);

        $this->assertTrue(true);
    }
}
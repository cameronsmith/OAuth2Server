<?php

use Tests\BaseTest;
use Tests\Stubs\PhpStream;

class PasswordControllerTest extends BaseTest
{
    public function testTrueIsTrue() {

        $this->post('/password', [
            'grant_type' => 'password',
            'client_id' => 2,
            'client_secret' => 'secret1!',
            'username' => 'user1',
            'password' => 'Password1!'

        ]);

        $this->assertTrue(true);
    }
}
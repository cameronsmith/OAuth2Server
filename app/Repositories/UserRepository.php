<?php namespace App\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use App\Entities\UserEntity;

class UserRepository implements UserRepositoryInterface
{
    protected $repoConnection;

    /**
     * UserRepository constructor.
     *
     * @param RepositoryConnection $repoConnection
     */
    public function __construct(RepositoryConnection $repoConnection)
    {
        $this->repoConnection = $repoConnection;
    }

    /**
     * Get a user from their credentials.
     *
     * @param string $username
     * @param string $password
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @return UserEntity|bool
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $sql = 'SELECT username, password FROM users WHERE username=:username';
        $stmt = $this->repoConnection->prepare($sql);
        $stmt->execute([
            'username' => $username,
        ]);

        $user = $stmt->fetch();
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        return new UserEntity();
    }

}
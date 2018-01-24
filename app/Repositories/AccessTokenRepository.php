<?php namespace App\Repositories;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use App\Entities\AccessTokenEntity;
use Carbon\Carbon;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    protected $repoConnection;

    /**
     * RefreshTokenRepository constructor.
     *
     * @param \App\Repositories\RepositoryConnection $repoConnection
     */
    public function __construct(RepositoryConnection $repoConnection)
    {
        $this->repoConnection = $repoConnection;
    }

    /**
     * Persist access tokens.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     * @return bool
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $tokenId = $accessTokenEntity->getIdentifier();
        $clientId = $accessTokenEntity->getClient()->getIdentifier();
        $userId = $accessTokenEntity->getUserIdentifier();
        $refreshExpireTime = $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s');
        $scope = $accessTokenEntity->getScopes()[0]->getIdentifier();
        $createdAt = Carbon::now()->toDateTimeString();

        $sql = 'INSERT INTO access_tokens (token_id, client_id, user_id, scope, expires, created) 
                VALUES(:token_id, :client_id, :user_id, :scope, :expires, :created)';

        $statement = $this->repoConnection->prepare($sql);
        $statement->bindParam('token_id', $tokenId);
        $statement->bindParam('client_id', $clientId);
        $statement->bindParam('user_id', $userId);
        $statement->bindParam('scope', $scope);
        $statement->bindParam('expires', $refreshExpireTime);
        $statement->bindParam('created', $createdAt);

        return $statement->execute();
    }

    /**
     * Revoke access token
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId)
    {
        $sql = 'UPDATE access_tokens SET revoked=1 WHERE token_id=:token_id';
        $statement = $this->repoConnection->prepare($sql);
        $statement->bindParam('token_id', $tokenId);
        $statement->execute();
    }

    /**
     * Is access token revoked.
     *
     * @param string $tokenId
     * @return bool
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $sql = 'SELECT id, expires FROM access_tokens WHERE token_id=:token_id AND revoked=:revoked LIMIT 1';
        $statement = $this->repoConnection->prepare($sql);
        $statement->bindParam('token_id', $tokenId);
        $statement->bindValue('revoked', 0);
        $statement->execute();

        $row = $statement->fetch();
        if (empty($row)) {
            return true;
        }

        $currentDate = new \DateTime();
        $expiredDate = new \DateTime($row['expires']);

        if ($currentDate > $expiredDate) {
            return true;
        }

        return false;
    }

    /**
     * Get new token.
     *
     * @param ClientEntityInterface $clientEntity
     * @param array $scopes
     * @param null $userIdentifier
     * @return AccessTokenEntity
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);
        return $accessToken;
    }
}
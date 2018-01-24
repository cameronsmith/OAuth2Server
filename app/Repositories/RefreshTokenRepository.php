<?php namespace App\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use App\Entities\RefreshTokenEntity;
use App\Repositories\RepositoryConnection;
use Carbon\Carbon;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
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
     * Persist the refresh token in a database.
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntityInterface
     * @return bool
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntityInterface)
    {
        $refreshToken = $refreshTokenEntityInterface->getIdentifier();
        $refreshExpireTime = $refreshTokenEntityInterface->getExpiryDateTime()->format('Y-m-d H:i:s');
        $scope = $refreshTokenEntityInterface->getAccessToken()->getScopes()[0]->getIdentifier();
        $createdAt = Carbon::now()->toDateTimeString();

        $sql = 'INSERT INTO refresh_tokens (refresh_token, scope, expires, created) VALUES(:refresh_token, :scope, :expires, :created)';
        $statement = $this->repoConnection->prepare($sql);
        $statement->bindParam('refresh_token', $refreshToken);
        $statement->bindParam('scope', $scope);
        $statement->bindParam('expires', $refreshExpireTime);
        $statement->bindParam('created', $createdAt);

        return $statement->execute();
    }

    /**
     * Revoke refresh token.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId)
    {
        $sql = 'UPDATE refresh_tokens SET revoked=1 WHERE refresh_token=:token_id';
        $statement = $this->repoConnection->prepare($sql);
        $statement->bindParam('token_id', $tokenId);
        $statement->execute();
    }

    /**
     * Has the refresh_token expired.
     *
     * @param string $tokenId
     * @return bool
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $sql = 'SELECT id, expires FROM refresh_tokens WHERE refresh_token=:token_id AND revoked=:revoked LIMIT 1';
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
     * Get new refresh token.
     *
     * @return RefreshTokenEntity
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }


}
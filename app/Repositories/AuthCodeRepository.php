<?php namespace App\Repositories;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use App\Entities\AuthCodeEntity;
use Carbon\Carbon;

class AuthCodeRepository implements AuthCodeRepositoryInterface
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
     * Persist authorization code.
     *
     * @param AuthCodeEntityInterface $authCodeEntity
     * @return bool
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $tokenId = $authCodeEntity->getIdentifier();
        $refreshExpireTime = $authCodeEntity->getExpiryDateTime()->format('Y-m-d H:i:s');
        $scope = $authCodeEntity->getScopes()[0]->getIdentifier();
        $createdAt = Carbon::now()->toDateTimeString();

        $sql = 'INSERT INTO auth_tokens (token_id, scope, expires, created) VALUES(:token_id, :scope, :expires, :created)';
        $statement = $this->repoConnection->prepare($sql);
        $statement->bindParam('token_id', $tokenId);
        $statement->bindParam('scope', $scope);
        $statement->bindParam('expires', $refreshExpireTime);
        $statement->bindParam('created', $createdAt);

        return $statement->execute();
    }

    /**
     * Revoke an authorization code.
     *
     * @param string $codeId
     */
    public function revokeAuthCode($codeId)
    {
        $sql = 'UPDATE auth_tokens SET revoked=1 WHERE token_id=:token_id';
        $statement = $this->repoConnection->prepare($sql);
        $statement->bindParam('token_id', $codeId);
        $statement->execute();
    }

    /**
     * Has authorization code been revoked.
     *
     * @param string $codeId
     * @return bool
     */
    public function isAuthCodeRevoked($codeId)
    {
        $sql = 'SELECT id, expires FROM auth_tokens WHERE token_id=:token_id AND revoked=:revoked LIMIT 1';
        $statement = $this->repoConnection->prepare($sql);
        $statement->bindParam('token_id', $codeId);
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
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }
}
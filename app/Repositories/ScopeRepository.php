<?php namespace App\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use App\Entities\ScopeEntity;

class ScopeRepository implements ScopeRepositoryInterface
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
     * Get scope requested.
     *
     * @param string $scopeIdentifier
     * @return ScopeEntity|bool
     */
    public function getScopeEntityByIdentifier($scopeIdentifier)
    {
        $sql = 'SELECT * FROM scopes WHERE name=:scope';
        $statement = $this->repoConnection->prepare($sql);
        $statement->bindValue('scope', $scopeIdentifier);
        $statement->execute();

        $scopeRecord = $statement->fetch();

        if (!$scopeRecord) {
            return false;
        }

        $scope = new ScopeEntity();
        $scope->setIdentifier($scopeIdentifier);
        return $scope;
    }

    /**
     * Modify scopes on output if needed.
     *
     * If no scope is provided the `general` scope is added.
     *
     * @param array $scopes
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @param null $userIdentifier
     * @return array
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        if (empty($scopes)) {
            $scope = new ScopeEntity();
            $scope->setIdentifier('general');
            $scopes[] = $scope;
        }
        return $scopes;
    }

}
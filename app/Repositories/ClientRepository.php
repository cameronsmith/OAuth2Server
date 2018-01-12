<?php namespace App\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use App\Entities\ClientEntity;
use App\Repositories\RepositoryConnection;

class ClientRepository implements ClientRepositoryInterface
{
    protected $repoConnection;

    /**
     * ClientRepository constructor.
     *
     * @param \App\Repositories\RepositoryConnection $repoConnection
     */
    public function __construct(RepositoryConnection $repoConnection)
    {
        $this->repoConnection = $repoConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $stmt = $this->repoConnection->prepare('SELECT * FROM clients WHERE id = :id');
        $stmt->execute([
            'id' => $clientIdentifier
        ]);

        $client = $stmt->fetch();

        if (!$client) {
            return false;
        }

        if ($mustValidateSecret
            && $client['is_confidential']
            && password_verify($clientSecret, $client['secret']) === false) {

            return false;
        }

        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($clientIdentifier);
        $clientEntity->setName($client['name']);
        $clientEntity->setRedirectUri($client['redirect_uri']);

        return $clientEntity;
    }
}
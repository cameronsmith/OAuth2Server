<?php namespace App\Factories;

use App\Repositories\RepositoryConnection;
use App\Exceptions\NoTableDefinedInFactory;

abstract class Factory
{
    protected $tableName;
    protected $data = [];

    /**
     * Factory constructor.
     *
     * @param $tableName
     * @param $data
     */
    public function __construct($tableName, $data)
    {
        $this->data = $data;
        $this->tableName = $tableName;
    }

    /**
     * Return factory values.
     *
     * @param array $data
     * @return static
     * @throws NoTableDefinedInFactory
     */
    public static function create(array $data = [])
    {
        $className = get_called_class();
        $insertTableName = $className::TABLE_NAME;

        if (!defined($className . '::TABLE_NAME')) {
            throw new NoTableDefinedInFactory('There is no TABLE_NAME specified in the ' . $className);
        }

        return new static($insertTableName, array_merge(static::factory(), $data));
    }

    /**
     * Persist data to table.
     *
     * @param RepositoryConnection $repoConnection
     * @return $this|bool
     */
    public function persist(RepositoryConnection $repoConnection)
    {
        if (count($this->data) == 0) {
            return false;
        }

        $columnNames = array_keys($this->data);
        $columns = implode(',', $columnNames);
        $values = implode(',:', $columnNames);
        $sql = 'INSERT INTO `'. $this->tableName .'` (' . $columns . ') VALUES (:'. $values.')';
        $stmt = $repoConnection->prepare($sql);

        foreach($columnNames as $columnName) {
            $stmt->bindParam(':' . $columnName, $this->data[$columnName]);
        }

        $stmt->execute();

        return $this;
    }

    /**
     * Return the data of the factory.
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Method that supplies the factory.
     *
     * @return mixed
     */
    abstract protected static function factory();
}
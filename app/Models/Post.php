<?php
declare(strict_types=1);

namespace App\Models;

use JetBrains\PhpStorm\Pure;

/**
 * Class Post model.
 *
 * @package App\Models
 */
class Post
{
    protected string $tableName = 'posts';

    public string $id = '';
    protected string $from_name = '';
    public string $from_id = '';
    public string $message = '';
    protected string $type = '';
    public string $created_time = '';

    /**
     * Create Post model from array.
     *
     * @param array $data
     * @return $this
     */
    public function create(array $data): self
    {
        $this->id = $this->getDataValue('id', $data);
        $this->from_name = $this->getDataValue('from_name', $data);
        $this->from_id = $this->getDataValue('from_id', $data);
        $this->message = $this->getDataValue('message', $data);
        $this->type = $this->getDataValue('type', $data);
        $this->created_time = $this->getDataValue('created_time', $data);

        return $this;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $key
     * @param array  $data
     * @return mixed
     */
    #[Pure] protected function getDataValue(string $key, array $data): mixed
    {
        return (array_key_exists($key, $data)) ? $data[$key] : null;
    }

}

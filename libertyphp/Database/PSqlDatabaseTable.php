<?php

namespace Libertyphp\Database;

abstract class PSqlDatabaseTable
{
    /** @var SqlDatabaseInterface */
    protected $db;

    public function __construct(SqlDatabaseInterface $db)
    {
        $this->db = $db;
    }

    /**
     * @return string
     */
    abstract static function getTableName();

    /**
     * @return string
     */
    abstract static function getPrimaryKeyName();

    /**
     * @param int $id
     * @return array|null
     *
     * @throws SqlQueryException
     */
    protected function getRowById(int $id): ?array
    {
        $tableName = static::getTableName();
        $pk = static::getPrimaryKeyName();
        return $this->db->selectRow("SELECT * FROM {$tableName} WHERE {$pk} = :pk", ['pk' => $id]);
    }

    /**
     * @param array $ids
     * @return array
     * @throws SqlQueryException
     */
    protected function getRowsByIds(array $ids): array
    {
        $ids = array_unique($ids);
        if (!$ids) {
            return [];
        }

        $tableName = static::getTableName();
        $pk = static::getPrimaryKeyName();
        $idsSql = join(', ', $ids);

        if (!$idsSql) {
            return [];
        }

        return $this->db->select("SELECT * FROM {$tableName} WHERE {$pk} IN ({$idsSql})");
    }

    /**
     * @param array $columnValues
     * @throws SqlQueryException
     */
    protected function insertRow(array $columnValues)
    {
        $sqlColumns = [];
        $sqlValues = [];
        foreach ($columnValues as $column => $value) {
            $sqlColumns[] = "{$column}";
            $sqlValues[] = ":{$column}";
        }

        $columnsSqlString = join(', ', $sqlColumns);
        $valuesSqlString = join(', ', $sqlValues);

        $tableName = static::getTableName();

        $this->db->execute(
            "INSERT INTO {$tableName} ({$columnsSqlString}) VALUES ({$valuesSqlString})",
            $columnValues
        );
    }

    /**
     * @param $id
     * @param array $columnValues
     * @throws SqlQueryException
     */
    protected function updateRow(int $id, array $columnValues)
    {
        $pk = static::getPrimaryKeyName();

        $columnsSetSql = [];
        foreach ($columnValues as $column => $value) {
            $columnsSetSql[] = "{$column} = :{$column}";
        }

        $columnsSqlString = join(', ', $columnsSetSql);
        $columnValues[$pk] = $id;

        $tableName = static::getTableName();

        $this->db->execute(
            "UPDATE {$tableName} SET {$columnsSqlString} WHERE {$pk} = :id",
            $columnValues
        );
    }

    /**
     * @param array $row
     * @return array
     * @throws SqlQueryException
     */
    protected function saveRow(array $row)
    {
        $pk = static::getPrimaryKeyName();

        if (!$row[$pk]) {
            unset($row[$pk]);

            $this->insertRow($row);

            $lastInsertedId = (int) $this->db->getLastInsertedId();
            $row[$pk] = $lastInsertedId;
        } else {
            $pkValue = $row[$pk];
            unset($row[$pk]);

            $this->updateRow($pkValue, $row);
            $row[$pk] = $pkValue;
        }

        return $row;
    }

    /**
     * @param string[] $where
     * @param string[]|null $order
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array
     *
     * @throws SqlQueryException
     */
    public function getRows(array $where, array $order = null, int $limit = null, int $offset = null): array
    {
        $tableName = static::getTableName();
        $pk = static::getPrimaryKeyName();

        $binds = [];

        $whereSql = ['1 = 1'];
        foreach ($where as $whereCondition => $value) {
            if (is_array($value)) {
                $whereInSqlString = '';
                foreach ($value as $i => $valueItem) {
                    $whereInSqlString .= is_string($valueItem) ? "'{$valueItem}'" : $valueItem;
                    if ($i < count($value) - 1) {
                        $whereInSqlString .= ',';
                    }
                }

                $whereSql[] = str_replace('?', "({$whereInSqlString})", $whereCondition);
            } else {
                if (is_numeric($whereCondition)) {
                    $whereCondition = $value;
                }

                $whereSql[] = "{$whereCondition}";
                if ($whereCondition !== $value) {
                    $binds[] = $value;
                }
            }
        }

        $whereSql = join(' AND ', $whereSql);
        $orderSql = $order ? join(', ', $order) : "{$pk} ASC";

        $sql = "SELECT * FROM {$tableName} WHERE {$whereSql} ORDER BY {$orderSql}";

        if ($limit) {
            $sql .= " LIMIT ?";
            $binds[] = $limit;
        }

        if ($offset) {
            $sql .= " OFFSET ?";
            $binds[] = $offset;
        }

        return $this->db->select($sql, $binds);
    }

    /**
     * @param array $where
     * @param array|null $order
     * @return array|null
     *
     * @throws SqlQueryException
     */
    public function getRow(array $where, array $order = null): ?array
    {
        $rows = static::getRows($where, $order, 1);
        return $rows[0] ?? null;
    }

    /**
     * @param array $where
     * @return int
     * @throws SqlQueryException
     */
    public function getCount(array $where): int
    {
        $tableName = static::getTableName();
        $binds = [];

        if (!$where) {
            $sql = "SELECT COUNT(*) cnt FROM {$tableName}";
        } else {
            $whereSql = [];
            foreach ($where as $whereCondition => $value) {
                if (is_array($value)) {
                    $whereInSqlString = '';
                    foreach ($value as $i => $valueItem) {
                        $whereInSqlString .= is_string($valueItem) ? "'{$valueItem}'" : $valueItem;
                        if ($i < count($value) - 1) {
                            $whereInSqlString .= ',';
                        }
                    }

                    $whereSql[] = str_replace('?', "({$whereInSqlString})", $whereCondition);
                } else {
                    if (is_numeric($whereCondition)) {
                        $whereCondition = $value;
                    }

                    $whereSql[] = "{$whereCondition}";
                    if ($whereCondition !== $value) {
                        $binds[] = $value;
                    }
                }
            }

            $whereSql = join(' AND ', $whereSql);
            $sql = "SELECT COUNT(*) cnt FROM {$tableName} WHERE {$whereSql}";
        }

        return $this->db->selectRow($sql, $binds)['cnt'];
    }

    /**
     * @param int $id
     * @throws SqlQueryException
     */
    public function deleteRow(int $id)
    {
        $tableName = static::getTableName();
        $pk = static::getPrimaryKeyName();

        $this->db->execute("DELETE FROM {$tableName} WHERE {$pk} = ?", [$id]);
    }
}

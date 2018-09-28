<?php

namespace Libertyphp\Database;

abstract class SqlCommand
{
    /** @var SqlDatabaseInterface */
    protected $db;

    public function __construct(SqlDatabaseInterface $db)
    {
        $this->db = $db;
    }
}

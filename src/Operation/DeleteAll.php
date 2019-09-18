<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Operation;

use PDOException;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;

/**
 * Deletes all rows from all tables in a dataset.
 */
class DeleteAll implements Operation
{
    protected $useTransaction;

    public function __construct($transaction = true)
    {
        $this->useTransaction = $transaction;
    }

    public function execute(Connection $connection, IDataSet $dataSet)
    {
        $pdo = $connection->getConnection();
        if ($this->useTransaction) {
            $pdo->beginTransaction();
        }
        try {
            foreach ($dataSet->getReverseIterator() as $table) {
                $sql = 'DELETE FROM ' . $connection->quoteSchemaObject($table->getTableMetaData()->getTableName());
                $pdo->exec($sql);
            }
            if ($this->useTransaction) {
                $pdo->commit();
            }
        } catch (PDOException $e) {
            if ($this->useTransaction) {
                $pdo->rollBack();
            }
            throw new Exception('DELETE_ALL', $sql, [], $table, $e->getMessage());
        }
    }
}

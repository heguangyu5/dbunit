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

use PDO;
use PDOException;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;

/**
 * Executes a truncate against all tables in a dataset.
 */
class Truncate implements Operation
{
    protected $useTransaction;
    protected $useCascade;

    public function __construct($transaction = true, $cascade = false)
    {
        $this->useTransaction = $transaction;
        $this->useCascade     = $cascade;
    }

    public function execute(Connection $connection, IDataSet $dataSet)
    {
        $pdo = $connection->getConnection();
        $pdoMysql = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql';
        if ($pdoMysql) {
            $this->disableForeignKeyChecksForMysql($pdo);
        }

        if ($this->useTransaction) {
            $pdo->beginTransaction();
        }
        try {
            foreach ($dataSet->getReverseIterator() as $table) {
                $sql = $connection->getTruncateCommand() . ' ' . $connection->quoteSchemaObject($table->getTableMetaData()->getTableName());
                if ($this->useCascade && $connection->allowsCascading()) {
                    $sql .= ' CASCADE';
                }
                $pdo->exec($sql);
            }
            if ($this->useTransaction) {
                $pdo->commit();
            }
        } catch (\Exception $e) {
            if ($this->useTransaction) {
                $pdo->rollBack();
            }
            if ($pdoMysql) {
                $this->enableForeignKeyChecksForMysql($pdo);
            }
            if ($e instanceof PDOException) {
                throw new Exception('TRUNCATE', $sql, [], $table, $e->getMessage());
            }
            throw $e;
        }

        if ($pdoMysql) {
            $this->enableForeignKeyChecksForMysql($pdo);
        }
    }

    private function disableForeignKeyChecksForMysql(PDO $pdo)
    {
        $pdo->exec('SET @PHPUNIT_OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    }

    private function enableForeignKeyChecksForMysql(PDO $pdo)
    {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=@PHPUNIT_OLD_FOREIGN_KEY_CHECKS');
    }
}

<?php
/**
 * @group operation
 */

require_once \dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'DatabaseTestUtility.php';

class Extensions_Database_Operation_OperationsTest extends PHPUnit\DbUnit\TestCase
{
    protected function setUp(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO/SQLite is required to run this test.');
        }

        parent::setUp();
    }

    public function getConnection()
    {
        return new PHPUnit\DbUnit\Database\DefaultConnection(DBUnitTestUtility::getSQLiteMemoryDB(), 'sqlite');
    }

    public function getDataSet()
    {
        return new PHPUnit\DbUnit\DataSet\ArrayDataSet(
            include __DIR__ . '/../_files/ArrayDataSets/OperationsTestFixture.php'
        );
    }

    public function testDelete()
    {
        $deleteOperation = new PHPUnit\DbUnit\Operation\Delete();
        $deleteOperation->execute(
            $this->getConnection(),
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/DeleteOperationTest.php'
            )
        );

        $this->assertDataSetsEqual(
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/DeleteOperationResult.php'
            ),
            $this->getConnection()->createDataSet()
        );
    }

    public function testDeleteAll()
    {
        $deleteAllOperation = new PHPUnit\DbUnit\Operation\DeleteAll();
        $deleteAllOperation->execute(
            $this->getConnection(),
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(array(
                'table1' => array(),
                'table2' => array(),
                'table3' => array()
            ))
        );

        $this->assertTableEmpty('table1', 'table2', 'table3');
    }

    public function testTruncate()
    {
        $truncateOperation = new PHPUnit\DbUnit\Operation\Truncate();
        $truncateOperation->execute(
            $this->getConnection(),
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(array(
                'table1' => array(),
                'table2' => array(),
                'table3' => array()
            ))
        );

        $this->assertTableEmpty('table1', 'table2', 'table3');
    }

    public function testInsert()
    {
        $insertOperation = new PHPUnit\DbUnit\Operation\Insert();
        $insertOperation->execute(
            $this->getConnection(),
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/InsertOperationTest.php'
            )
        );

        $this->assertDataSetsEqual(
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/InsertOperationResult.php'
            ),
            $this->getConnection()->createDataSet(array('table1', 'table2', 'table3'))
        );
    }

    public function testUpdate(): void
    {
        $updateOperation = new PHPUnit\DbUnit\Operation\Update();
        $updateOperation->execute(
            $this->getConnection(),
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/UpdateOperationTest.php'
            )
        );

        $this->assertDataSetsEqual(
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/UpdateOperationResult.php'
            ),
            $this->getConnection()->createDataSet()
        );
    }

    public function testReplace(): void
    {
        $replaceOperation = new PHPUnit\DbUnit\Operation\Replace();
        $replaceOperation->execute(
            $this->getConnection(),
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/ReplaceOperationTest.php'
            )
        );

        $this->assertDataSetsEqual(
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/ReplaceOperationResult.php'
            ),
            $this->getConnection()->createDataSet()
        );
    }

    public function testInsertEmptyTable(): void
    {
        $insertOperation = new PHPUnit\DbUnit\Operation\Insert();
        $insertOperation->execute(
            $this->getConnection(),
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/EmptyTableInsertTest.php'
            )
        );

        $this->assertDataSetsEqual(
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/EmptyTableInsertResult.php'
            ),
            $this->getConnection()->createDataSet()
        );
    }

    public function testInsertAllEmptyTables(): void
    {
        $insertOperation = new PHPUnit\DbUnit\Operation\Insert();
        $insertOperation->execute(
            $this->getConnection(),
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(array(
                'table1' => array()
            ))
        );

        $this->assertDataSetsEqual(
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(
                include __DIR__ . '/../_files/ArrayDataSets/OperationsTestFixture.php'
            ),
            $this->getConnection()->createDataSet()
        );
    }
}

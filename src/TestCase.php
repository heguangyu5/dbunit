<?php

namespace PHPUnit\DbUnit;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->getSetUpOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->getTearDownOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    protected function getSetUpOperation()
    {
        return new Operation\Composite(array(
            new Operation\Truncate(),
            new Operation\Insert()
        ));
    }

    protected function getTearDownOperation()
    {
        return new Operation\None();
    }

    public static function assertTablesEqual(DataSet\ITable $expected, DataSet\ITable $actual, $message = '')
    {
        $constraint = new Constraint\TableIsEqual($expected);

        self::assertThat($actual, $constraint, $message);
    }

    public static function assertDataSetsEqual(DataSet\IDataSet $expected, DataSet\IDataSet $actual, $message = '')
    {
        $constraint = new Constraint\DataSetIsEqual($expected);

        self::assertThat($actual, $constraint, $message);
    }

    public function assertTableRowCount($tableName, $expected, $message = '')
    {
        $constraint = new Constraint\TableRowCount($tableName, $expected);
        $actual     = $this->getConnection()->getRowCount($tableName);

        self::assertThat($actual, $constraint, $message);
    }

    public function assertTableEmpty()
    {
        $tables = func_get_args();
        foreach ($tables as $table) {
            $this->assertTableRowCount($table, 0);
        }
    }

    protected function createDefaultDBConnection(\PDO $connection, $schema = '')
    {
        return new Database\DefaultConnection($connection, $schema);
    }

    protected function createArrayDataSet(array $data)
    {
        return new DataSet\ArrayDataSet($data);
    }

    abstract protected function getConnection();
    abstract protected function getDataSet();
}

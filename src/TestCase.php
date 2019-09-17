<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit;

/**
 * A TestCase extension that provides functionality for testing and asserting
 * against a real database.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Tester
     */
    protected $databaseTester;

    /**
     * Asserts that two given tables are equal.
     *
     * @param ITable $expected
     * @param ITable $actual
     * @param string $message
     */
    public static function assertTablesEqual(DataSet\ITable $expected, DataSet\ITable $actual, $message = ''): void
    {
        $constraint = new Constraint\TableIsEqual($expected);

        self::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that two given datasets are equal.
     *
     * @param ITable $expected
     * @param ITable $actual
     * @param string $message
     */
    public static function assertDataSetsEqual(DataSet\IDataSet $expected, DataSet\IDataSet $actual, $message = ''): void
    {
        $constraint = new Constraint\DataSetIsEqual($expected);

        self::assertThat($actual, $constraint, $message);
    }

    /**
     * Performs operation returned by getSetUpOperation().
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseTester = null;

        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();
    }

    /**
     * Performs operation returned by getTearDownOperation().
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->getDatabaseTester()->setTearDownOperation($this->getTearDownOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onTearDown();

        /*
         * Destroy the tester after the test is run to keep DB connections
         * from piling up.
         */
        $this->databaseTester = null;
    }

    /**
     * Assert that a given table has a given amount of rows
     *
     * @param string $tableName Name of the table
     * @param int    $expected  Expected amount of rows in the table
     * @param string $message   Optional message
     */
    public function assertTableRowCount($tableName, $expected, $message = ''): void
    {
        $constraint = new Constraint\TableRowCount($tableName, $expected);
        $actual     = $this->getConnection()->getRowCount($tableName);

        self::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that a given table contains a given row
     *
     * @param array  $expectedRow Row expected to find
     * @param ITable $table       Table to look into
     * @param string $message     Optional message
     */
    public function assertTableContains(array $expectedRow, DataSet\ITable $table, $message = ''): void
    {
        self::assertThat($table->assertContainsRow($expectedRow), self::isTrue(), $message);
    }

    /**
     * Closes the specified connection.
     *
     * @param Connection $connection
     */
    protected function closeConnection(Database\Connection $connection): void
    {
        $this->getDatabaseTester()->closeConnection($connection);
    }

    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    abstract protected function getConnection();

    /**
     * Gets the IDatabaseTester for this testCase. If the IDatabaseTester is
     * not set yet, this method calls newDatabaseTester() to obtain a new
     * instance.
     *
     * @return Tester
     */
    protected function getDatabaseTester()
    {
        if (empty($this->databaseTester)) {
            $this->databaseTester = $this->newDatabaseTester();
        }

        return $this->databaseTester;
    }

    /**
     * Returns the test dataset.
     *
     * @return IDataSet
     */
    abstract protected function getDataSet();

    /**
     * Returns the database operation executed in test setup.
     *
     * @return Operation
     */
    protected function getSetUpOperation()
    {
        return Operation\Factory::CLEAN_INSERT();
    }

    /**
     * Returns the database operation executed in test cleanup.
     *
     * @return Operation
     */
    protected function getTearDownOperation()
    {
        return Operation\Factory::NONE();
    }

    /**
     * Creates a IDatabaseTester for this testCase.
     *
     * @return Tester
     */
    protected function newDatabaseTester()
    {
        return new DefaultTester($this->getConnection());
    }

    /**
     * Creates a new DefaultDatabaseConnection using the given PDO connection
     * and database schema name.
     *
     * @param PDO    $connection
     * @param string $schema
     *
     * @return DefaultConnection
     */
    protected function createDefaultDBConnection(\PDO $connection, $schema = '')
    {
        return new Database\DefaultConnection($connection, $schema);
    }

    /**
     * Creates a new ArrayDataSet with the given array.
     * The array parameter is an associative array of tables where the key is
     * the table name and the value an array of rows. Each row is an associative
     * array by itself with keys representing the field names and the values the
     * actual data.
     * For example:
     * array(
     *     "addressbook" => array(
     *         array("id" => 1, "name" => "...", "address" => "..."),
     *         array("id" => 2, "name" => "...", "address" => "...")
     *     )
     * )
     *
     * @param array $data
     *
     * @return ArrayDataSet
     */
    protected function createArrayDataSet(array $data)
    {
        return new DataSet\ArrayDataSet($data);
    }
}

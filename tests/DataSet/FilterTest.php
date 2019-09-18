<?php
/**
 * @group dataset
 */
class Extensions_Database_DataSet_FilterTest extends PHPUnit\Framework\TestCase
{
    protected $expectedDataSet;

    public function setUp(): void
    {
        $this->expectedDataSet = new PHPUnit\DbUnit\DataSet\ArrayDataSet(
            include __DIR__ . '/../_files/ArrayDataSets/FilteredTestFixture.php'
        );
    }

    public function testExcludeFilteredDataSet(): void
    {
        $constraint = new PHPUnit\DbUnit\Constraint\DataSetIsEqual($this->expectedDataSet);
        $dataSet    = new PHPUnit\DbUnit\DataSet\ArrayDataSet(
            include __DIR__ . '/../_files/ArrayDataSets/FilteredTestComparison.php'
        );

        $filteredDataSet = new PHPUnit\DbUnit\DataSet\Filter($dataSet);
        $filteredDataSet->addExcludeTables(['table2']);
        $filteredDataSet->setExcludeColumnsForTable('table1', ['table1_id']);
        $filteredDataSet->setExcludeColumnsForTable('table3', ['table3_id']);

        self::assertThat($filteredDataSet, $constraint);
    }

    public function testIncludeFilteredDataSet(): void
    {
        $constraint = new PHPUnit\DbUnit\Constraint\DataSetIsEqual($this->expectedDataSet);
        $dataSet    = new PHPUnit\DbUnit\DataSet\ArrayDataSet(
            include __DIR__ . '/../_files/ArrayDataSets/FilteredTestComparison.php'
        );

        $filteredDataSet = new PHPUnit\DbUnit\DataSet\Filter($dataSet);
        $filteredDataSet->addIncludeTables(['table1', 'table3']);
        $filteredDataSet->setIncludeColumnsForTable('table1', ['column1', 'column2', 'column3', 'column4']);
        $filteredDataSet->setIncludeColumnsForTable('table3', ['column9', 'column10', 'column11', 'column12']);

        self::assertThat($filteredDataSet, $constraint);
    }

    public function testIncludeExcludeMixedDataSet(): void
    {
        $constraint = new PHPUnit\DbUnit\Constraint\DataSetIsEqual($this->expectedDataSet);
        $dataSet    = new PHPUnit\DbUnit\DataSet\ArrayDataSet(
            include __DIR__ . '/../_files/ArrayDataSets/FilteredTestComparison.php'
        );

        $filteredDataSet = new PHPUnit\DbUnit\DataSet\Filter($dataSet);
        $filteredDataSet->addIncludeTables(['table1', 'table3']);
        $filteredDataSet->setExcludeColumnsForTable('table1', ['table1_id']);
        $filteredDataSet->setIncludeColumnsForTable('table3', ['column9', 'column10', 'column11', 'column12']);

        self::assertThat($filteredDataSet, $constraint);
    }
}

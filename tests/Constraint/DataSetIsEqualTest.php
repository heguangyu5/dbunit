<?php
/**
 * @group constraint
 */
class Extensions_Database_Constraint_TableIsEqualTest extends PHPUnit\Framework\TestCase
{
    protected $metadata;
    protected $constraint;

    public function setUp(): void
    {
        $this->metadata = new PHPUnit\DbUnit\DataSet\DefaultTableMetadata(
            'book',
            array('id', 'title'),
            array('id')
        );
        $table = new PHPUnit\DbUnit\DataSet\DefaultTable($this->metadata);
        $table->addRow(array('id' => 1, 'title' => 'phpunit manual'));
        $this->constraint = new PHPUnit\DbUnit\Constraint\TableIsEqual($table);
    }

    public function testMatchesMustBeITable()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('PHPUnit_Extensions_Database_DataSet_ITable expected');

        $this->constraint->evaluate(new stdClass, '', true);
    }

    public function testFailureDescription()
    {
        $this->expectException(PHPUnit\Framework\ExpectationFailedException::class);
        $this->expectExceptionMessage("Failed asserting that \n+----------------------+----------------------+
| book                                        |
+----------------------+----------------------+
|          id          |        title         |
+----------------------+----------------------+
|          2           |       phpunit        |
+----------------------+----------------------+

 is equal to expected (table diff enabled)
+----------------------+----------------------+
| book                                        |
+----------------------+----------------------+
|          id          |        title         |
+----------------------+----------------------+
|    1 != actual 2     | 'phpunit manual' !=  |
+----------------------+----------------------+

.");

        $other = new PHPUnit\DbUnit\DataSet\DefaultTable($this->metadata);
        $other->addRow(array('id' => 2, 'title' => 'phpunit'));

        $this->constraint->evaluate($other, '', false);
    }
}

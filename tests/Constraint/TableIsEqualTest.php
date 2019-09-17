<?php
/**
 * @group constraint
 */
class Extensions_Database_Constraint_DataSetIsEqualTest extends PHPUnit\Framework\TestCase
{
    protected $constraint;

    public function setUp(): void
    {
        $this->constraint = new PHPUnit\DbUnit\Constraint\DataSetIsEqual(
            new PHPUnit\DbUnit\DataSet\ArrayDataSet(array(
                'book' => array(
                    array('id' => 1, 'title' => 'phpunit manual')
                )
            ))
        );
    }

    public function testMatchesMustBeIDataSet()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('PHPUnit_Extensions_Database_DataSet_IDataSet expected');

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

        $other = new PHPUnit\DbUnit\DataSet\ArrayDataSet(array(
            'book' => array(
                array('id' => 2, 'title' => 'phpunit')
            )
        ));
        $this->constraint->evaluate($other, '', false);
    }
}

<?php
/**
 * @group constraint
 */
use PHPUnit\DbUnit\Constraint\TableRowCount;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class Extensions_Database_Constraint_TableRowCountTest extends TestCase
{
    public function testConstraint(): void
    {
        $constraint = new TableRowCount('name', 42);

        $this->assertTrue($constraint->evaluate(42, '', true));
        $this->assertFalse($constraint->evaluate(24, '', true));
        $this->assertEquals('is equal to expected row count 42', $constraint->toString());

        try {
            $this->assertThat(24, $constraint, '');
        } catch (ExpectationFailedException $e) {
            $this->assertEquals(
                'Failed asserting that 24 is equal to expected row count 42.',
                $e->getMessage()
            );
        }
    }
}

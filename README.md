# DbUnit

[PHPUnit](https://phpunit.de/) extension for database interaction testing.

## Installation

put `src` folder in php include path, and rename it to `DbUnit`.

    include 'DbUnit/autoload.php';

    class MyTest extends PHPUnit\DbUnit\TestCase
    {
        protected static $connection;

        protected function getConnection()
        {
            if (!self::$connection) {
                $pdo = new PDO('sqlite::memory:');
                $pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT, title VARCHAR(200), content TEXT)');
                self::$connection = $this->createDefaultDBConnection($pdo, 'sqlite');
            }
            return self::$connection;
        }

        protected function getDataSet()
        {
            return $this->createArrayDataSet(array(
                'posts' => array(
                    array('id' => 1, 'title' => 'hello', 'content' => 'world')
                )
            ));
        }

        public function testMyLogic()
        {
            $this->assertTableRowCount('posts', 1);
        }
    }

<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Database\Driver;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\ORM\EntityManagerInterface;
use JDatabaseDriver;
use JDatabaseQueryPreparable;
use RuntimeException;

/**
 * Class DoctrineDriver
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Database\Driver
 */
class DoctrineDriver extends \JDatabaseDriver implements \Serializable
{
    /**
     * @var EntityManagerInterface
     */
    protected static $entityManager;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    protected $platform;

    /**
     * The name of the database driver.
     *
     * @var    string
     */
    public $name = 'pdo';

    /**
     * @var    Statement  The prepared statement.
     */
    protected $prepared;

    /**
     * Contains the current query execution status
     *
     * @var array
     */
    protected $executed = false;

    /**
     * The character(s) used to quote SQL statement names such as table names or field names,
     * etc. The child classes should define this as necessary.  If a single character string the
     * same character is used for both sides of the quoted name, else the first character will be
     * used for the opening quote and the second for the closing quote.
     *
     * @var    string
     */
    protected $nameQuote = '`';

    /**
     * The null or zero representation of a timestamp for the database driver.  This should be
     * defined in child classes to hold the appropriate value for the engine.
     *
     * @var    string
     */
    protected $nullDate = '0000-00-00 00:00:00';

    /**
     * @var    string  The minimum supported database version.
     */
    protected static $dbMinimum = '5.0.4';

    /**
     * @param  EntityManagerInterface $entityManager
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setEntityManager(EntityManagerInterface $entityManager)
    {
        self::$entityManager = $entityManager;
    }

    /**
     * Constructor.
     *
     * @param   array $options List of options used to configure the connection
     *
     * @throws \RuntimeException If database platform is unsupported
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->connection = self::$entityManager->getConnection();
        $this->platform = $this->connection->getDatabasePlatform();
        $this->driver = $this->connection->getDriver();

        switch (true) {
            case $this->platform instanceof MySqlPlatform:
                $this->name = 'pdomysql';
                break;
            case $this->platform instanceof SqlitePlatform:
                $this->name = 'sqlite';
                break;
            case $this->platform instanceof SQLServerPlatform:
                $this->name = 'sqlsrv';
                break;
            case $this->platform instanceof PostgreSqlPlatform:
                $this->name = 'postgresql';
                break;
            case $this->platform instanceof OraclePlatform:
                $this->name = 'oracle';
                break;
            default:
                throw new \RuntimeException(sprintf('Unsupported Database Platform %s', get_class($this->platform)));
        }
    }

    /**
     * Test to see if the connector is available.
     *
     * @return  bool     True on success, false otherwise.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public static function isSupported()
    {
        return true;
    }

    /**
     * Connects to the database if needed.
     *
     * @return  void
     * @throws RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function connect()
    {
    }

    /**
     * Determines if the connection to the server is active.
     *
     * @return  bool     True if connected to the database engine.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function connected()
    {
        return true;
    }

    /**
     * Disconnects the database.
     *
     * @return  void
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function disconnect()
    {
    }

    /**
     * Execute the SQL statement.
     *
     * @return  mixed  A database cursor resource on success, bool    false on failure.
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function execute()
    {
        // Take a local copy so that we don't modify the original query and cause issues later
        $query = $this->replacePrefix((string) $this->sql);

        if (!($this->sql instanceof JDatabaseQuery) && ($this->limit > 0 || $this->offset > 0)) {
            // @TODO
            $query .= ' LIMIT ' . $this->offset . ', ' . $this->limit;
        }

        // Increment the query counter.
        $this->count++;

        // Reset the error values.
        $this->errorNum = 0;
        $this->errorMsg = '';
        $memoryBefore = null;

        // If debugging is enabled then let's log the query.
        if ($this->debug) {
            // Add the query to the object queue.
            $this->log[] = $query;

            \JLog::add($query, \JLog::DEBUG, 'databasequery');

            $this->timings[] = microtime(true);
        }

        // Execute the query.
        $this->executed = false;
        $previous = null;

        if ($this->prepared instanceof Statement) {
            // Bind the variables:
            if ($this->sql instanceof JDatabaseQueryPreparable) {
                $bounded = $this->sql->getBounded();

                foreach ($bounded as $key => $obj) {
                    $this->prepared->bindParam($key, $obj->value, $obj->dataType, $obj->length, $obj->driverOptions);
                }
            }

            try {
                $this->executed = $this->prepared->execute();
            } catch (\Exception $previous) {
            }
        }

        if ($this->debug) {
            $this->timings[] = microtime(true);

            if (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
                $this->callStacks[] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            } else {
                $this->callStacks[] = debug_backtrace();
            }
        }

        // If an error occurred handle it.
        if (!$this->executed) {
            // Get the error number and message before we execute any more queries.
            $errorNum = (int) $this->connection->errorCode();
            $errorMsg = (string) 'SQL: ' . implode(", ", $this->connection->errorInfo());

            // Get the error number and message from before we tried to reconnect.
            $this->errorNum = $errorNum;
            $this->errorMsg = $errorMsg;

            // Throw the normal query exception.
            \JLog::add(
                \JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg),
                \JLog::ERROR,
                'databasequery'
            );
            throw new RuntimeException($this->errorMsg, $this->errorNum, $previous);
        }

        return $this->prepared;
    }

    /**
     * Get the number of affected rows for the previous executed SQL statement.
     * Only applicable for DELETE, INSERT, or UPDATE statements.
     *
     * @return  int      The number of affected rows.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function getAffectedRows()
    {
        $this->connect();

        if ($this->prepared instanceof Statement) {
            return $this->prepared->rowCount();
        } else {
            return 0;
        }
    }

    /**
     * Get the number of returned rows for the previous executed SQL statement.
     *
     * @param   resource $cursor An optional database cursor resource to extract the row count from.
     *
     * @return  int       The number of returned rows.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function getNumRows($cursor = null)
    {
        $this->connect();

        if ($cursor instanceof Statement) {
            return $cursor->rowCount();
        } elseif ($this->prepared instanceof Statement) {
            return $this->prepared->rowCount();
        } else {
            return 0;
        }
    }

    /**
     * Method to get the auto-incremented value from the last INSERT statement.
     *
     * @return  string  The value of the auto-increment field from the last inserted row.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function insertid()
    {
        $this->connect();

        return $this->connection->lastInsertId();
    }

    /**
     * Select a database for use.
     *
     * @param   string $database The name of the database to select for use.
     *
     * @return  bool     True if the database was successfully selected.
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function select($database)
    {
        $this->connect();

        return true;
    }

    /**
     * Sets the SQL statement string for later execution.
     *
     * @param   mixed $query         The SQL statement to set either as a JDatabaseQuery object or a string.
     * @param   int   $offset        The affected row offset to set.
     * @param   int   $limit         The maximum affected rows to set.
     * @param   array $driverOptions The optional PDO driver options
     *
     * @return  JDatabaseDriver  This object to support method chaining.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function setQuery($query, $offset = null, $limit = null, array $driverOptions = [])
    {
        $this->connect();

        $this->freeResult();

        if (is_string($query)) {
            // Allows taking advantage of bound variables in a direct query:
            $query = $this->getQuery(true)->setQuery($query);
        }

        if ($query instanceof \JDatabaseQueryLimitable && !is_null($offset) && !is_null($limit)) {
            $query->setLimit($limit, $offset);
        }

        $query = $this->replacePrefix((string) $query);

        $this->prepared = $this->connection->prepare($query);

        // Store reference to the JDatabaseQuery instance:
        parent::setQuery($query, $offset, $limit);

        return $this;
    }

    /**
     * Set the connection to use UTF-8 character encoding.
     *
     * @return  bool     True on success.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function setUTF()
    {
        return false;
    }


    /**
     * Method to commit a transaction.
     *
     * @param   bool $toSavepoint If true, commit to the last savepoint.
     *
     * @return  void
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function transactionCommit($toSavepoint = false)
    {
        $this->connect();

        if (!$toSavepoint || $this->transactionDepth == 1) {
            $this->connection->commit();
        }

        $this->transactionDepth--;
    }

    /**
     * Method to roll back a transaction.
     *
     * @param   bool $toSavepoint If true, rollback to the last savepoint.
     *
     * @return  void
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function transactionRollback($toSavepoint = false)
    {
        $this->connect();

        if (!$toSavepoint || $this->transactionDepth == 1) {
            $this->connection->rollBack();
        }

        $this->transactionDepth--;
    }

    /**
     * Method to initialize a transaction.
     *
     * @param   bool $asSavepoint If true and a transaction is already active, a savepoint will be created.
     *
     * @return  void
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function transactionStart($asSavepoint = false)
    {
        $this->connect();

        if (!$asSavepoint || !$this->transactionDepth) {
            $this->connection->beginTransaction();
        }

        $this->transactionDepth++;
    }

    /**
     * Method to fetch a row from the result set cursor as an array.
     *
     * @param   mixed $cursor The optional result set cursor from which to fetch the row.
     *
     * @return  mixed  Either the next row from the result set or false if there are no more rows.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    protected function fetchArray($cursor = null)
    {
        if (!empty($cursor) && $cursor instanceof Statement) {
            return $cursor->fetch(\PDO::FETCH_NUM);
        }

        if ($this->prepared instanceof Statement) {
            return $this->prepared->fetch(\PDO::FETCH_NUM);
        }
    }

    /**
     * Method to fetch a row from the result set cursor as an associative array.
     *
     * @param   mixed $cursor The optional result set cursor from which to fetch the row.
     *
     * @return  mixed  Either the next row from the result set or false if there are no more rows.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    protected function fetchAssoc($cursor = null)
    {
        if (!empty($cursor) && $cursor instanceof Statement) {
            return $cursor->fetch(\PDO::FETCH_ASSOC);
        }

        if ($this->prepared instanceof Statement) {
            return $this->prepared->fetch(\PDO::FETCH_ASSOC);
        }

        return null;
    }

    /**
     * Method to fetch a row from the result set cursor as an object.
     *
     * @param   mixed  $cursor The optional result set cursor from which to fetch the row.
     * @param   string $class  Unused, only necessary so method signature will be the same as parent.
     *
     * @return  mixed   Either the next row from the result set or false if there are no more rows.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    protected function fetchObject($cursor = null, $class = 'stdClass')
    {
        if (!empty($cursor) && $cursor instanceof Statement) {
            $object = $cursor->fetch(\PDO::FETCH_OBJ);
        } else {
            if ($this->prepared instanceof Statement) {
                $object = $this->prepared->fetch(\PDO::FETCH_OBJ);
            }
        }

        if (!isset($object)) {
            return false;
        }

        $newObject = $object;
        if ($class !== 'stdClass') {
            $newObject = new $class;
            foreach (get_object_vars($object) as $key => $value) {
                $newObject->{$key} = $value;
            }
        }

        return $newObject;
    }

    /**
     * Method to free up the memory used for the result set.
     *
     * @param   mixed $cursor The optional result set cursor from which to fetch the row.
     *
     * @return  void
     * @author Maximiian Ruta <mr@xtain.net>
     */
    protected function freeResult($cursor = null)
    {
        $this->executed = false;

        if ($cursor instanceof Statement) {
            $cursor->closeCursor();
            $cursor = null;
        }

        if ($this->prepared instanceof Statement) {
            $this->prepared->closeCursor();
            $this->prepared = null;
        }
    }

    /**
     * Method to get the next row in the result set from the database query as an object.
     *
     * @param   string $class The class name to use for the returned row object.
     *
     * @return  mixed   The result of the query as an array, false if there are no more rows.
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function loadNextObject($class = 'stdClass')
    {
        $this->connect();

        // Execute the query and get the result set cursor.
        if (!$this->executed) {
            if (!($this->execute())) {
                return $this->errorNum ? null : false;
            }
        }

        // Get the next row from the result set as an object of type $class.
        if ($row = $this->fetchObject(null, $class)) {
            return $row;
        }

        // Free up system resources and return.
        $this->freeResult();

        return false;
    }

    /**
     * Method to get the next row in the result set from the database query as an array.
     *
     * @return  mixed  The result of the query as an array, false if there are no more rows.
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function loadNextAssoc()
    {
        $this->connect();

        // Execute the query and get the result set cursor.
        if (!$this->executed) {
            if (!($this->execute())) {
                return $this->errorNum ? null : false;
            }
        }

        // Get the next row from the result set as an object of type $class.
        if ($row = $this->fetchAssoc()) {
            return $row;
        }

        // Free up system resources and return.
        $this->freeResult();

        return false;
    }

    /**
     * Method to get the next row in the result set from the database query as an array.
     *
     * @return  mixed  The result of the query as an array, false if there are no more rows.
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function loadNextRow()
    {
        $this->connect();

        // Execute the query and get the result set cursor.
        if (!$this->executed) {
            if (!($this->execute())) {
                return $this->errorNum ? null : false;
            }
        }

        // Get the next row from the result set as an object of type $class.
        if ($row = $this->fetchArray()) {
            return $row;
        }

        // Free up system resources and return.
        $this->freeResult();

        return false;
    }

    /**
     * Drops a table from the database.
     *
     * @param   string $table    The name of the database table to drop.
     * @param   bool   $ifExists Optionally specify that the table must exist before it is dropped.
     *
     * @return  JDatabaseDriver     Returns this object to support chaining.
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function dropTable($table, $ifExists = true)
    {
        $query = $this->getQuery(true);

        $query->setQuery('DROP TABLE ' . ($ifExists ? 'IF EXISTS ' : '') . $this->quoteName($table));

        $this->setQuery($query);

        $this->execute();

        return $this;
    }

    /**
     * Escapes a string for usage in an SQL statement.
     *
     * @param   string $text  The string to be escaped.
     * @param   bool   $extra Optional parameter to provide extra escaping.
     *
     * @return  string   The escaped string.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function escape($text, $extra = false)
    {
        if (is_int($text) || is_float($text)) {
            return $text;
        }

        $result = substr($this->connection->quote($text), 1, -1);

        if ($extra) {
            $result = addcslashes($result, '%_');
        }

        return $result;
    }

    /**
     * Method to get the database collation in use by sampling a text field of a table in the database.
     *
     * @return  mixed  The collation in use by the database or bool    false if not supported.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function getCollation()
    {
        // TODO
        // Attempt to get the database collation by accessing the server system variable.
        $this->setQuery('SHOW VARIABLES LIKE "collation_database"');
        $result = $this->loadObject();

        if (property_exists($result, 'Value')) {
            return $result->Value;
        } else {
            return false;
        }
    }

    /**
     * Shows the table CREATE statement that creates the given tables.
     *
     * @param   mixed $tables A table name or a list of table names.
     *
     * @return  array  A list of the create SQL for the tables.
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function getTableCreate($tables)
    {
        // TODO
        // Initialise variables.
        $result = [];

        // Sanitize input to an array and iterate over the list.
        settype($tables, 'array');

        foreach ($tables as $table) {
            $this->setQuery('SHOW CREATE TABLE ' . $this->quoteName($table));

            $row = $this->loadRow();

            // Populate the result array based on the create statements.
            $result[$table] = $row[1];
        }

        return $result;
    }

    /**
     * Retrieves field information about the given tables.
     *
     * @param   string $table    The name of the database table.
     * @param   bool   $typeOnly True (default) to only return field types.
     *
     * @return  array  An array of fields by table.
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        // TODO
        $result = [];

        // Set the query to get the table fields statement.
        $this->setQuery('SHOW FULL COLUMNS FROM ' . $this->quoteName($table));

        $fields = $this->loadObjectList();

        // If we only want the type as the value add just that to the list.
        if ($typeOnly) {
            foreach ($fields as $field) {
                $result[$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
            }
        } // If we want the whole field data object add that to the list.
        else {
            foreach ($fields as $field) {
                $result[$field->Field] = $field;
            }
        }

        return $result;
    }

    /**
     * Retrieves field information about the given tables.
     *
     * @param   mixed $tables A table name or a list of table names.
     *
     * @return  array  An array of keys for the table(s).
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function getTableKeys($tables)
    {
        // TODO
        // Get the details columns information.
        $this->setQuery('SHOW KEYS FROM ' . $this->quoteName($tables));

        $keys = $this->loadObjectList();

        return $keys;
    }

    /**
     * Method to get an array of all tables in the database.
     *
     * @return  array  An array of all the tables in the database.
     * @throws  RuntimeException
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function getTableList()
    {
        // TODO
        // Set the query to get the tables statement.
        $this->setQuery($this->platform->getListTablesSQL());
        $tables = $this->loadColumn();

        return $tables;
    }

    /**
     * Get the version of the database connector
     *
     * @return  string  The database connector version.
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getVersion()
    {
        // TODO
        return '';
    }

    /**
     * Locks a table in the database.
     *
     * @param   string $tableName The name of the table to unlock.
     *
     * @return  JDatabaseDriver     Returns this object to support chaining.
     * @throws  RuntimeException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function lockTable($tableName)
    {
        // TODO
        $this->setQuery('LOCK TABLES ' . $this->quoteName($tableName) . ' WRITE')->execute();

        return $this;
    }

    /**
     * Renames a table in the database.
     *
     * @param   string $oldTable The name of the table to be renamed
     * @param   string $newTable The new name for the table.
     * @param   string $backup   Table prefix
     * @param   string $prefix   For the table - used to rename constraints in non-mysql databases
     *
     * @return  JDatabaseDriver    Returns this object to support chaining.
     * @throws  RuntimeException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        // TODO
        $this->setQuery('RENAME TABLE ' . $this->quoteName($oldTable) . ' TO ' . $this->quoteName($newTable));

        $this->execute();

        return $this;
    }

    /**
     * Unlocks tables in the database.
     *
     * @return  JDatabaseDriver Returns this object to support chaining.
     * @throws  RuntimeException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function unlockTables()
    {
        // TODO
        $this->setQuery('UNLOCK TABLES')->execute();

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        $data = array();

        $skip = array(
            'connection',
            'platform',
            'driver'
        );

        foreach ($this as $key => $value) {
            if (in_array($key, $skip)) {
                continue;
            }
            $data[$key] = $value;
        }

        return serialize($data);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        \XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies(__CLASS__);

        $this->connection = self::$entityManager->getConnection();
        $this->platform = $this->connection->getDatabasePlatform();
        $this->driver = $this->connection->getDriver();
    }
}

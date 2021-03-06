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
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;
use JDatabaseQueryPreparable;
use RuntimeException;

/**
 * Class AbstractDoctrineDriver
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Database\Driver
 */
abstract class AbstractDoctrineDriver extends \JDatabaseDriver implements \Serializable
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
     * @var    \Doctrine\DBAL\Driver  The driver.
     */
    protected $driver;

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
     * @var bool
     */
    protected $sessionSet = false;

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
    }

    /**
     * Gets the name of the database used by this conneciton.
     *
     * @return  string
     *
     * @since   11.4
     */
    public function getDatabaseName()
    {
        return $this->connection->getDatabase();
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
        if (!$this->sessionSet && $this->platform instanceof MySqlPlatform) {
            $this->connection->exec("SET @@SESSION.sql_mode = '';");
            $this->sessionSet = true;
        }

        // Take a local copy so that we don't modify the original query and cause issues later
        $query = $this->replacePrefix((string) $this->sql);

        if (!($this->sql instanceof \JDatabaseQuery) && ($this->limit > 0 || $this->offset > 0)) {
            // @TODO
            if ($this->offset > 0) {
                $query .= ' LIMIT ' . $this->offset . ', ' . $this->limit;
            } else {
                $query .= ' LIMIT ' . $this->limit;
            }
        }

        $this->prepared = $this->connection->prepare($query);

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

        /*
        if ($query instanceof \JDatabaseQueryLimitable && !is_null($this->limit) && !is_null($this->offset)) {
            $query->setLimit($this->limit, $this->offset);
        }
        */

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

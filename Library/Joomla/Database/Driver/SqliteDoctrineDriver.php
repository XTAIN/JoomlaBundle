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

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use JDatabaseDriver;
use PDO;
use RuntimeException;

/**
 * Class SqliteDoctrineDriver
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Database\Driver
 */
class SqliteDoctrineDriver extends AbstractDoctrineDriver
{

    /**
     * Retrieve a PDO database connection attribute
     * http://www.php.net/manual/en/pdo.getattribute.php
     *
     * Usage: $db->getOption(PDO::ATTR_CASE);
     *
     * @param   mixed  $key  One of the PDO::ATTR_* Constants
     *
     * @return mixed
     *
     * @since  12.1
     */
    public function getOption($key)
    {
        $this->connect();

        /** @var \PDO $pdo */
        $pdo = $this->connection->getWrappedConnection();

        return $pdo->getAttribute($key);
    }

    /**
     * Retrieve a PDO database connection attribute
     * http://www.php.net/manual/en/pdo.getattribute.php
     *
     * Usage: $db->getOption(PDO::ATTR_CASE);
     *
     * @param   mixed  $key  One of the PDO::ATTR_* Constants
     * @param   mixed  $value
     *
     * @return mixed
     *
     * @since  12.1
     */
    public function setOption($key, $value)
    {
        $this->connect();

        /** @var \PDO $pdo */
        $pdo = $this->connection->getWrappedConnection();

        return $pdo->setAttribute($key, $value);
    }

    /**
     * Return the query string to alter the database character set.
     *
     * @param   string  $dbName  The database name
     *
     * @return  string  The query that alter the database query string
     *
     * @since   12.2
     */
    public function getAlterDbCharacterSet($dbName)
    {
        $charset = $this->utf8mb4 ? 'utf8mb4' : 'utf8';

        return 'PRAGMA encoding = "' . $charset . '"';
    }

    /**
     * This function replaces a string identifier <var>$prefix</var> with the string held is the
     * <var>tablePrefix</var> class variable.
     *
     * @param   string  $query   The SQL statement to prepare.
     * @param   string  $prefix  The common table prefix.
     *
     * @return  string  The processed SQL statement.
     *
     * @since   12.1
     */
    public function replacePrefix($query, $prefix = '#__')
    {
        $query = trim($query);

        $replacedQuery = str_replace($prefix, $this->tablePrefix, $query);

        return $replacedQuery;
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

        $this->name = 'sqlite';

        $c = $this->getTableCreate("#__users");
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
     * Method to get the database collation in use by sampling a text field of a table in the database.
     *
     * @return  mixed  The collation in use by the database or bool    false if not supported.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function getCollation()
    {
        // Attempt to get the database collation by accessing the server system variable.
        $this->setQuery('PRAGMA encoding');
        $result = $this->loadObject();

        if (property_exists($result, 'encoding')) {
            return $result->encoding;
        } else {
            return false;
        }
    }

    /**
     * Method to escape a string for usage in an SQLite statement.
     *
     * Note: Using query objects with bound variables is
     * preferable to the below.
     *
     * @param   string   $text   The string to be escaped.
     * @param   boolean  $extra  Unused optional parameter to provide extra escaping.
     *
     * @return  string  The escaped string.
     *
     * @since   12.1
     */
    public function escape($text, $extra = false)
    {
        if (is_int($text) || is_float($text))
        {
            return $text;
        }

        return \SQLite3::escapeString($text);
    }

    /**
     * Shows the table CREATE statement that creates the given tables.
     *
     * Note: Doesn't appear to have support in SQLite
     *
     * @param   mixed  $tables  A table name or a list of table names.
     *
     * @return  array  A list of the create SQL for the tables.
     *
     * @since   12.1
     * @throws  RuntimeException
     */
    public function getTableCreate($tables)
    {
        $this->connect();

        // Sanitize input to an array and iterate over the list.
        settype($tables, 'array');

        return $tables;
    }

    /**
     * Retrieves field information about a given table.
     *
     * @param   string   $table     The name of the database table.
     * @param   boolean  $typeOnly  True to only return field types.
     *
     * @return  array  An array of fields for the database table.
     *
     * @since   12.1
     * @throws  RuntimeException
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $this->connect();

        $columns = array();
        $query = $this->getQuery(true);

        $fieldCasing = $this->getOption(PDO::ATTR_CASE);

        $this->setOption(PDO::ATTR_CASE, PDO::CASE_UPPER);

        $table = strtoupper($table);

        $query->setQuery('pragma table_info(' . $table . ')');

        $this->setQuery($query);
        $fields = $this->loadObjectList();

        if ($typeOnly)
        {
            foreach ($fields as $field)
            {
                $columns[$field->NAME] = $field->TYPE;
            }
        }
        else
        {
            foreach ($fields as $field)
            {
                // Do some dirty translation to MySQL output.
                // TODO: Come up with and implement a standard across databases.
                $columns[$field->NAME] = (object) array(
                    'Field' => $field->NAME,
                    'Type' => $field->TYPE,
                    'Null' => ($field->NOTNULL == '1' ? 'NO' : 'YES'),
                    'Default' => $field->DFLT_VALUE,
                    'Key' => ($field->PK != '0' ? 'PRI' : '')
                );
            }
        }

        $this->setOption(PDO::ATTR_CASE, $fieldCasing);

        return $columns;
    }

    /**
     * Get the details list of keys for a table.
     *
     * @param   string  $table  The name of the table.
     *
     * @return  array  An array of the column specification for the table.
     *
     * @since   12.1
     * @throws  RuntimeException
     */
    public function getTableKeys($table)
    {
        $this->connect();

        $keys = array();
        $query = $this->getQuery(true);

        $fieldCasing = $this->getOption(PDO::ATTR_CASE);

        $this->setOption(PDO::ATTR_CASE, PDO::CASE_UPPER);

        $table = strtoupper($table);
        $query->setQuery('pragma table_info( ' . $table . ')');

        // $query->bind(':tableName', $table);

        $this->setQuery($query);
        $rows = $this->loadObjectList();

        foreach ($rows as $column)
        {
            if ($column->PK == 1)
            {
                $keys[$column->NAME] = $column;
            }
        }

        $this->setOption(PDO::ATTR_CASE, $fieldCasing);

        return $keys;
    }

    /**
     * Method to get an array of all tables in the database (schema).
     *
     * @return  array   An array of all the tables in the database.
     *
     * @since   12.1
     * @throws  RuntimeException
     */
    public function getTableList()
    {
        $this->connect();

        $type = 'table';

        $query = $this->getQuery(true)
                      ->select('name')
                      ->from('sqlite_master')
                      ->where('type = :type')
                      ->bind(':type', $type)
                      ->order('name');

        $this->setQuery($query);

        $tables = $this->loadColumn();

        return $tables;
    }

    /**
     * Get the version of the database connector.
     *
     * @return  string  The database connector version.
     *
     * @since   12.1
     */
    public function getVersion()
    {
        $this->connect();

        $this->setQuery("SELECT sqlite_version()");

        return $this->loadResult();
    }

    /**
     * Select a database for use.
     *
     * @param   string  $database  The name of the database to select for use.
     *
     * @return  boolean  True if the database was successfully selected.
     *
     * @since   12.1
     * @throws  RuntimeException
     */
    public function select($database)
    {
        $this->connect();

        return true;
    }

    /**
     * Set the connection to use UTF-8 character encoding.
     *
     * Returns false automatically for the Oracle driver since
     * you can only set the character set when the connection
     * is created.
     *
     * @return  boolean  True on success.
     *
     * @since   12.1
     */
    public function setUtf()
    {
        $this->connect();

        return false;
    }

    /**
     * Locks a table in the database.
     *
     * @param   string  $table  The name of the table to unlock.
     *
     * @return  SqliteDoctrineDriver  Returns this object to support chaining.
     *
     * @since   12.1
     * @throws  RuntimeException
     */
    public function lockTable($table)
    {
        return $this;
    }

    /**
     * Renames a table in the database.
     *
     * @param   string  $oldTable  The name of the table to be renamed
     * @param   string  $newTable  The new name for the table.
     * @param   string  $backup    Not used by Sqlite.
     * @param   string  $prefix    Not used by Sqlite.
     *
     * @return  SqliteDoctrineDriver  Returns this object to support chaining.
     *
     * @since   12.1
     * @throws  RuntimeException
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $this->setQuery('ALTER TABLE ' . $oldTable . ' RENAME TO ' . $newTable)->execute();

        return $this;
    }

    /**
     * Unlocks tables in the database.
     *
     * @return  SqliteDoctrineDriver  Returns this object to support chaining.
     *
     * @since   12.1
     * @throws  RuntimeException
     */
    public function unlockTables()
    {
        return $this;
    }

    /**
     * Test to see if the PDO ODBC connector is available.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   12.1
     */
    public static function isSupported()
    {
        return class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers());
    }

    /**
     * Method to commit a transaction.
     *
     * @param   boolean  $toSavepoint  If true, commit to the last savepoint.
     *
     * @return  void
     *
     * @since   12.3
     * @throws  RuntimeException
     */
    public function transactionCommit($toSavepoint = false)
    {
        $this->connect();

        if (!$toSavepoint || $this->transactionDepth <= 1)
        {
            parent::transactionCommit($toSavepoint);
        }
        else
        {
            $this->transactionDepth--;
        }
    }

    /**
     * Method to roll back a transaction.
     *
     * @param   boolean  $toSavepoint  If true, rollback to the last savepoint.
     *
     * @return  void
     *
     * @since   12.3
     * @throws  RuntimeException
     */
    public function transactionRollback($toSavepoint = false)
    {
        $this->connect();

        if (!$toSavepoint || $this->transactionDepth <= 1)
        {
            parent::transactionRollback($toSavepoint);
        }
        else
        {
            $savepoint = 'SP_' . ($this->transactionDepth - 1);
            $this->setQuery('ROLLBACK TO ' . $this->quoteName($savepoint));

            if ($this->execute())
            {
                $this->transactionDepth--;
            }
        }
    }

    /**
     * Method to initialize a transaction.
     *
     * @param   boolean  $asSavepoint  If true and a transaction is already active, a savepoint will be created.
     *
     * @return  void
     *
     * @since   12.3
     * @throws  RuntimeException
     */
    public function transactionStart($asSavepoint = false)
    {
        $this->connect();

        if (!$asSavepoint || !$this->transactionDepth)
        {
            parent::transactionStart($asSavepoint);
        }

        $savepoint = 'SP_' . $this->transactionDepth;
        $this->setQuery('SAVEPOINT ' . $this->quoteName($savepoint));

        if ($this->execute())
        {
            $this->transactionDepth++;
        }
    }


    /**
     * @param AbstractPlatform $platform
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    static public function supportsPlatform(AbstractPlatform $platform)
    {
        return $platform instanceof SqlitePlatform;
    }
}
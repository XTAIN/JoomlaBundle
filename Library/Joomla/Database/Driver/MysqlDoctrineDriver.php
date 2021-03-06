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
use Doctrine\DBAL\Platforms\MySqlPlatform;
use JDatabaseDriver;
use RuntimeException;

/**
 * Class MysqlDoctrineDriver
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Database\Driver
 */
class MysqlDoctrineDriver extends AbstractDoctrineDriver
{
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

        $this->name = 'pdomysql';
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
     * Method to get the database collation in use by sampling a text field of a table in the database.
     *
     * @return  mixed  The collation in use by the database or bool    false if not supported.
     * @author Maximiian Ruta <mr@xtain.net>
     */
    public function getCollation()
    {
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
        $this->getOption(\PDO::ATTR_SERVER_VERSION);
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
        $this->setQuery('UNLOCK TABLES')->execute();

        return $this;
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    static public function supportsPlatform(AbstractPlatform $platform)
    {
        return $platform instanceof MySqlPlatform;
    }
}
<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Database\Query;

/**
 * Class JDatabaseQuerySqlite
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Database\Driver
 */
class JDatabaseQuerySqlite extends \JProxy_JDatabaseQuerySqlite
{
    /**
     * @param string $query
     * @param int    $limit
     * @param int    $offset
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function processLimit($query, $limit, $offset = 0)
    {
        if ($limit > 0 || $offset > 0) {
            if ($offset > 0) {
                $query .= ' LIMIT ' . $offset . ', ' . $limit;
            } else {
                $query .= ' LIMIT ' . $limit;
            }
        }

        return $query;
    }
}

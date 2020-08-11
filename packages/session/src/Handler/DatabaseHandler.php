<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Session\Handler;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Database\Platform\AbstractPlatform;
use Windwalker\Query\Bounded\ParamType;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * Database session storage handler for PHP
 *
 * @see    http://www.php.net/manual/en/function.session-set-save-handler.php
 * @since  2.0
 */
class DatabaseHandler implements HandlerInterface
{
    use OptionAccessTrait;

    /**
     * The DatabaseAdapter to use when querying.
     *
     * @var DatabaseAdapter
     */
    protected DatabaseAdapter $db;

    /**
     * Class init.
     *
     * @param  DatabaseAdapter  $db
     * @param  array            $options
     */
    public function __construct(DatabaseAdapter $db, array $options = [])
    {
        $this->db = $db;

        $this->prepareOptions(
            [
                'table' => 'windwalker_sessions',
                'columns' => [
                    'id' => 'id',
                    'data' => 'data',
                    'time' => 'time',
                ],
            ],
            $options
        );
    }

    /**
     * Re-initializes existing session, or creates a new one.
     *
     * @param  string  $savePath     Save path
     * @param  string  $sessionName  Session name, see http://php.net/function.session-name.php
     *
     * @return bool true on success, false on failure
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Closes the current session.
     *
     * @return bool true on success, false on failure
     */
    public function close()
    {
        return true;
    }

    /**
     * Read the data for a particular session identifier from the SessionHandler backend.
     *
     * @param  string  $id  The session identifier.
     *
     * @return  string  The session data.
     *
     * @throws \Exception
     * @since   2.0
     */
    public function read($id)
    {
        return $this->db->select($this->getOption('columns')['data'])
            ->from($this->getOption('table'))
            ->where($this->getOption('columns')['id'], $id)
            ->result();
    }

    /**
     * Write session data to the SessionHandler backend.
     *
     * @param  string  $id    The session identifier.
     * @param  string  $data  The session data.
     *
     * @return  boolean  True on success, false otherwise.
     * @since   2.0
     */
    public function write($id, $data)
    {
        $columns = $this->getOption('columns');

        $mergeSql = $this->getMergeSql();

        if ($mergeSql !== null) {
            $this->db->createQuery()
                ->sql($mergeSql)
                ->bind('id', $id, ParamType::STRING)
                ->bind('data', $data, ParamType::STRING)
                ->bind('time', time(), ParamType::INT)
                ->execute();

            return true;
        }

        $this->db->transaction(function () use ($id, $data, $columns): StatementInterface {
            $item = [
                $columns['data'] => $data,
                $columns['time'] => (int) time(),
                $columns['id'] => $id,
            ];

            $sess = $this->db->createQuery()
                ->select('*')
                ->from($this->getOption('table'))
                ->where('id', $id)
                ->forUpdate()
                ->get();

            if ($sess === null) {
                return $this->db->getWriter()->insertOne(
                    $this->getOption('table'),
                    $item,
                    $columns['id']
                );
            }

            return $this->db->getWriter()->updateOne(
                $this->getOption('table'),
                $item,
                $columns['id']
            );
        });

        return true;
    }

    /**
     * Destroy the data for a particular session identifier in the SessionHandler backend.
     *
     * @param  string  $id  The session identifier.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @throws \Exception
     * @since   2.0
     */
    public function destroy($id)
    {
        $this->db->delete($this->getOption('table'))
            ->where('id', $id)
            ->execute();

        return true;
    }

    /**
     * Garbage collect stale sessions from the SessionHandler backend.
     *
     * @param  integer  $lifetime  The maximum age of a session.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @throws  \Exception
     * @since   2.0
     */
    public function gc($lifetime = 1440)
    {
        // Determine the timestamp threshold with which to purge old sessions.
        $past = time() - $lifetime;

        $this->db->delete($this->getOption('table'))
            ->where($this->getOption('time'), '<', $past)
            ->execute();

        return true;
    }

    /**
     * Returns a merge/upsert (i.e. insert or update) SQL query when supported by the database.
     *
     * @return string|null The SQL string or null when not supported
     */
    private function getMergeSql(): ?string
    {
        $platformName = $this->db->getPlatform()->getName();

        $columns = $this->getOption('columns');
        $table = $this->getOption('table');

        $query = $this->db->createQuery();

        switch ($platformName) {
            case AbstractPlatform::MYSQL:
                return $query->format(
                    "INSERT INTO %n (%n, %n, %n) VALUES (:id, :data, :time)
ON DUPLICATE KEY UPDATE %n = VALUES(%n), %n = VALUES(%n)",
                    $table,
                    $columns['id_col'],
                    $columns['data_col'],
                    $columns['time_col'],
                    $columns['data_col'],
                    $columns['data_col'],
                    $columns['time_col'],
                    $columns['time_col']
                );

            case 'oci':
                // DUAL is Oracle specific dummy table
                return $query->format(
                    " MERGE INTO %n USING DUAL ON (%n = :id) WHEN NOT MATCHED
                    THEN INSERT (%n, %n, %n) VALUES (:id, :data, :time) WHEN MATCHED
                    THEN UPDATE SET %n = :data, %n = :time",
                    $table,
                    $columns['id_col'],
                    $columns['id_col'],
                    $columns['data_col'],
                    $columns['time_col'],
                    $columns['data_col'],
                    $columns['time_col']
                );

            case AbstractPlatform::SQLSERVER === $platformName && version_compare(
                    $this->db->getDriver()->getVersion(),
                    '10',
                    '>='
                ):
                // @codingStandardsIgnoreStart
                // MERGE is only available since SQL Server 2008 and must be terminated by semicolon
                // It also requires HOLDLOCK according to http://weblogs.sqlteam.com/dang/archive/2009/01/31/UPSERT-Race-Condition-With-MERGE.aspx
                return $query->format(
                    "MERGE INTO %n WITH (HOLDLOCK) USING (SELECT 1 AS dummy) AS src ON (%n = :id)
                    WHEN NOT MATCHED THEN INSERT (%n, %n, %n) VALUES (:id, :data, :time)
                    WHEN MATCHED THEN UPDATE SET %n = :data, %n = :time;",
                    $table,
                    $columns['id_col'],
                    $columns['id_col'],
                    $columns['data_col'],
                    $columns['time_col'],
                    $columns['data_col'],
                    $columns['time_col']
                );

            case AbstractPlatform::SQLITE:
                return $query->format(
                    "INSERT OR REPLACE INTO %n (%n, %n, %n) VALUES (:id, :data, :time)",
                    $table,
                    $columns['id_col'],
                    $columns['data_col'],
                    $columns['time_col']
                );
        }

        return null;
    }
}

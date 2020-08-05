<?php

namespace App\Resources\Data;

use FFI\Exception;
use Symfony\Component\Console\Exception\LogicException;
use Illuminate\Database\Connectors\SQLiteConnector;
use Illuminate\Database\QueryException;
use PDOStatement;

class BaseMapper {
    /**
     * Database instance that will connect to a SQLite instance
     * 
     * @var PDO
     */
    protected $db;

    public function __construct()
    {
        $this->db = app('db');
    }

    /**
     * These methods are used to streamline generating SQLite queries.
     */


     /**
      * Returns a query that will create a table for the fields supplied. 
      *
      * Note: If a table exists already with the given name, that table may not have the same desired fields.
      *
      * @param string $table
      * @param array $fields
      * @return string
      * @throws LogicException
      */
    protected function getCreateTableQuery(string $table, array $fields=[]) : string
    {
        if ($fields === []){
            throw new LogicException("Please provide at least 1 field for the table.");
        }

        $q = "CREATE TABLE IF NOT EXISTS {$table} (" . join(", ", $fields) . ")";
        return $q;
    }

    /**
     * Returns a query to get documents given the provided fields, their conditions, and a supplied order.
     *
     * @param string $table
     * @param array $fields
     * @param array $conditions
     * @param array $order
     * @return string
     */
    protected function getSelectQuery(string $table, array $fields=["*"], array $conditions=[], array $order=[]) : string
    {
        $q = "SELECT " . join(', ', $fields) . " FROM {$table}";

        if ($conditions) {
            $q .= " WHERE " . join(" AND " , $conditions);
        }

        if ($order) {
            $q .= " ORDER BY " . join(", " , $order);
        }
        
        return $q;
    }

    /**
     * Returns an upsert query for a table the given fields and corresponding values
     *
     * @param string $table
     * @param array $fields
     * @param array $values
     * @return string
     */
    protected function getUpsertQuery(string $table, array $fields=["*"], array $values=[]) : string
    {
        if ($fields === []){
            throw new LogicException("Please provide at least 1 field for the table.");
        }

        $q = "INSERT INTO {$table}(" . join(", ", $fields) . ") VALUES ('" . join("', '", $values) . "')";
        $q .= " ON CONFLICT({$fields[0]}) DO UPDATE SET ";

        $cons = [];
        foreach ($fields as $f){
            array_push($cons, "{$f}=excluded.{$f}");
        }

        $q .= join(", ", $cons);
        return $q;
    }

    /**
     * Returns a delete query.
     * 
     * Note: It will delete all documents from the table if no conditions are supplied.
     *
     * @param string $table
     * @param array $conditions
     * @return void
     */
    protected function getDeleteQuery(string $table, array $conditions=[])
    {
        $q = "DELETE FROM {$table}";
        if ($conditions) {
            $q .= " WHERE " . join(" AND " , $conditions);
        }

        return $q;
    } 

    /**
     * Returns all documents for a given table (dictated by the type of mapper)
     * 
     * @return array
     */
    public function findAll() : array
    {
        $q = $this->getSelectQuery($this->TABLE, ["*"]);
        $resp = $this->db->select($q);
        return $this->loadMany($resp);
    }

    /**
     * Used to delete all entries in a given table (dictated by the type of mapper)
     * 
     * @return bool
     */
    public function deleteAll() : bool
    {
        $q = $this->getDeleteQuery($this->TABLE);
        $resp = $this->db->statement($q);
        return $resp;
    }

    /**
     * Used as a wrapper to parse arrays into the single desired object. 
     * Returns null when the passed array is empty or null
     * 
     * @param array|null $dump
     * @return object|null
     */
    protected function load(?array $dump) : ?object
    {
        if (!$dump){
            return null;
        }

        return $this->_load($dump[0]);
    }

    
    /**
     * Used to load responses from queries that expect more than one document
     * 
     * @param array $dump
     * @return array
     */
    protected function loadMany(array $dump) : array
    {
        $messages = [];
        foreach ($dump as $row) {
            array_push($messages, $this->_load($row));
        }

        return $messages;
    }

    /**
     * Placeholder to ensure children classes implement it
     *
     * @param object $dump
     * @return object|null
     */

    protected function _load(object $dump) : ?object
    {
        throw new Exception("Not implemented");
    }

    /**
     * Used to catch QueryExceptions and simply return null when nothing is found
     * 
     * @param string $q
     * @return ?array
     */
    protected function _find(string $q) : ?array
    {
        try {
            $resp = $this->db->select($q);
        } catch (QueryException $e) {
            return null;
        }

        return $resp;
    }


}















?>
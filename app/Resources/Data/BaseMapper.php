<?php

namespace App\Resources\Data;

use FFI\Exception;
use Symfony\Component\Console\Exception\LogicException;
use Illuminate\Database\Connectors\SQLiteConnector;
use Illuminate\Database\QueryException;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDOStatement;

class BaseMapper {

    public function __construct()
    {
        $this->setUpTable();
    }


     /**
     * Placeholder to ensure children classes implement it
     *
     * @return void
     */
    protected function setUpTable()
    {
        throw new Exception("Not implemented");
    }


    /**
     * Returns all documents for a given table (dictated by the type of mapper)
     * 
     * @return array
     */
    public function findAll() : array
    {
        $resp = DB::table($this->TABLE)->get();
        return $this->loadMany($resp);
    }

    /**
     * Used to delete all entries in a given table (dictated by the type of mapper)
     * 
     * @return bool
     */
    public function deleteAll() : bool
    {
        return DB::table($this->TABLE)->delete();
    }

    /**
     * Used as a wrapper to parse arrays into the single desired object. 
     * Returns null when the passed array is empty or null
     * 
     * @param array|null $dump
     * @return object|null
     */
    protected function load(?object $dump) : ?object
    {
        if (!$dump){
            return null;
        }

        return $this->_load($dump);
    }

    
    /**
     * Used to load responses from queries that expect more than one document
     * 
     * @param array $dump
     * @return array
     */
    protected function loadMany(Collection $dump) : array
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
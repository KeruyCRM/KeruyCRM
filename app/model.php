<?php

class Model extends \Prefab
{
    public $db;

    public function __construct()
    {
        //\Cache::instance()->reset('schema'); Сброс кеша
        try {
            $host = \K::$fw->DB_host;
            $port = \K::$fw->DB_port;
            $name = \K::$fw->DB_name;
            if (\K::$fw->TYPE_DATABASE == 'mysql') {
                $this->db = new \DB\SQL(
                    "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
                    \K::$fw->DB_username,
                    \K::$fw->DB_password
                /*,[
                    \PDO::ATTR_TIMEOUT => 5
                ]*/
                );
            } elseif (\K::$fw->TYPE_DATABASE == 'sqlite') {
                $this->db = new \DB\SQL("db/{$name}.sqlite");
            } else {
                exit('Type database not defined');
            }
        } catch (\PDOException $e) {
            //\Flash::instance()->setKey('error', "Error!: " . $e->getMessage());
        }
    }

    public function exec($cmds, $args = null, $ttl = 0, $log = true, $stamp = false)
    {
        return $this->db->exec($cmds, $args, $ttl, $log, $stamp);
    }

    public function mapper($table, $fields = null)
    {
        $mapper = new DB\SQL\Mapper($this->db, $table, $fields, \K::$fw->TTL_SCHEMA);

        $mapper->aftererase(function ($self) {
            \K::cache()->reset($self->table() . '.sql');
        });
        $mapper->afterinsert(function ($self) {
            \K::cache()->reset($self->table() . '.sql');
        });
        $mapper->aftersave(function ($self) {
            \K::cache()->reset($self->table() . '.sql');
        });
        $mapper->afterupdate(function ($self) {
            \K::cache()->reset($self->table() . '.sql');
        });

        return $mapper;
    }

    public function count()
    {
        return $this->db->count();
    }

    public function quote($val, $type = \PDO::PARAM_STR)
    {
        return $this->db->quote($val, $type);
    }

    public function quoteToString($array)
    {
        $array_map = array_map(['self', 'quote'], $array);
        return implode(',', $array_map);
    }

    public function quotekey($key, $split = true)
    {
        return $this->db->quotekey($key, $split);
    }

    public function db_fetch_all($table, $column = null, $ttl = null)
    {
        return $this->db_fetch($table, [], [], $column, $ttl);
    }

    public function db_fetch($table, $filter = [], $options = [], $column = null, $ttl = null)
    {
        $mapper = $this->mapper($table, $column);
        return $mapper->find(
            $filter,
            $options,
            $ttl
        );
    }

    public function db_fetch_one($table, $filter = [], $options = [], $column = null, $ttl = null)
    {
        $mapper = $this->mapper($table, $column);
        return $mapper->findone(
            $filter,
            $options,
            $ttl
        )->cast();
    }

    public function db_fetch_count($table, $filter = [], $ttl = null)
    {
        $mapper = $this->mapper($table);
        return $mapper->count(
            $filter,
            $ttl
        );
    }

    public function db_find($table, $value, $column = 'id')
    {
        $mapper = $this->mapper($table, $column);

        $info_query = $mapper->findone(
            [$column . ' = ?', $value]
        );

        if ($info_query) {
            return $info_query->cast();
        } else {
            $schema = $mapper->schema();
            $keys = array_keys($schema);
            return array_fill_keys($keys, '');
        }
    }

    public function db_count($table, $value = '', $column = 'id')
    {
        $mapper = $this->mapper($table);

        return $mapper->count(
            (strlen($value) > 0 ? [$column . ' = ?', $value] : [])
        );
    }

    public function db_delete_row($table, $value, $column = 'id')
    {
        $mapper = $this->mapper($table);
        return $mapper->erase([$column . ' = ?', $value]);
        //db_query("delete from " . $table . " where " . $column . "='" . db_input($value) . "'");
    }

    public function db_query($query, $debug = false, $link = 'db_link')
    {
        //global $$link, $app_db_query_log, $app_db_slow_query_log;
        return $this->db->exec($query);

        if (\K::$fw->DEV_MODE) {
            $starttime = microtime(true);
        }

        if ($debug) {
            echo '<div class="alert alert-warning" style="font-size: 11px; margin: 5px; padding: 3px; font-family:monospace;">' . htmlspecialchars(
                    $query
                ) . '</div><br>';
        }

        try {
            $result = mysqli_query($$link, $query);
        } catch (mysqli_sql_exception $e) {
            $html = '
            <app_db_error>
            <div style="color: #b94a48; background: #f2dede; border: 1px solid #eed3d7; padding: 5px; margin: 5px; font-family: verdana; font-size: 12px; line-height: 1.5;">
              <div><strong>Database Error:</strong> ' . $e->getCode() . ' - ' . htmlspecialchars($e->getMessage()) . '</div>
              <div><strong>Query:</strong> ' . htmlspecialchars($query) . '</div>
              <div><strong>Page: </strong> ' . $_SERVER['REQUEST_URI'] . '</div>
              <div style="padding-top: 10px;">' . nl2br($e->getTraceAsString()) . '</div>
            </div>
        ';
            die($html);
        }

        if (\K::$fw->DEV_MODE) {
            $time = number_format((microtime(true) - $starttime), 3);
            if ($time > \K::$fw->CFG_SLOW_QUERY_TIME) {
                $app_db_slow_query_log[] = $query . ' [' . $time . ']';
            } else {
                $app_db_query_log[] = $query . ' [' . $time . ']';
            }
        }

        return $result;
    }

    /*public function db_load($table, $fields = null, $filter = null, $option = null)
     {
         $mapper = $this->mapper($table, $fields);
         $mapper->load($filter, $option);

         return $mapper;
     }*/

    public function db_perform($table, $data, $action = 'insert', $parameters = [], $debug = false)
    {
        $mapper = $this->mapper($table);
        if ($parameters) {
            $mapper->load($parameters);
        }

        foreach ($data as $columns => $value) {
            $mapper->{$columns} = $value;
        }

        return $mapper->save();
    }

    public function db_insert_id($mapper)
    {
        return $mapper->get('_id');
    }
}
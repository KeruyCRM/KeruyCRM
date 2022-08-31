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
                    , [
                        //\PDO::ATTR_TIMEOUT => 5
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                    ] + (\K::$fw->DB_FORCE_SQL_MODE ? [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="' . \K::$fw->DB_SET_SQL_MODE . '"'] : [])
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

    public function begin()
    {
        return $this->db->begin();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function trans()
    {
        return $this->db->trans();
    }

    public function mapper($table, $fields = null)
    {
        $mapper = new DB\SQL\Mapper($this->db, $table, $fields, \K::$fw->TTL_SCHEMA);

        if (!$cache = \K::cache()->get($table . '.tags')) {
            $cache = [];
        }

        $mapper->aftererase(function ($self) use ($cache) {
            \K::cache()->reset($self->table() . '.sql');
            foreach ($cache as $key => $value) {
                \K::cache()->reset($key . '.sql');
            }
        });
        $mapper->afterinsert(function ($self) use ($cache) {
            \K::cache()->reset($self->table() . '.sql');
            foreach ($cache as $key => $value) {
                \K::cache()->reset($key . '.sql');
            }
        });
        $mapper->aftersave(function ($self) use ($cache) {
            \K::cache()->reset($self->table() . '.sql');
            foreach ($cache as $key => $value) {
                \K::cache()->reset($key . '.sql');
            }
        });
        $mapper->afterupdate(function ($self) use ($cache) {
            \K::cache()->reset($self->table() . '.sql');
            foreach ($cache as $key => $value) {
                \K::cache()->reset($key . '.sql');
            }
        });

        return $mapper;
    }

    public function schema()
    {
        return new \DB\SQL\Schema($this->db);
    }

    public function getTables()
    {
        return $this->schema()->getTables();
    }

    public function count()
    {
        return $this->db->count();
    }

    public function quote($val, $type = \PDO::PARAM_STR)
    {
        return $this->db->quote($val, $type);
    }

    public function quoteToString($array, $type = \PDO::PARAM_STR)
    {
        $array_map = array_map(function ($val) use ($type) {
            return $this->quote($val, $type);
        }, $array);
        return implode(',', $array_map);
    }

    public function quotekey($key, $split = true)
    {
        return $this->db->quotekey($key, $split);
    }

    public function db_fetch($table, $filter = [], $options = [], $column = null, $virtualFields = [], $ttl = 'auto')
    {
        $ttl = $this->getTTL($table, $ttl);

        $mapper = $this->mapper($table, $column);

        foreach ($virtualFields as $field => $value) {
            $mapper->{$field} = $value;
        }

        return $mapper->find(
            $filter,
            $options,
            $ttl
        );
    }

    public function db_fetch_split($sql_query)
    {
        return self::db_fetch(
            $sql_query['table'],
            $sql_query['filter'],
            $sql_query['options'],
            $sql_query['column']
        );
    }

    public function db_fetch_one(
        $table,
        $filter = [],
        $options = [],
        $column = null,
        $virtualFields = [],
        $ttl = 'auto'
    ) {
        $ttl = $this->getTTL($table, $ttl);

        $mapper = $this->mapper($table, $column);

        foreach ($virtualFields as $field => $value) {
            $mapper->{$field} = $value;
        }

        $value = $mapper->findone(
            $filter,
            $options,
            $ttl
        );

        if ($value) {
            return $value->cast();
        } else {
            return false;
        }
    }

    public function db_fetch_count($table, $filter = [], $ttl = 'auto')
    {
        $ttl = $this->getTTL($table, $ttl);

        $mapper = $this->mapper($table);
        return $mapper->count(
            $filter,
            $ttl
        );
    }

    public function db_find($table, $value, $column = 'id', $fields = null, $ttl = 'auto')
    {
        $ttl = $this->getTTL($table, $ttl);

        $mapper = $this->mapper($table, $fields);

        $info_query = $mapper->findone(
            [$column . ' = ?', $value],
            null,
            $ttl
        );

        if ($info_query) {
            return $info_query->cast();
        } else {
            return $this->db_show_columns($table);
        }
    }

    public function db_count($table, $value = '', $column = 'id', $ttl = 'auto')
    {
        $ttl = $this->getTTL($table, $ttl);

        $mapper = $this->mapper($table);

        return $mapper->count(
            (strlen($value) > 0 ? [$column . ' = ?', $value] : []),
            null,
            $ttl
        );
    }

    public function db_delete_row($table, $value, $column = 'id')
    {
        return self::db_delete($table, [$column . ' = ?', $value]);
    }

    public function db_delete($table, $filter = null)
    {
        $mapper = $this->mapper($table);
        $mapper->load($filter);
        return $mapper->erase();
    }

    public function db_query_exec($cmds, $args = null, $tagCache = '', $log = true, $stamp = false)
    {
        if ($tagCache) {
            $tagCache = str_replace(' ', '', $tagCache);

            $exp = explode(',', $tagCache);
            sort($exp);
            $tagCache = implode(',', $exp);

            if (!$ttlQuery = \K::cache()->get($tagCache . '.ttl')) {
                $flip = array_flip($exp);

                $intersect = array_intersect_key(\K::$fw->TTL_CACHE_TABLE, $flip);

                if (count($intersect)) {
                    sort($intersect);//Get MIN value TTL for table
                    $ttlQuery = array_shift($intersect);
                } else {
                    $ttlQuery = \K::$fw->TTL_QUERY;
                }

                \K::cache()->set($tagCache . '.ttl', $ttlQuery, \K::$fw->TTL_FOR_MIN_TTL_TABLE);
            }

            if (!$cache = \K::cache()->get('tags')) {
                $cache = [];
            }

            if (!isset($cache[$tagCache])) {
                $cache[$tagCache] = '';
                \K::cache()->set('tags', $cache, 60 * 60 * 24 * 365);

                foreach ($exp as $value) {
                    if (!$cache = \K::cache()->get($value . '.tags')) {
                        $cache = [];
                    }

                    if (!isset($cache[$tagCache])) {
                        $cache[$tagCache] = '';
                        \K::cache()->set($value . '.tags', $cache, 60 * 60 * 24 * 365);
                    }
                }
            }

            $ttl = [$ttlQuery, $tagCache];
        } else {
            $ttl = 0;
        }

        return $this->db->exec($cmds, $args, $ttl, $log, $stamp);
    }

    public function db_query_exec_one($cmds, $args = null, $tagCache = '', $log = true, $stamp = false)
    {
        $query = $this->db_query_exec($cmds, $args, $tagCache, $log, $stamp);
        return $query[0] ?? '';
    }

    public function db_query_($query, $debug = false, $link = 'db_link')
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

    public function db_perform($table, $data, $parameters = [], $update = false, $debug = false)
    {
        $mapper = $this->mapper($table);
        if ($parameters) {
            $mapper->load($parameters);
        }

        foreach ($data as $columns => $value) {
            if (is_null($value)) {
                $value = '';
            }

            $mapper->{$columns} = $value;
        }

        if ($update) {
            return $mapper->update();
        }
        return $mapper->save();
    }

    public function db_update($table, $data, $parameters = [], $debug = false)
    {
        return $this->db_perform($table, $data, $parameters, true, $debug);
    }

    public function db_insert_id($mapper)
    {
        return $mapper->get('_id');
    }

    public function db_prepare_input($string)
    {
        if (is_string($string)) {
            return \Helpers\App::app_sanitize_string($string);
        } elseif (is_array($string)) {
            reset($string);

            foreach ($string as $key => $value) {
                $string[$key] = $this->db_prepare_input($value);
            }
            return $string;
        } else {
            return $string;
        }
    }

    public function db_show_columns($table)
    {
        $mapper = $this->mapper($table);

        $schema = $mapper->schema();
        $keys = array_keys($schema);
        return array_fill_keys($keys, '');
    }

    public function db_has_encryption_key()
    {
        return \K::fw()->exists('DB_ENCRYPTION_KEY') and strlen(\K::$fw->DB_ENCRYPTION_KEY);
    }

    private function getTTL($table, $ttl)
    {
        if (isset(\K::$fw->TTL_CACHE_TABLE[$table])) {
            return [\K::$fw->TTL_CACHE_TABLE[$table], $table];
        }

        if ($ttl == 'auto') {
            return [\K::$fw->TTL_QUERY, $table];
        }

        return $ttl;
    }

    public function db_check_privileges(
        $required_privileges = ['Select', 'Insert', 'Update', 'Delete', 'Create', 'Drop', 'Alter']
    ) {
        //check user privileges
        $user_privileges_list = [];
        $user_privileges_query = $this->db_query_exec('SHOW PRIVILEGES');

        //while ($user_privileges = db_fetch_array($user_privileges_query)) {
        foreach ($user_privileges_query as $user_privileges) {
            $user_privileges_list[] = $user_privileges['Privilege'];
        }

        foreach ($required_privileges as $v) {
            if (!in_array($v, $user_privileges_list)) {
                die ('Error: "' . $v . '" privilege for mysql user is required. Please update privileges for user "' . \K::$fw->DB_SERVER_USERNAME . '"');
            }
        }
    }

    public function db_prepare_html_input($html)
    {
        if (!strlen($html)) {
            return '';
        }

        $html = preg_replace(['#<script(.*?)>(.*?)</script>#is', '#<script(.*?)>#is'], '', $html);

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('HTML.Trusted', true);
        $purifier = new \HTMLPurifier($config);

        return $purifier->purify($html);
    }

    function db_input_protect($string)
    {
        return preg_replace('/[\W|_]+/u', '_', \K::fw()->clean($string));
    }

    public function forceCommit()
    {
        if (!$this->trans()) {
            $this->begin();
            return true;
        }
        return false;
    }
}
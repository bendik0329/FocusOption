<?php

class Dal
{
    /**
     * Represents an array of db-connections.
     * 
     * @var array
     */
    private $dbConfig;
    
    /**
     * PDO::instance
     * 
     * @var PDO
     */
    protected $connection;
    
    /**
     * Indicates whether the connection is established.
     * 
     * @var bool
     */
    protected $isConnected;
    
    /**
     * Represents database table name.
     * 
     * @var string
     */
    protected $table;
    
    /**
     * Array of parameters to bind before executing sql query.
     * 
     * @var array
     */
    protected $bindParamsMap;
    
    /**
     * Represents an amount of parameters to bind before executing sql query.
     * 
     * @var int
     */
    protected $paramsMapCounter;
    
    /**
     * Represents type of database (mysql, pgsql, etc...).
     * 
     * @var string
     */
    protected $dbDriver;
    
    /**
     * Name of choosen database connection.
     * 
     * @var string
     */
    private $db;
    
    /**
     * Constructor.
     * 
     * @param string $db
     * @param string $table
     * @param array  $arrDbConf
     */
    public function __construct($db, $table = '', array $arrDbConf = [])
    {
        $this->dbConfig = [
            'dal' => [
                'dbdriver'  => 'mysql',
                'host'      => $arrDbConf['db_hostname'],
                //'port'      => '3306',
                'dbname'    => $arrDbConf['db_name'],
                'username'  => $arrDbConf['db_username'],
                'password'  => $arrDbConf['db_password'],
            ],
        ];
        
        $this->connection       = null;
        $this->isConnected      = false;
        $this->table            = $table;
        $this->dbDriver         = $this->dbConfig[$db]['dbdriver'];
        $this->bindParamsMap    = array();
        $this->paramsMapCounter = 0;
        $this->db               = $db;
    }
    
    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->disconnect();
    }
    
    /**
     * Creates a new PDO connection, if the provided name is valid.
     * Otherwise returns null.
     * 
     * @param  string $name
     * @return mixed
     */
    private function getConnectionByName($name)
    {
        if (isset($this->dbConfig[$name])) {
            $pdo = new PDO(
                $this->dbConfig[$name]['dbdriver'] . ':host='   . 
                $this->dbConfig[$name]['host']     . ';port='   .
                $this->dbConfig[$name]['port']     . ';dbname=' .
                $this->dbConfig[$name]['dbname'],
                $this->dbConfig[$name]['username'],
                $this->dbConfig[$name]['password']
            );
            
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
            
        } else {
            return null;
        }
    }
    
    /**
     * Resets PDO connection, if the provided name is valid.
     * Otherwise returns null.
     * 
     * @param  string $name
     * @return void
     */
    protected function setConnectionByName($name)
    {
        $this->disconnect();
        $this->connection  = $this->getConnectionByName($name);
        $this->isConnected = true;
    }
    
    /**
     * Connects to the given database.
     * 
     * @param  void
     * @return void
     */
    protected function connect()
    {
        if (!$this->isConnected) {
            $this->connection  = $this->getConnectionByName($this->db);
            $this->isConnected = true;
        }
    }
    
    /**
     * Closes database connection.
     * 
     * @param  void
     * @return void
     */
    protected function disconnect()
    {
        if ($this->connection) {
            $this->connection = null;
        }
        
        $this->isConnected   = false;
        $this->bindParamsMap = array();
    }
    
    /**
     * Inserts a new value into parameters map and returns a token,
     * in order to indentify it later at binding time.
     * 
     * @param  mixed $value
     * @return string
     */
    public function paramsMapInsert($value)
    {
        $this->paramsMapCounter++;
        $this->bindParamsMap[':p' . $this->paramsMapCounter] = $value;
        return ':p' . $this->paramsMapCounter;
    }
    
    /**
     * Builds 'where' clause of sql.
     * 
     * @param  array  $paramsArr
     * @param  bool   $noBind
     * @return string
     * 
     * Sample:
     * $paramsArr = array('username' => array('glue'     => 'AND | OR',  // default: 'AND'
     *                                        'operator' => 'IN',
     *                                        'value'    => array('pasha', 'nadya')),
     *                    'role'     => array('operator' => '>',
     *                                        'value'    => 1),
     *                    'email'    => 'test@mail.ru',
     * 
     *                    'custom'   => "Here can be placed some complicated condition.
     *                                   REMARKS:
     *                                   1. MUST start the condition with 'OR' or 'AND'.
     *                                   2. DO NOT finish the condition with 'OR' or 'AND'.");
     * 
     * Remark: the purpose of $noBind is to disable parameters binding during sql query execution.
     */
    public function where($paramsArr, $noBind = false)
    {
        $where           = ' WHERE 1 = 1 ';
        $firstItaration  = true;
        $where          .= isset($paramsArr['custom']) ? ' ' . $paramsArr['custom'] . ' ' : '';
        unset($paramsArr['custom']);
        
        foreach ($paramsArr as $field => $arr) {
            if (is_array($arr)) {
                $where .= !$firstItaration && isset($arr['glue']) 
                        ? ' '     . $arr['glue'] . ' ' .  $field . ' '    . $arr['operator'] . ' ' 
                        : ' AND '                      .  $field . ' '    . $arr['operator'] . ' ';
                
                if (is_array($arr['value'])) {
                    if ($arr['operator'] == 'BETWEEN' || $arr['operator'] == 'NOT BETWEEN') {
                        // As it currently stands, a length of $arr['value'] is for sure 2.
                        if ($noBind) {
                            $where .= ' ' . $arr['value'][0] . ' AND '
                                   .  ' ' . $arr['value'][1] . ' ';
                        } else {
                            $where .= ' ' . $this->paramsMapInsert($arr['value'][0]) . ' AND '
                                   .  ' ' . $this->paramsMapInsert($arr['value'][1]) . ' ';
                        }
                        
                    } else {
                        $where .= '(';

                        foreach ($arr['value'] as $value) {
                            if ($noBind) {
                                $where .= ' ' . $value . ' ,';
                            } else {
                                $where .= ' ' . $this->paramsMapInsert($value) . ' ,';
                            }
                        }

                        $where = substr($where, 0, strlen($where) - 1);  // Removes the last comma.
                        $where .= ')';
                    }
                    
                } else {
                    // As it currently stands, $arr['value'] in not array.
                    if ($noBind) {
                        $where .= ' ' . $arr['value'] . ' ';
                    } else {
                        $where .= ' ' . $this->paramsMapInsert($arr['value']) . ' ';
                    }
                }
                
            } else {
                // As it currently stands, $arr is scalar.
                if ($noBind) {
                    $where .= ' AND ' . $field . ' = ' . $arr . ' ';
                } else {
                    $where .= ' AND ' . $field . ' = ' . $this->paramsMapInsert($arr) . ' ';
                }
            }
            
            $firstItaration = false;
        }
        return $where;
    }
    
    /**
     * Builds 'having' clause of sql.
     * 
     * @param  array $paramsArr
     * @return string
     */
    public function having($paramsArr)
    {
        $where = $this->where($paramsArr);
        return ' HAVING ' . substr($where, 5, strlen($where) - 6);
    }
    
    /**
     * Builds the 'join' sql clause.
     * 
     * @param  array  $paramsArr
     * @return string
     * 
     * Sample: $paramsArr = array('table1' => array('jointype' => 'INNER',
     *                                              'key1'     => 'value1',
     *                                              'key2'     => 'value2'),
     *                            'table2' => array('jointype' => 'LEFT',
     *                                              'key1'     => 'value1'));
     */
    public function join($paramsArr)
    {
        $join = ' ';
        
        foreach ($paramsArr as $table => $arr) {
            $join .= isset($arr['jointype']) ? $arr['jointype'] : 'INNER';
            $join .= ' JOIN ' . $table . ' ON ';
        
            foreach ($arr as $k => $v) {
                if ($k != 'jointype') {
                    $join .= $k . ' = ' . $v . ' AND ';
                }
            }
            $join = substr($join, 0, strlen($join) - 4);  // Removes the last 'AND'.
        }
        return $join;
    }
    
    /**
     * Builds 'order by' sql clause.
     * 
     * @param  array  $paramsArr
     * @return string
     * 
     * Sample: $paramsArr = array('column1' => 'ASC|DESC',
     *                            'column2' => 'ASC|DESC');
     */
    public function orderby($paramsArr)
    {
        $orderby = ' ORDER BY ';
        
        foreach ($paramsArr as $column => $order) {
            $orderby .= $column . ' ' . $order . ',';
        }
        return substr($orderby, 0, strlen($orderby) - 1);
    }
    
    /**
     * Builds the 'select' sql clause.
     * 
     * @param  array $paramsArr
     * @return string
     * 
     * Sample: $paramsArr['select'] = array('id' => 'id', 'email' => 'email');
     */
    public function selectColumns($paramsArr)
    {
        $select = 'SELECT ';
        
        foreach ($paramsArr as $column => $alias) {
            if ('*' == $column || '*' == $alias) {
                $select .= ' *,';
                continue;
            }
            
            $select .= $column . ' AS ' . $alias  . ',';
        }
        return substr($select, 0, strlen($select) - 1);
    }
    
    /**
     * Builds the 'group by' sql clause.
     * 
     * @param  array $paramsArr
     * @return string
     * 
     * Sample: $paramsArr = array('col1', 'col2', 'col3');
     */
    public function groupby($paramsArr)
    {
        $groupby = ' GROUP BY';
        
        foreach ($paramsArr as $column) {
            $groupby .= ' ' . $column . ',';
        }
        return substr($groupby, 0, strlen($groupby) - 1) . ' ';
    }
    
    /**
     * Builds 'limit' sql clause.
     * 
     * @param  array $paramsArr
     * @return string
     * 
     * Sample: $paramsArr = array('offset' => 0, 'limit' => 2);
     */
    public function limit($paramsArr)
    {
        $offset = isset($paramsArr['offset']) ? ' ' . $paramsArr['offset'] . ' '  : ' 0 ';
        $limit  = isset($paramsArr['limit'])  ? ' ' . $paramsArr['limit']  . ' '  : ' 1 ';
        return $this->dbDriver == 'mysql' ? " LIMIT $offset , $limit " : " LIMIT $limit OFFSET $offset ";
    }
    
    /**
     * Representing an amount of affected rows.
     * 
     * @param  int $affectedRows
     * @return string
     */
    protected function formatMessage($affectedRows)
    {
        switch ($affectedRows) {
            case 0:
                return 'No rows affected';
            case 1:
                return 'One row affected';
            default:
                return $affectedRows . ' rows affected';
        }
    }
    
    /**
     * Custom SQL query.
     * Remark: parameters binding cannot be performed automatically, only manualy.
     * 
     * @param  string $sql
     * @param  bool   $debug
     * @param  bool   $toJson
     * @return mixed
     */
    public function query($sql, $debug = false, $toJson = false)
    {
        try {
            $this->connect();
            $stmt = $this->connection->prepare($sql);
            
            if ($debug) {
                $this->debug($sql);
            }
            
            $this->bindParams($stmt);
            $stmt->execute();
            $this->bindParamsMap = array();
            
            if ($toJson) {
                return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            } else {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
        } catch (PDOException $e) {
            $this->disconnect();
            
            if ($toJson) {
                return json_encode($debug ? array('Error' => $this->generateError($e)) : array('Error' => 'Error'));
            } else {
                return $debug ? $this->generateError($e) : 'Error';
            }
        }
    }
    
    /**
     * Custom SQL query.
     * Remark: parameters binding cannot be performed automatically, only manualy.
     * 
     * @param  string $sql
     * @param  int    $columnToFetch
     * @param  bool   $debug
     * @return mixed
     */
    public function fetchColumn($sql, $columnToFetch = 0, $debug = false)
    {
        try {
            $this->connect();
            $stmt = $this->connection->prepare($sql);
        
            if ($debug) {
                $this->debug($sql);
            }
        
            $this->bindParams($stmt);
            $stmt->execute();
            $this->bindParamsMap = array();
            return $stmt->fetchColumn($columnToFetch);
            
        } catch (PDOException $e) {
            $this->disconnect();
            return $debug ? $this->generateError($e) : 'Error';
        }
    }
    
    /**
     * Counts records from given table, under given conditions.
     * 
     * @param  array  $paramsArr
     * @param  bool   $debug
     * @return string
     * 
     * Sample: $paramsArr = array('table' => 'table_name',
     *                            'join'  => array(...build join array here...),
     *                            'where' => array(...build where array here...));
     */
    public function count($paramsArr = null, $debug = false)
    {
        $sql  = 'SELECT COUNT(*) AS count FROM ';
        $sql .= isset($paramsArr['table']) ? $paramsArr['table'] : $this->table;
        
        if (is_array($paramsArr)) {
            $sql .= isset($paramsArr['join'])  ? $this->join($paramsArr['join'])   : '';
            $sql .= isset($paramsArr['where']) ? $this->where($paramsArr['where']) : '';
        }
        
        if ($debug) {
            $this->debug($sql);
        }
        
        try {
            $this->connect();
            $stmt = $this->connection->prepare($sql);
            $this->bindParams($stmt);
            $stmt->execute();
            $this->bindParamsMap = array();
            return $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            $this->disconnect();
            return $debug ? $this->generateError($e) : 'Error';
        }
    }
    
    /**
     * Returns all the records from the given table, under given conditions.
     * 
     * @param  array  $paramsArr
     * @param  bool  $debug
     * @param  bool  $toJson
     * @return string
     * 
     * Sample: $paramsArr = array('table'   => 'table_name',
     *                            'select'  => array('col1' => 'alias1',
     *                                               'col2' => 'alias2'),
     *                            'join'    => array('jointype' => 'INNER',
     *                                               'key1'     => 'value1',
     *                                               'key2'     => 'value2'),
     *                            'where'   => array('username' => array('operator' => 'IN',
     *                                                                   'value'    => array('Jacob', 'Ron')),
     *                                               'role'     => array('operator' => '>',
     *                                                                   'value'    => 1)),
     *                            'groupby' => array(col1, col2, col3),
     *                            'having'  => array(SAME STRUCTURE AS "WHERE"),
     *                            'orderby' => array('column1' => 'ASC|DESC',
     *                                               'column2' => 'ASC|DESC'),
     *                            'limit'   => array('offset' => 0, 'limit' => 5));
     */
    public function select($paramsArr = null, $debug = false, $toJson = false)
    {
        $sql = 'SELECT * FROM ' . $this->table;
        
        if (is_array($paramsArr)) {
            $sql  = isset($paramsArr['select']) ? $this->selectColumns($paramsArr['select']) : 'SELECT *';
            $sql .= ' FROM ';
            $sql .= isset($paramsArr['table'])   ? $paramsArr['table']                   : $this->table;
            $sql .= isset($paramsArr['join'])    ? $this->join($paramsArr['join'])       : '';
            $sql .= isset($paramsArr['where'])   ? $this->where($paramsArr['where'])     : '';
            $sql .= isset($paramsArr['groupby']) ? $this->groupby($paramsArr['groupby']) : '';
            $sql .= isset($paramsArr['having'])  ? $this->having($paramsArr['having'])   : '';
            $sql .= isset($paramsArr['orderby']) ? $this->orderby($paramsArr['orderby']) : '';
            $sql .= isset($paramsArr['limit'])   ? $this->limit($paramsArr['limit'])     : '';
        }
        
        if ($debug) {
            $this->debug($sql);
        }
        
        try {
            $this->connect();
            $stmt = $this->connection->prepare($sql);
            $this->bindParams($stmt);
            $stmt->execute();
            $this->bindParamsMap = array();
            
            if ($toJson) {
                return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                
            } else {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
        } catch (PDOException $e) {
            $this->disconnect();
            
            if ($toJson) {
                return json_encode($debug ? array('Error' => $this->generateError($e)) : array('Error' => 'Error'));
            } else {
                return $debug ? $this->generateError($e) : 'Error';
            }
        }
    }
    
    /**
     * Returns comprehensive information about the given error.
     * 
     * @param  PDOException $e
     * @return string
     */
    protected function generateError(PDOException $e)
    {
        return '<table border = "1">' .
               '<tr><td>Err code</td><td>'  . $e->getCode()    . '</td></tr>' .
               '<tr><td>In file </td><td>'  . $e->getFile()    . '</td></tr>' .
               '<tr><td>In line </td><td>'  . $e->getLine()    . '</td></tr>' .
               '<tr><td>Details </td><td>'  . $e->getMessage() . '</td></tr></table>';
    }
    
    /**
     * Inserts a new record to given table.
     * Returns string, indecating whether sql 'insert' succedded or failed.
     * 
     * @param  array $paramsArr
     * @param  bool  $debug
     * @return int|string
     * 
     * Sample: $paramsArr = array('table'   => 'table_name',
     *                            'columns' => array(col1, col2, col3),
     *                                         array(value1, value2, value3),
     *                                         array(value1, value2, value3),
     *                                         array(value1, value2, value3));
     */
    public function insert($paramsArr, $debug = false)
    {
        $insert = 'INSERT INTO ';
		
		if (isset($paramsArr['table'])) {
			$insert .= $paramsArr['table'];
			unset($paramsArr['table']);
		} else {
			$insert .= $this->table;
		}
		
        $insert .= '(' . implode(',', $paramsArr['columns']) . ') VALUES ';
        unset($paramsArr['columns']);
        
        foreach ($paramsArr as $k => $v) {
            $insert .= '(';
            
            foreach ($v as $singleValue) {
                $insert .= ' ' . $this->paramsMapInsert($singleValue) . ' ,';
            }
            $insert = substr($insert, 0, strlen($insert) - 1) . '),';
        }
        $insert = substr($insert, 0, strlen($insert) - 1) . ';';
        
        if ($debug) {
            $this->debug($insert);
        }
        
        try {
            $this->connect();
            $stmt = $this->connection->prepare($insert);
            $this->bindParams($stmt);
            $stmt->execute();
            $this->bindParamsMap = array();
            
            $rowCount = $stmt->rowCount();
            return is_numeric($rowCount) ? $rowCount : 0;
            
        } catch (PDOException $e) {
            $this->disconnect();
            return $debug ? $this->generateError($e) : 'Error';
        }
    }
    
    /**
     * Inserts a new record to given table.
     * 
     * @param  array $paramsArr
     * @param  bool  $debug
     * @return bool|string
     * 
     * Sample: $paramsArr = array('table' => 'table_name', 'column' => 'value', 'column2' => 'value2');
     */
    public function insertSingle($paramsArr, $debug = false)
    {
        $insert     = 'INSERT INTO ';
        $strColumns = ' (';
        $strValues  = ' VALUES(';
        
        if (isset($paramsArr['table'])) {
            $insert .= $paramsArr['table'];
            unset($paramsArr['table']);
        } else {
            $insert .= $this->table;
        }
        
        foreach ($paramsArr as $k => $v) {
            $strColumns .= $k . ',';
            $strValues  .= $this->paramsMapInsert($v) . ',';
        }
        
        $strColumns = substr($strColumns, 0, strlen($strColumns) - 1) . ')';
        $strValues  = substr($strValues,  0, strlen($strValues)  - 1) . ');';
        $insert    .= $strColumns . $strValues;
        
        if ($debug) {
            $this->debug($insert);
        }
        
        try {
            $this->connect();
            $stmt = $this->connection->prepare($insert);
            $this->bindParams($stmt);
            
            if ($stmt->execute()) {
                $this->bindParamsMap = array();
                return true;
            }
            
            $this->bindParamsMap = array();
            return false;
            
        } catch (PDOException $e) {
            $this->disconnect();
            return $debug ? $this->generateError($e) : false;
        }
    }
    
    /**
     * Deletes choosen records from given table.
     * 
     * @param  array $paramsArr
     * @param  bool  $debug
     * @return string
     * 
     * Sample: $paramsArr = array('table' => 'table_name', 
     *                            'where' => array(...build params array for "where()"...));
     */
    public function delete($paramsArr, $debug = false)
    {
        $delete = 'DELETE FROM ';
		
		if (isset($paramsArr['table'])) {
			$delete .= $paramsArr['table'];
			unset($paramsArr['table']);
		} else {
			$delete .= $this->table;
		}
		
        $delete .= isset($paramsArr['where']) ? $this->where($paramsArr['where']) : '';
        
        if ($debug) {
            $this->debug($delete);
        }
        
        try {
            $this->connect();
            $stmt = $this->connection->prepare($delete);
            $this->bindParams($stmt);
            $stmt->execute();
            $this->bindParamsMap = array();
            $rowCount = $stmt->rowCount();
            
            return is_numeric($rowCount) ? $rowCount : 0;
            
        } catch (PDOException $e) {
            $this->disconnect();
            return $debug ? $this->generateError($e) : 'Error';
        }
    }
    
    /**
     * Updates choosen columns of choosen records of choosen table.
     * 
     * @param  array $paramsArr
     * @param  bool  $debug
     * @return bool
     * 
     * Sample: $paramsArr = array('table' => 'table_name',
     *                            'where' => array('username' => array('operator' => 'IN',
     *                                                                 'value'    => array('Dima', 'Oleg'))),
     *                            
     *                            // In case of MySQL join
     *                            'join' => array('table1' => array('jointype' => 'INNER',
     *                                                              'key1'     => 'value1',
     *                                                              'key2'     => 'value2'),
     *                                            'table2' => array('jointype' => 'LEFT',
     *                                                              'key1'     => 'value1')),
     * 
     *                            // In case of PostgreSQL join
     *                            'from' => array(...list of tables to join here...),
     *                            // OR if only one table will be joined
     *                            'from' => 'shema_name.table_name',
     * 
     *                            // List of columns at the target table to update.
     *                            'col1' => $newValue1,
     *                            'col2' => $newValue2);
     */
    public function update($paramsArr, $debug = false)
    {
        $update = 'UPDATE ';
        $where  = '';
        
		if (isset($paramsArr['table'])) {
			$update .= $paramsArr['table'];
			unset($paramsArr['table']);
		} else {
			$update .= $this->table;
		}
		
        if (isset($paramsArr['where'])) {
            $where = $this->where($paramsArr['where']);
            unset($paramsArr['where']);
        }
        
        if ($this->dbDriver == 'mysql') {
            $update .= isset($paramsArr['join']) ? $this->join($paramsArr['join']) : '';
        }
        
        $update .= ' SET ';
        
        foreach ($paramsArr as $column => $value) {
            if ($column == 'join' || $column == 'from') {
                continue;
            } else {
                $update .= $column . ' = ' . $this->paramsMapInsert($value) . ' ,';
            }
        }
        
        $update  = substr($update, 0, strlen($update) - 1);
        
        if ($this->dbDriver == 'pgsql') {
            if (isset($paramsArr['from'])) {
                if (is_array($paramsArr['from'])) {
                    $update .= ' FROM ' . implode(',', $paramsArr['from']) . ' ';
                } else {
                    $update .= ' FROM ' . $paramsArr['from'] . ' ';
                }
            }
        }
        
        $update .= $where . ' ;';
        
        if ($debug) {
            $this->debug($update);
        }
        
        try {
            $this->connect();
            $stmt = $this->connection->prepare($update);
            $this->bindParams($stmt);
            $stmt->execute();
            $this->bindParamsMap = array();
            $rowCount = $stmt->rowCount();
            
            return is_numeric($rowCount) ? $rowCount : 0;
            
        } catch (PDOException $e) {
            $this->disconnect();
            return $debug ? $this->generateError($e) : 'Error';
        }
    }
    
    /**
     * Binds an actual values to reserved placeholders.
     * 
     * @param  PDO::Statment $stmt
     * @return void
     */
    protected function bindParams($stmt)
    {
        foreach ($this->bindParamsMap as $k => &$v) {
            if (is_numeric($v)) {
                $stmt->bindParam($k, $v, PDO::PARAM_INT);
            } elseif (is_bool($v)) {
                $stmt->bindParam($k, $v, PDO::PARAM_BOOL);
            } elseif (is_null($v)) {
                $stmt->bindParam($k, $v, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam($k, $v, PDO::PARAM_STR);
            }
        }
        $this->paramsMapCounter = 0;
    }
    
    /**
     * Shows an actual SQL query before execution.
     * 
     * @param  string $sql
     * @return void
     */
    protected function debug($sql)
    {
        $sql = str_replace(',', ' , ', $sql); // PATCH.
        $sql = str_replace('(', ' ( ', $sql); // PATCH.
        $sql = str_replace(')', ' ) ', $sql); // PATCH.
        
        $sql    = explode(' ', $sql);
        $result = '';
        
        foreach ($sql as $word) {
            if (!empty($word)) {
                if (':' == $word[0]) {
                    $result .= is_string($this->bindParamsMap[$word])
                             ? "'"     . $this->bindParamsMap[$word] . "' "
                             :           $this->bindParamsMap[$word] . ' ';
                    
                } else {
                    $result .= $word . ' ';
                }
            } else {
                $result .= $word . ' ';
            }
        }
        
        echo '<pre>', $result, '</pre>';
    }
}


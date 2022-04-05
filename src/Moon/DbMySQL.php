<?php
declare(strict_types=1);
namespace Moon;

class DbMySQL {
    private $querynum = 0;
    private $config = array();
    private $sqldebug = array();

    private $pdo = null;
    private $statement = null;
    private $error_info = array();

    private $sql = '';

    function __construct($config = array()) {
        if(!empty($config)) {
            $this->set_config($config);
        }
    }

    function set_config($config) {
        $this->config = &$config;
    }

    public function connect() {
        $this->pdo = new PDO(
            $this->config['dbdsn'], 
            $this->config['dbuser'],
            $this->config['dbpwd'],
            array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4')
        );
        // $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //,PDO::ATTR_STATEMENT_CLASS => array('MyPDOStatement', array())
    }

    public function query($sql, $args = null) {
        $sql = $this->parse_sql($sql, $args);
        $this->sql = $sql;

        if ($args) {
            $this->statement = $this->pdo->prepare($sql);
            if ($this->statement === false) {
                $this->error($sql, true);
            }
            $this->statement->setFetchMode(PDO::FETCH_NAMED);
            $tmp = $this->statement->execute($args);
            if ($tmp === false) {
                $this->error($sql, true, $this->statement->errorInfo());
            }
        } else {
            $this->statement = $this->pdo->query($sql, PDO::FETCH_NAMED);
            if ($this->statement === false) {
                $this->error($sql, true);
            }
        }

        return true;
    }

    public function fetch_all($sql, $args = null) {
        $this->query($sql, $args);
        return $this->statement->fetchAll();
    }

    public function fetch_first($sql, $args = null) {
        $this->query($sql, $args);
        return $this->statement->fetch(PDO::FETCH_NAMED);
    }

    public function result_first($sql, $args = null) {
        $this->query($sql, $args);
        $item = $this->statement->fetchColumn(0);
        return $item;
    }

    public function execute($sql, $args = null) {
        $this->query($sql, $args);
        return $this->affected_rows();
    }

    public function delete($table, $condition = '', $condition_args = '') {
        $sql = "delete from `{$table}` ";
        if($condition){
            $sql .= ' where '.$condition;
        }
        return $this->execute($sql, $condition_args);
    }

    public function insert($table, $data, $batch = false, $replace = false) {
        $sql = '';
        if ($replace) {
            $cmd = 'replace';
        }else{
            $cmd = 'insert';
        }
        // 如果是一个空插入，单独处理
        if (empty($data)) {
            $sql = $cmd." into `{$table}` () values ()";
            return $this->execute($sql);
        }

        if ($batch === true) {
            $fields = array_keys($data[0]);
        } else {
            $fields = array_keys($data);
        }

        $sql = $cmd." into `{$table}` (`";
        $sql .= implode('`,`', $fields);
        $sql .= '`) values (';
        $sql .= ':' . implode(',:', $fields);
        $sql .= ')';

        if ($batch === false) {
            return $this->execute($sql, $data);
        }

        $this->statement = $this->pdo->prepare($sql);
        foreach ($data as $key => &$row) {
            $this->statement->execute($row);
        }
        unset($row);
        return count($data);

    }


    public function update($table, $data, $condition, $condition_args) {
        $sql = "update `{$table}` set ";
        $fields = array_keys($data);

        $temp = '';
        foreach ($data as $key => $value) {
            $sql .= $temp."`{$key}`=:{$key}";
            $temp = ',';
        }

        $sql .= ' where '.$condition;
        $args = array_merge($data, $condition_args);

        return $this->execute($sql, $args);
    }

    public function affected_rows() {
        return $this->statement->rowCount();
    }

    public function quote($value) {
        return $this->pdo->quote($value);
    }

    // 有一些pdo无法处理的需要单独处理
    // in条件，key固定为%n
    private function parse_sql($sql, &$args) {
        $search = $replace = array();

        // 处理%n
        if (isset($args['%n'])) {
            $in_args = $args['%n'];
            unset($args['%n']);
            foreach ($in_args as $key => $value) {
                $in_args[$key] = $this->quote($value);
            }
            $search[] = '%n';
            $replace[] = implode(',', $in_args);
        }

        // 处理%begin
        if (isset($args['%begin'])) {
            $search[] = '%begin';
            $replace[] = $args['%begin'];
            unset($args['%begin']);
        }
        // 处理%pagecount
        if (isset($args['%pagecount'])) {
            $search[] = '%pagecount';
            $replace[] = $args['%pagecount'];
            unset($args['%pagecount']);
        }

        return str_replace($search, $replace, $sql);
    }

    function last_sql() {
        return $this->sql;
    }

    private function error($sql = '', $halt = false, $error_obj = false) {
        if ($error_obj) {
            $tmp = $error_obj;
        } else {
            $tmp = $this->pdo->errorInfo();
        }
        
        $this->error_info['sql_state'] = $tmp[0];
        $this->error_info['driver_state'] = $tmp[1];
        $this->error_info['message'] = $tmp[2];

        $this->halt("sql:{$sql}\n<br />[{$tmp['0']}] {$tmp[2]}", $tmp[0]);
    }


    private function halt($message = '', $code = 0, $sql = '') {
        throw new DbException($message, 0, $sql);
    }

    function last_id() {
        return $this->pdo->lastInsertId();
    }

    function begin_transaction() {
        return $this->pdo->beginTransaction();
    }
    
    function commit() {
        return $this->pdo->commit();
    }
    
    function rollback() {
        return $this->pdo->rollback();
    }

}


class DbException extends \Exception {
    private $sql = '';

    public function __construct($message, $code = 0, $sql = '') {
        $this->sql = $sql;

        parent::__construct($message, $code);
    }

    public function get_sql() {
        return $this->sql;
    }
}

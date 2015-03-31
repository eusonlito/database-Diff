<?php namespace Database\Diff\Parsers\Mysql;

class Column
{
    private $name;
    private $type;
    private $unsigned;
    private $null;
    private $auto_increment;
    private $length;
    private $decimal;
    private $default;
    private $values;

    public static function getParseExp()
    {
        return '/`?([a-z0-9$\_,-]+[^`\s])`?\s*(\w+)(\([^\)]+\))?\s*([^,]*)/i';
    }

    public static function getParseAlterExp()
    {
        return '/ALTER TABLE `?([a-zA-Z0-9$\_,]+[^`\s])`?'
            .'\s*(ADD|DROP|MODIFY|CHANGE)\s*(?:COLUMN)\s*'
            .'`?([a-z0-9$\_,]+[^`\s])`?'
            .'\s*`?([a-z0-9_-]+)`?\s*(\(`?([a-z0-9_-]+)`?\))?'
            .'([^;]*);/i';
    }

    public static function parse($sql)
    {
        preg_match(self::getParseExp(), trim(str_replace(["\n", "\r"], '', $sql)), $m);

        return $m;
    }

    public static function getAll($sql)
    {
        $columns = [];

        preg_match_all(self::getParseExp(), $sql, $m);

        foreach ($m[0] as $i => $column) {
            $columns[$m[1][$i]] = new self($column);
        }

        return $columns;
    }

    public function __construct($sql)
    {
        $this->sql = self::parse($sql);

        $this->setParameters();

        return $this;
    }

    private function setParameters()
    {
        $params = $this->sql[3].' '.$this->sql[4];

        $this->name = $this->sql[1];
        $this->type = $this->sql[2];
        $this->unsigned = stristr($params, ' unsigned') ? true : false;
        $this->null = stristr($params, ' not null') ? false : true;
        $this->auto_increment = stristr($params, ' auto_increment') ? true : false;

        if (preg_match('/default ([0-9\.]+|["\']([^"\']*)["\'])/', $params, $m)) {
            $this->default = isset($m[2]) ? $m[2] : (int)$m[1];
        }

        $class = 'setParameters'.$this->type;

        if (method_exists($this, $class)) {
            $this->$class($params);
        } else {
            $this->setParametersDefault($params);
        }
    }

    private function setParametersDefault($params)
    {
        if (preg_match('/^\(([0-9]+)\)/', $params, $m)) {
            $this->length = (int)$m[1];
        } elseif (preg_match('/^\(([0-9]+),\s*([0-9]+)/', $params, $m)) {
            $this->length = (int)$m[1];
            $this->decimal = (int)$m[2];
        }
    }

    private function setParametersEnum($params)
    {
        $this->values = [];

        preg_match('/^\(([^\)]+)/', $params, $m);

        foreach (explode(',', $m[1]) as $value) {
            $value = trim($value);
            $this->values[] = substr($value, 1, strlen($value) - 2);
        }
    }

    public function alter($sql)
    {
        dd($sql);
    }

    public function __toString()
    {
        return '`'.$this->name.'`'
            .' '.$this->type
            .$this->lengthString()
            .($this->unsigned ? ' unsigned' : '')
            .($this->null ? ' null' : ' not null')
            .($this->auto_increment ? ' auto_increment' : '');
    }

    private function lengthString()
    {
        if (empty($this->length)) {
            return '';
        }

        return ' ('.$this->length.($this->decimal ? (','.$this->decimal) : '').')';
    }
}
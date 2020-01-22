<?php

abstract class Entity {

    const ID='id';

    public function update() {
        $class=get_called_class();
        $id=$class::ID;
        return DB::update($class::TABLENAME, $this->getColumnValues(), $id);
    }

    public function insert() {
        $class=get_called_class();
        $values=$this->getColumnValues();
        $id=$class::ID;
        unset($values[$id]);
        $this->$id=DB::insert($class::TABLENAME, $values, $id);
        return $this->$id;
    }

    public function upsert() {
        $class=get_called_class();
        if (is_null($this->{$class::ID})) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    protected function getColumnValues(): array {
        return get_object_vars($this);
    }

    public function inputPostInteger($param) {
        $this->$param=filter_input(INPUT_POST, $param, FILTER_VALIDATE_INT);
    }

    public function inputPostString($param) {
        $this->$param=filter_input(INPUT_POST, $param);
    }

    public function inputPostTextarea($param) {
        $this->$param=filter_input(INPUT_POST, $param);
    }

    public function inputPostDate($param) {
        $this->$param=filter_input(INPUT_POST, $param);
    }

    public function inputPostDatetime($param) {
        $this->$param=filter_input(INPUT_POST, $param);
    }

    public static function fetch($id): ?self {
        $class=get_called_class();
        $s=DB::prepare('SELECT * FROM '.$class::TABLENAME.' WHERE '.$class::ID.'=?');
        $s->execute([$id]);
        $s->setFetchMode(PDO::FETCH_CLASS, $class);
        return $s->fetch();
    }

}

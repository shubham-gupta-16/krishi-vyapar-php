<?php
class QueryManager
{

    public function __construct(string $q, int $limit = 3)
    {
        $this->qArr = explode(" ", $q, $limit);
        $this->oc = 1;
        $this->q = $q;
    }

    public function getBaseLogic(array $columns, $pre, $post):string
    {
        $q = $this->q;
        $str = '';
        for ($i = 0; $i < sizeof($columns); $i++) {
            $col = $columns[$i];
            if ($i != 0) {
                $str .= "OR";
            }
            $str .= " $col LIKE '$pre$q$post' ";
        }
        return $str;
    }
    public function getIndividualLogic(array $columns, string $pre, string $post): string
    {
        $pro = '';
        for ($i = 0; $i < sizeof($this->qArr); $i++) {
            $val = $this->qArr[$i];
            if ($i != 0)
            for ($j=0; $j < sizeof($columns); $j++) { 
                $col = $columns[$j];
                $pro .= " OR $col LIKE '$pre$val$post' ";
            }
        }
        return $pro;
    }

    private function def_0(string $column, string $val, string $pre = '%', string $post = '%'): string
    {
        return "$column LIKE '$pre$val$post'";
    }
    function def(string $column, string $val): string
    {
        $def_1 = $this->def_0($column, $val, '', '');
        $def_2 = $this->def_0($column, $val, '', '%');
        // $def_3 = $this->def_0($column, $val, '%', '%');
        $def =
            "\n WHEN $def_1 THEN " . $this->oc++ .
            "\n WHEN $def_2 THEN " . $this->oc++ .
            // "\n WHEN $def_3 THEN " . $this->oc++ .
            "";
        // "$def_1 OR $def_2 OR $def_3";
        return $def;
    }

    public function getGroupOrder(array $columns, string $pre, string $post): string
    {
        $pro = '';
        $fr = true;
        foreach ($this->qArr as $val) {
            if (!$fr)
                $pro .= ' AND ';
            $first = true;
            $def = '';
            foreach ($columns as $col) {
                if (!$first)
                    $def .= " OR ";
                $def .= $this->def_0($col, $val, $pre, $post);
                $first = false;
            }
            $pro .= "($def)";
            $fr = false;
        }
        return "\n WHEN $pro THEN " . $this->oc++;
    }

    public function getInidividualOrder(array $c): string
    {
        $str = '';
        foreach ($this->qArr as $val) {
            foreach ($c as $col) {
                $str .= $this->def($col, $val);
            }
        }
        return $str;
    }

    public function orderElseCount(): int
    {
        return $this->oc;
    }
}

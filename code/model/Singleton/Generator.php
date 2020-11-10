<?php

namespace Kelvinho\Virus\Singleton;

class Generator {
    private array $list;
    private array $keys;
    private int $index;
    private int $length;

    public function __construct(array $list) {
        $this->list = $list;
        $this->keys = array_keys($list);
        $this->index = 0;
        $this->length = count($list);
    }

    public function next() {
        if ($this->length === $this->index) return null;
        return $this->list[$this->keys[$this->index++]];
    }
}

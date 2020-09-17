<?php

namespace Kelvinho\Virus\Singleton;

class Generator {
    private array $list;
    private int $index;

    public function __construct(array $list) {
        $this->list = $list;
        $this->index = 0;
    }

    public function next() {
        if (count($this->list) === $this->index) return null;
        $this->index++;
        return $this->list[$this->index - 1];
    }
}
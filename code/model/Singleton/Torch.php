<?php

namespace Kelvinho\Virus\Singleton;

class Torch {
    public static function range(int $n, int $start = 0): array {
        $answer = [];
        for ($i = 0; $i < $n; $i++) $answer[$i] = $i + $start;
        return $answer;
    }

    public static function reverse(array $a): array {
        $answer = [];
        $n = count($a);
        for ($i = 0; $i < $n; $i++) array_push($answer, $a[$n - $i - 1]);
        return $answer;
    }

    public static function clone(array $a): array {
        $answer = [];
        foreach ($a as $el) $answer[] = $el;
        return $answer;
    }

    public static function zeros(int $n): array {
        $answer = [];
        for ($i = 0; $i < $n; $i++) $answer[] = 0;
        return $answer;
    }
}
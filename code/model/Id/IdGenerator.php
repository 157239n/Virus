<?php


namespace Kelvinho\Virus\Id;

/**
 * Interface IdGenerator, responsible for generating new ids
 *
 * @package Kelvinho\Virus\Id
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
interface IdGenerator {
    /**
     * Creates a new virus id. Guarantees to be unique.
     *
     * @return string The 64-character-long virus id
     */
    public function newVirusId(): string;

    /**
     * Creates a new attack id. Guarantees to be unique.
     *
     * @return string The 64-character-long attack id
     */
    public function newAttackId(): string;
}
<?php

namespace Kelvinho\Virus\Virus;

/**
 * Interface VirusFactory. Responsible for instantiating viruses and creating new ones.
 *
 * @package Kelvinho\Virus\Virus
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
interface VirusFactory {
    /**
     * Gets a virus from a virus id. If there are no viruses with that id, it returns null
     *
     * @param string $virus_id The virus id
     * @return Virus|null THe virus
     */
    public function get($virus_id): Virus;

    /**
     * Creates a new virus under the current user
     *
     * @param string $user_handle
     * @param bool $standalone
     * @return Virus The virus
     */
    public function new(string $user_handle = null, bool $standalone = true): Virus;

    /**
     * Checks whether the virus id exists or not
     *
     * @param string $virus_id The virus id
     * @return bool Exists?
     */
    public function exists(string $virus_id): bool;
}
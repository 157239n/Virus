<?php

namespace Kelvinho\Virus\Attack;

/**
 * Interface AttackFactory. Responsible for instantiating AttackBase and create new ones.
 *
 * @package Kelvinho\Virus\Attack
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
interface AttackFactory {
    /**
     * Get an attack when given an attack id.
     *
     * @param string $attack_id The attack id
     * @return AttackBase The attack
     */
    public function get(string $attack_id): AttackBase;

    /**
     * Creates a new attack.
     *
     * @param string $virus_id The virus id
     * @param string $attack_package The attack package name
     * @param string $name The attack name
     * @return AttackBase The attack
     * @throws AttackPackageNotFound if the attack package doesn't exist
     */
    public function new(string $virus_id, string $attack_package, string $name): AttackBase;

    /**
     * Checks whether an attack id exists or not.
     *
     * @param string $attack_id The attack id
     * @return bool Whether it exists or not
     */
    public function exists(string $attack_id);
}
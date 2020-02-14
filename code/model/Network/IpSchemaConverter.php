<?php

namespace Kelvinho\Virus\Network;

/**
 * Interface IpSchemaConverter. Converts from different ip address formats (range, cidr, singular, ...) to an ip range
 *
 * @package Kelvinho\Virus\Network
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
interface IpSchemaConverter {
    /**
     * @param string $ipAddressRepresentation An ip address representation, like 192.168.0.1, or 172.16.0.0/12
     * @return bool Whether the ip address representation is valid for this schema
     */
    public function valid(string $ipAddressRepresentation): bool;

    /**
     * Converts an ip address representation to an ip range
     *
     * @param string $ipAddressRepresentation A valid ip address representation
     * @return double[] Array containing first and last ip in the range
     */
    public function convert(string $ipAddressRepresentation): array;
}
<?php

namespace Kelvinho\Virus\Network\Ip;

/**
 * Class Any. Represents every ip possible.
 *
 * @package Kelvinho\Virus\Network\Ip
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Any implements IpSchemaConverter {
    public function valid(string $ipAddressRepresentation): bool {
        return ($ipAddressRepresentation === "*") || ($ipAddressRepresentation === "any");
    }

    public function convert(string $ipAddressRepresentation): array {
        return [
            ip2long("0.0.0.0"),
            ip2long("255.255.255.255")
        ];
    }
}
<?php

namespace Kelvinho\Virus\Network\Ip;

use Kelvinho\Virus\Network\IpSchemaConverter;

/**
 * Class Localhost. Represents localhost, aka loopback interface.
 *
 * @package Kelvinho\Virus\Network\Ip
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Localhost implements IpSchemaConverter {
    public function valid(string $ipAddressRepresentation): bool {
        return $ipAddressRepresentation = "localhost";
    }

    public function convert(string $ipAddressRepresentation): array {
        return [
            ip2long("127.0.0.1"),
            ip2long("127.0.0.1")
        ];
    }
}
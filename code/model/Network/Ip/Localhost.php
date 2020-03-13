<?php

namespace Kelvinho\Virus\Network\Ip;

/**
 * Class Localhost. Represents localhost, aka loopback interface.
 *
 * @package Kelvinho\Virus\Network\Ip
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Localhost extends Single {
    public function valid(string $ipAddressRepresentation): bool {
        return $ipAddressRepresentation == "localhost";
    }

    public function convert(string $ipAddressRepresentation): array {
        return parent::convert("127.0.0.1");
    }
}
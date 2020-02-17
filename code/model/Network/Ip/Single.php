<?php

namespace Kelvinho\Virus\Network\Ip;

/**
 * Class Single. Represents a single, normal ip address.
 *
 * @package Kelvinho\Virus\Network\Ip
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Single implements IpSchemaConverter {
    public function valid(string $ipAddressRepresentation): bool {
        return !!preg_match('/^\d+\.\d+\.\d+\.\d+$/', $ipAddressRepresentation);
    }

    public function convert(string $ipAddressRepresentation): array {
        return [
            ip2long($ipAddressRepresentation),
            ip2long($ipAddressRepresentation)
        ];
    }
}
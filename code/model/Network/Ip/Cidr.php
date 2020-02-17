<?php

namespace Kelvinho\Virus\Network\Ip;

/**
 * Class Cidr. Represents ip addresses with cidr (classless inter-domain routing) notation and looks like 172.16.0.0/12.
 *
 * @package Kelvinho\Virus\Network\Ip
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Cidr implements IpSchemaConverter {
    public function valid(string $ipAddressRepresentation): bool {
        return !!preg_match('/^\d+\.\d+\.\d+\.\d+\/\d+$/', $ipAddressRepresentation);
    }

    public function convert(string $ipAddressRepresentation): array {
        $cidr = explode('/', $ipAddressRepresentation);
        return [
            (ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))),
            (ip2long($cidr[0])) + pow(2, (32 - (int)$cidr[1])) - 1,
        ];
    }
}
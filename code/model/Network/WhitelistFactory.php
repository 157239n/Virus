<?php

namespace Kelvinho\Virus\Network;

use Kelvinho\Virus\Network\Ip\Any;
use Kelvinho\Virus\Network\Ip\Cidr;
use Kelvinho\Virus\Network\Ip\Localhost;
use Kelvinho\Virus\Network\Ip\Single;

/**
 * Class WhitelistFactory. Creates a whitelist.
 *
 * @package Kelvinho\Virus\Network
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class WhitelistFactory {
    public function new(): Whitelist {
        return new Whitelist([
            new Any(),
            new Cidr(),
            new Localhost(),
            new Single()
        ]);
    }
}
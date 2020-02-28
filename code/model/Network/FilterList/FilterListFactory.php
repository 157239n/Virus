<?php

namespace Kelvinho\Virus\Network\FilterList;

use Kelvinho\Virus\Network\Ip\Any;
use Kelvinho\Virus\Network\Ip\Cidr;
use Kelvinho\Virus\Network\Ip\Localhost;
use Kelvinho\Virus\Network\Ip\Single;

/**
 * Class FilterListFactory. Base class of WhitelistFactory and BlacklistFactory.
 *
 * @package Kelvinho\Virus\Network
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
abstract class FilterListFactory {
    protected bool $isWhitelist = true;

    public function new(): FilterList {
        $converters = [
            new Any(),
            new Cidr(),
            new Localhost(),
            new Single()];
        return $this->isWhitelist ? new Whitelist($converters) : new Blacklist($converters);
    }
}
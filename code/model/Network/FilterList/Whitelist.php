<?php

namespace Kelvinho\Virus\Network\FilterList;

/**
 * Class Whitelist. Represents a whitelist. You can add ip addresses that you want to include in, and check if an ip is valid.
 *
 * @package Kelvinho\Virus\Network\Ip\FilterList
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Whitelist extends FilterList {
    protected bool $isWhitelist = true;
}
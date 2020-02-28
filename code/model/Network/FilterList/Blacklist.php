<?php

namespace Kelvinho\Virus\Network\Ip\FilterList;

/**
 * Class Blacklist. Represents a blacklist. You can add ip addresses that you want to filter out, and check if an ip is valid.
 *
 * @package Kelvinho\Virus\Network\Ip\FilterList
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Blacklist extends FilterList {
    protected bool $isWhitelist = false;
}
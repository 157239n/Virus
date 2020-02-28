<?php

use Kelvinho\Virus\Network\Ip\FilterList\Blacklist;
use Kelvinho\Virus\Network\Ip\FilterList\BlacklistFactory;
use PHPUnit\Framework\TestCase;

class BlacklistTest extends TestCase {
    /** @var Blacklist */
    private $blacklist;

    protected function setUp(): void {
        $blacklistFactory = new BlacklistFactory();
        $this->blacklist = $blacklistFactory->new();
    }

    public function testAddIp1() {
        $this->blacklist->addIp("localhost");
        $this->assertFalse($this->blacklist->allowed("127.0.0.1"));
        $this->assertTrue($this->blacklist->allowed("192.168.0.1"));
    }

    public function testAddIp2() {
        $this->blacklist->addIp("*");
        $this->assertFalse($this->blacklist->allowed("123.86.192.255"));
        $this->assertFalse($this->blacklist->allowed("255.255.255.255"));
    }

    public function testAddIp3() {
        $this->blacklist->addIp("192.168.0.0/16");
        $this->assertFalse($this->blacklist->allowed("192.168.255.3"));
        $this->assertFalse($this->blacklist->allowed("192.168.75.0"));
        $this->assertTrue($this->blacklist->allowed("172.17.0.1"));
    }
}

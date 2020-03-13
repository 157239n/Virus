<?php

use Kelvinho\Virus\Network\FilterList\Whitelist;
use Kelvinho\Virus\Network\FilterList\WhitelistFactory;
use PHPUnit\Framework\TestCase;

class WhitelistTest extends TestCase {
    /** @var Whitelist */
    private $whitelist;

    protected function setUp(): void {
        $whitelistFactory = new WhitelistFactory();
        $this->whitelist = $whitelistFactory->new();
    }

    public function testAddIp1() {
        $this->whitelist->addIp("localhost");
        $this->assertTrue($this->whitelist->allowed("127.0.0.1"));
        $this->assertFalse($this->whitelist->allowed("192.168.0.1"));
    }

    public function testAddIp2() {
        $this->whitelist->addIp("*");
        $this->assertTrue($this->whitelist->allowed("123.86.192.255"));
        $this->assertTrue($this->whitelist->allowed("255.255.255.255"));
    }

    public function testAddIp3() {
        $this->whitelist->addIp("192.168.0.0/16");
        $this->assertTrue($this->whitelist->allowed("192.168.255.3"));
        $this->assertTrue($this->whitelist->allowed("192.168.75.0"));
        $this->assertFalse($this->whitelist->allowed("172.17.0.1"));
    }
}

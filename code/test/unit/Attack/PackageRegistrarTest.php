<?php

use Kelvinho\Virus\Attack\PackageRegistrar;
use PHPUnit\Framework\TestCase;

class PackageRegistrarTest extends TestCase {
    private PackageRegistrar $packageRegistrar;

    protected function setUp(): void {
        $mysql_result = $this->createMock("\mysqli_result");
        $mysql_result->method("fetch_assoc")->willReturnOnConsecutiveCalls(
            array("package_name" => "win.oneTime.CollectFile", "class_name" => "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\CollectFile\\CollectFile", "location" => "Windows/OneTime/CollectFile", "display_name" => "easy.CollectFile", "description" => "Collects a bunch of files"),
            array("package_name" => "win.background.MonitorLocation", "class_name" => "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\Background\\MonitorLocation\\MonitorLocation", "location" => "Windows/Background/MonitorLocation", "display_name" => "easy.background.MonitorLocation", "description" => "Continuously monitors for the host's computer"));
        $mysqli = $this->createMock("\mysqli");
        $mysqli->method("query")->willReturn($mysql_result);
        $this->packageRegistrar = new PackageRegistrar($mysqli, __DIR__);
    }

    public function testHasPackages() {
        $this->assertTrue($this->packageRegistrar->hasPackage("win.oneTime.CollectFile"));
        $this->assertTrue($this->packageRegistrar->hasPackage("win.background.MonitorLocation"));
        $this->assertFalse($this->packageRegistrar->hasPackage("win.background.MonitorLocation "));
    }

    public function testGetDescription() {
        $this->assertEquals("Collects a bunch of files", $this->packageRegistrar->getDescription("win.oneTime.CollectFile"));
        $this->assertNotEquals("Collects a bunch of file", $this->packageRegistrar->getDescription("win.oneTime.CollectFile"));
    }

    public function testGetClassName() {
        $this->assertEquals("\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\CollectFile\\CollectFile", $this->packageRegistrar->getClassName("win.oneTime.CollectFile"));
        $this->assertNotEquals("\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\Background\\MonitorLocation\\MonitorLocation", $this->packageRegistrar->getClassName("win.oneTime.CollectFile"));
    }
}
<?php

use Kelvinho\Virus\Auth\AuthenticatorImp;
use PHPUnit\Framework\TestCase;

class AuthenticatorTest extends TestCase {
    public function testAuthenticated() {
        $session = $this->createMock("\Kelvinho\Virus\Session\Session");
        $session->method("has")->willReturn(true);
        $session->method("get")->willReturn("user_A");
        $mysqli = $this->createMock("\mysqli");
        $authenticator = new AuthenticatorImp($session, $mysqli);
        $this->assertTrue($authenticator->authenticated());
        $this->assertTrue($authenticator->authenticated("user_A"));
        $this->assertFalse($authenticator->authenticated("user_B"));
    }

    public function testAuthenticate() {
        $session = $this->createMock("\Kelvinho\Virus\Session\Session");
        $mysqli_result = $this->createMock("\mysqli_result");
        $mysqli_result->method("fetch_assoc")->willReturn(array("password_salt" => "837b7", "password_hash" => "ffdab39054746baf0f9d711e6390587de593986913eb69efec0ad1de1e45c520"));
        $mysqli = $this->createMock("\mysqli");
        $mysqli->method("query")->willReturn($mysqli_result);
        $authenticator = new AuthenticatorImp($session, $mysqli);
        $this->assertTrue($authenticator->authenticate("user", "password"));
        $this->assertFalse($authenticator->authenticate("user", "password1"));
    }
}
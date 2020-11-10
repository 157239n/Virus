<?php

namespace Kelvinho\Virus\User;

use Kelvinho\Virus\Network\Session;
use Kelvinho\Virus\Singleton\Logs;
use Kelvinho\Virus\Timezone\Timezone;
use Kelvinho\Virus\Usage\UsageFactory;
use mysqli;

/**
 * Class UserFactoryImp
 *
 * @package Kelvinho\Virus\User
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class UserFactoryImp implements UserFactory {
    private mysqli $mysqli;
    private UsageFactory $usageFactory;
    private Timezone $timezone;
    private Session $session;
    private ?User $currentUser = null;

    public function __construct(mysqli $mysqli, UsageFactory $usageFactory, Timezone $timezone, Session $session) {
        $this->mysqli = $mysqli;
        $this->usageFactory = $usageFactory;
        $this->timezone = $timezone;
        $this->session = $session;
    }

    public function new(string $user_handle, string $password, string $name, string $timezone = "GMT"): User {
        $password_salt = substr(hash("sha256", rand()), 0, 5);
        $password_hash = hash("sha256", $password_salt . $password);
        $usage = $this->usageFactory->new();

        mkdir(DATA_DIR . "/users/$user_handle");
        if (!$this->mysqli->query("insert into users (user_handle, password_hash, password_salt, name, timezone, resource_usage_id) values ('$user_handle', '$password_hash', '$password_salt', '" . $this->mysqli->escape_string($name) . "', '$timezone', " . $usage->getId() . ")")) Logs::error($this->mysqli->error);

        return $this->get($user_handle);
    }

    public function current(): ?User {
        return isset($this->currentUser) ? $this->currentUser : ($this->currentUser = $this->get($this->session->get("user_handle")));
    }

    public function currentChecked(): User {
        return isset($this->currentUser) ? $this->currentUser : ($this->currentUser = $this->get($this->session->getCheck("user_handle")));
    }

    public function get(?string $user_handle): ?User {
        if ($user_handle === null) return null;
        if (!$this->exists($user_handle)) throw new UserNotFound();
        return new User($user_handle, $this->mysqli, $this->usageFactory, $this->timezone);
    }

    public function exists(string $user_handle): bool {
        if (!$answer = $this->mysqli->query("select user_handle from users where user_handle = '" . $this->mysqli->escape_string($user_handle) . "'")) return false;
        if (!$row = $answer->fetch_assoc()) return false;
        return true;
    }

    public function getAll(): array {
        $user_handles = [];
        if (!$answer = $this->mysqli->query("select user_handle from users")) return [];
        while ($row = $answer->fetch_assoc()) $user_handles[] = $row["user_handle"];
        return $user_handles;
    }
}

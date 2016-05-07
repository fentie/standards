<?php

namespace WIW\Domain\Manager;

use WIW\Domain\Role;
use WIW\Domain\User;

class Manager extends User
{
    protected $role = Role::MANAGER;
}

<?php

namespace WIW\Domain\Employee;

use WIW\Domain\Role;
use WIW\Domain\User;

class Employee extends User
{
    protected $role = Role::EMPLOYEE;
}

<?php

namespace Tests\Domain\User;

use WIW\Domain\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testRolesMustBeValid()
    {
        $user = new User();
        $this->expectException(\UnexpectedValueException::class);
        $user->withRole('invalid role');
    }
}

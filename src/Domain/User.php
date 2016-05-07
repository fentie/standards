<?php
namespace WIW\Domain;

use Equip\Data\EntityInterface;
use Equip\Data\Traits\EntityTrait;

class User implements EntityInterface
{
    use EntityTrait;

    /** @var array */
    protected static $allowedRoles = [Role::EMPLOYEE, Role::MANAGER];

    protected $id;
    protected $name;
    protected $role;
    protected $email;
    protected $phone;
    protected $created_at;
    protected $updated_at;

    protected function types()
    {
        return [
            'id' => 'int',
            'name' => 'string',
            'role' => 'string',
            'email' => 'string',
            'phone' => 'string',
            'created_at' => 'string',
            'updated_at' => 'string',
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $role
     * @return static
     * @throws \UnexpectedValueException
     */
    public function withRole($role)
    {
        if (!in_array($role, self::$allowedRoles, true)) {
            throw new \UnexpectedValueException();
        }
        return $this->withData(['role' => $role]);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = get_object_vars($this);
        unset($data['created_at'], $data['updated_at'], $data['role']);

        return $data;
    }
}

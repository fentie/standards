<?php

namespace WIW\Domain;

use Equip\Data\EntityInterface;
use Equip\Data\Traits\EntityTrait;
use WIW\Domain\Employee\Employee;
use WIW\Domain\Manager\Manager;

class Shift implements EntityInterface
{
    use EntityTrait;

    private $id;
    /** @var Manager */
    private $manager;
    private $manager_id;
    /** @var Employee */
    private $employee;
    private $employee_id;
    private $break;
    private $start_time;
    private $end_time;
    private $created_at;
    private $updated_at;

    public function toArray()
    {
        $data = get_object_vars($this);
        unset($data['manager'], $data['employee']);
        $data['start_time'] = $this->date('start_time')->format(\DateTime::RFC822);
        $data['end_time'] = $this->date('end_time')->format(\DateTime::RFC822);
        $data['created_at'] = $this->date('created_at')->format(\DateTime::RFC822);
        $data['updated_at'] = $this->date('updated_at')->format(\DateTime::RFC822);

        return $data;
    }

    public function getDuration()
    {
        return $this->date('end_time')->diffInHours($this->date('start_time'));
    }

    private function validate()
    {
        if ($this->date('end_time')->lte($this->date('start_time'))) {
            throw new \DomainException('Shift end time must be after start time');
        }
    }
}

<?php

namespace WIW\Domain\Manager;

use PDO;
use WIW\Domain\Role;
use WIW\Domain\Shift;
use WIW\Domain\Employee\Employee;

class ManagerService
{
    /**
     * @var PDO
     */
    private $data;

    public function __construct(PDO $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $name
     * @return Manager
     */
    public function findByName($name)
    {
        $statement = $this->data->prepare('SELECT * FROM users WHERE name = :name AND role = :role LIMIT 1');
        $statement->execute([':name' => $name, ':role' => Role::MANAGER]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return new Manager($row);
    }

    /**
     * @param string $name
     * @return Employee
     */
    public function fetchEmployeeByName($name)
    {
        $statement = $this->data->prepare('SELECT * FROM users WHERE name = :name AND role = :role LIMIT 1');
        $statement->execute([':name' => $name, ':role' => Role::EMPLOYEE]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return new Employee($row);
    }

    /**
     * @param int $employeeId
     * @return Employee
     */
    public function fetchEmployeeById($employeeId)
    {
        $statement = $this->data->prepare('SELECT * FROM users WHERE id = :id AND role = :role LIMIT 1');
        $statement->execute([':id' => $employeeId, ':role' => Role::EMPLOYEE]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return new Employee($row);
    }

    /**
     * @param int $shiftId
     * @return Shift
     */
    public function fetchShiftById($shiftId)
    {
        $statement = $this->data->prepare('SELECT * FROM shifts WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $shiftId]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return new Shift($row);
    }

    /**
     * @param array $shiftData
     * @return Shift
     * @throws \InvalidArgumentException
     */
    public function createShift($shiftData)
    {
        $statement = $this->data->prepare(
            'INSERT INTO shifts(manager_id, employee_id, break, start_time, end_time)
              VALUES (:manager_id, :employee_id, :break, :start_time, :end_time)'
        );
        $bindParams = [
            ':manager_id' => $shiftData['manager_id'],
            ':employee_id' => $shiftData['employee_id'],
            ':break' => $shiftData['break'],
            ':start_time' => $shiftData['start_time'],
            ':end_time' => $shiftData['end_time'],
        ];

        if (!$statement->execute($bindParams)) {
            throw new \InvalidArgumentException('Could not save submitted Shift');
        }

        return $this->fetchShiftById($this->data->lastInsertId());
    }

    /**
     * @param Shift $shift
     * @return void
     * @throws \InvalidArgumentException
     */
    public function assignShift(Shift $shift)
    {
        $statement = $this->data->prepare('UPDATE shifts SET employee_id = :employee_id WHERE id = :id');
        if (!$statement->execute([':employee_id' => $shift->getEmployee()->getId(), ':id' => $shift->getId()])) {
            throw new \InvalidArgumentException('Could not save submitted Shift');
        }
    }

    /**
     * @param Shift $shift
     * @return void
     * @throws \InvalidArgumentException
     */
    public function rescheduleShift(Shift $shift)
    {
        $statement = $this->data->prepare(
            'UPDATE shifts
              SET start_time = :start_time,
              end_time = :end_time
              WHERE id = :id'
        );
        $bindParams = [
            ':start_time' => $shift->date('start_time')->format('Y-m-d\TH:i:s'),
            ':end_time' => $shift->date('end_time')->format('Y-m-d\TH:i:s'),
            ':id' => $shift->getId()
        ];
        if (!$statement->execute($bindParams)) {
            throw new \InvalidArgumentException('Could not save submitted Shift');
        }
    }

    /**
     * Retrieve a list of Shifts during the passed (optional) date range
     *
     * @param \DateTimeInterface|null $startTime Optional beginning of search range
     * @param \DateTimeInterface|null $endTime Optional end of search range
     * @return Shift[]
     */
    public function fetchShifts(\DateTimeInterface $startTime = null, \DateTimeInterface $endTime = null)
    {
        $boundParams = [];
        $sql = 'SELECT * FROM shifts WHERE 1 = 1 ';
        if ($startTime) {
            $sql .= ' AND start_time >= :startTime ';
            $boundParams['startTime'] = $startTime->format('Y-m-d\TH:i:s');
        }
        if ($endTime) {
            $sql .= ' AND end_time <= :endTime ';
            $boundParams['endTime'] = $endTime->format('Y-m-d\TH:i:s');
        }
        $statement = $this->data->prepare($sql);
        $statement->execute($boundParams);

        return array_map(function ($row) {
            return new Shift($row);
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }
}

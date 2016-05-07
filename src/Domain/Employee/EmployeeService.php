<?php

namespace WIW\Domain\Employee;

use PDO;
use WIW\Domain\Manager\Manager;
use WIW\Domain\Shift;
use WIW\Domain\Role;

class EmployeeService
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
     * @return Employee
     */
    public function findByName($name)
    {
        $statement = $this->data->prepare('SELECT * FROM users WHERE name = :name AND role = :role LIMIT 1');
        $statement->execute([':name' => $name, ':role' => Role::EMPLOYEE]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return new Employee($row);
    }

    /**
     * Retrieve a list of Shifts for the passed Employee, during the passed (optional) date range
     *
     * @param Employee $employee
     * @param \DateTimeInterface|null $startTime Optional beginning of search range
     * @param \DateTimeInterface|null $endTime Optional end of search range
     * @return Shift[]
     */
    public function fetchShifts(Employee $employee, \DateTimeInterface $startTime = null, \DateTimeInterface $endTime = null)
    {
        $boundParams = [':id' => $employee->getId()];
        $sql = 'SELECT * FROM shifts WHERE employee_id = :id ';
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

    /**
     * Fetch employees that share a shift with the passed employee during the selected time range
     *
     * @param Employee $employee
     * @param \DateTimeInterface $startTime Beginning of search range
     * @param \DateTimeInterface $endTime End of search range
     *
     * @return Employee[]
     */
    public function fetchCoworkers(Employee $employee, \DateTimeInterface $startTime, \DateTimeInterface $endTime)
    {
        $sql = 'SELECT u.* -- , s.start_time, s.end_time
                FROM users u
                  INNER JOIN (
                    SELECT employee_id
                    FROM shifts
                    WHERE start_time >= :startTime
                      AND end_time <= :endTime
                  ) s
                  ON s.employee_id = u.id
                WHERE employee_id != :id';
        $boundParams = [
            ':id' => $employee->getId(),
            ':startTime' => $startTime->format('Y-m-d H:i:s'),
            ':endTime' => $endTime->format('Y-m-d H:i:s'),
        ];
        $statement = $this->data->prepare($sql);
        $statement->execute($boundParams);

        return array_map(function ($row) {
            return new Employee($row);
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchManagerContacts(Employee $employee, \DateTimeInterface $startTime, \DateTimeInterface $endTime)
    {
        $sql = 'SELECT u.*, s.start_time, s.end_time
                FROM users u
                  INNER JOIN (
                    SELECT manager_id, start_time, end_time
                    FROM shifts
                    WHERE start_time >= :startTime
                      AND end_time <= :endTime
                      AND employee_id = :id
                  ) s
                  ON s.manager_id = u.id';
        $boundParams = [
            ':id' => $employee->getId(),
            ':startTime' => $startTime->format('Y-m-d H:i:s'),
            ':endTime' => $endTime->format('Y-m-d H:i:s'),
        ];
        $statement = $this->data->prepare($sql);
        $statement->execute($boundParams);

        return array_map(function ($row) {
            return new Manager($row);
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }
}

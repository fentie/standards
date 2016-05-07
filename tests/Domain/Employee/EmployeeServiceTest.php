<?php

namespace Domain\Employee;

use Carbon\Carbon;
use WIW\Domain\Employee\Employee;
use WIW\Domain\Manager\Manager;
use WIW\Domain\Shift;
use WIW\Domain\Employee\EmployeeService;

class EmployeeServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var EmployeeService */
    private $object;
    /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject */
    private $mockPdo;
    /** @var \PDOStatement|\PHPUnit_Framework_MockObject_MockObject */
    private $mockPdoStatement;

    public function setUp()
    {
        $this->mockPdo = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockPdoStatement = $this->getMockBuilder(\PDOStatement::class)->getMock();
        $this->object = new EmployeeService($this->mockPdo);
    }

    public function testFindByNameQueriesDataStore()
    {
        $this->givenWeCanPrepareStatements();
        $this->mockPdoStatement->method('fetch')->willReturn([]);

        $this->object->findByName('chuck');
    }

    public function testFindByNameReturnsEmployee()
    {
        $this->givenWeCanPrepareStatements();
        $this->mockPdoStatement->method('fetch')->willReturn([]);

        self::assertInstanceOf(Employee::class, $this->object->findByName('chuck'));
    }

    public function testFetchShiftsReturnsListOfShifts()
    {
        $this->givenWeCanPrepareStatements();
        $this->givenCanFetchUserDataFromPreparedStatement();
        self::assertContainsOnlyInstancesOf(Shift::class, $this->object->fetchShifts(new Employee()));
    }

    public function testFetchCoworkersReturnsListOfEmployees()
    {
        $irrelevantDateTime = new \DateTimeImmutable();
        $this->givenWeCanPrepareStatements();
        $this->givenCanFetchUserDataFromPreparedStatement();
        self::assertContainsOnlyInstancesOf(
            Employee::class,
            $this->object->fetchCoworkers(new Employee(), $irrelevantDateTime, $irrelevantDateTime)
        );
    }

    public function testFetchManagerContactsReturnsListOfManagers()
    {
        $irrelevantDateTime = new \DateTimeImmutable();
        $this->givenWeCanPrepareStatements();
        $this->givenCanFetchUserDataFromPreparedStatement();
        self::assertContainsOnlyInstancesOf(
            Manager::class,
            $this->object->fetchManagerContacts(new Employee(), $irrelevantDateTime, $irrelevantDateTime)
        );
    }

    private function givenWeCanPrepareStatements()
    {
        $this->mockPdo->expects(self::atLeastOnce())
            ->method('prepare')
            ->willReturn($this->mockPdoStatement);
    }

    private function givenCanFetchUserDataFromPreparedStatement()
    {
        $this->mockPdoStatement->method('fetchAll')->willReturn([
            [
                'employee_id' => 7,
                'manager_id' => 2,
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addHour()
            ],
            [
                'employee_id' => 7,
                'manager_id' => 2,
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addHour()
            ],
        ]);
    }
}

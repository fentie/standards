<?php

namespace Domain\Shift;

use Carbon\Carbon;
use WIW\Domain\Employee\Employee;
use WIW\Domain\Manager\Manager;
use WIW\Domain\Shift;

class ShiftTest extends \PHPUnit_Framework_TestCase
{
    public function testShiftRejectsZeroLengthShifts()
    {
        $now = new Carbon();
        $this->expectException(\DomainException::class);
        new Shift(['start_time' => $now, 'end_time' => $now]);
    }

    public function testShiftRejectsNegativeLengthShifts()
    {
        $now = new Carbon();
        $this->expectException(\DomainException::class);
        new Shift(['start_time' => $now, 'end_time' => $now->subSeconds(5)]);
    }

    public function testReportsShiftDurationInHours()
    {
        $now = new Carbon();
        // TODO here we see a business decision that needs to be made...
        $shift = new Shift(['start_time' => $now, 'end_time' => $now->copy()->addHours(4)->addMinutes(35)]);
        self::assertSame(4, $shift->getDuration());
    }

    public function testToArrayReturnsDatesAsRFC822()
    {
        $now = new Carbon();
        $shift = $this->buildValidShift($now);

        $expected = [
            'start_time' => $now->format(\DateTime::RFC822),
            'end_time' => $now->copy()->addHours(9)->format(\DateTime::RFC822),
            'created_at' => $now->copy()->subWeek()->format(\DateTime::RFC822),
            'updated_at' => $now->copy()->subDays(5)->format(\DateTime::RFC822),
        ];
        self::assertArraySubset($expected, $shift->toArray());
    }

    public function testToArrayRemovesObjectKeys()
    {
        $now = new Carbon();
        $shift = $this->buildValidShift($now);

        $result = $shift->toArray();
        self::assertArrayNotHasKey('manager', $result);
        self::assertArrayNotHasKey('employee', $result);
    }

    private function buildValidShift(Carbon $baseTime)
    {
        $input = [
            'id' => 7,
            'manager' => new Manager(),
            'manager_id' => 2,
            'employee' => new Employee(),
            'employee_id' => 1,
            'break' => 1.0,
            'start_time' => $baseTime->format(\DateTime::ATOM),
            'end_time' => $baseTime->copy()->addHours(9)->format(\DateTime::ATOM),
            'created_at' => $baseTime->copy()->subWeek()->format(\DateTime::ATOM),
            'updated_at' => $baseTime->copy()->subDays(5)->format(\DateTime::ATOM),
        ];
        return new Shift($input);
    }
}

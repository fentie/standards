<?php

namespace WIW\Action\Employee;

use Equip\Adr\Status;
use Equip\Exception\HttpException;
use WIW\Action\EmployeeAction;
use WIW\Domain\Shift;

class ViewWeeklySummary extends EmployeeAction
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $this->fetchEmployeeByRoute($input);
        list($startTime, $endTime) = $this->parseStartEndDatesFromUri($input);

        $shifts = $this->service->fetchShifts($this->employee, $startTime, $endTime);
        $hoursWorked = array_sum(array_map(function (Shift $shift) {
            return $shift->getDuration();
        }, $shifts));

        return $this->payload
            ->withStatus(Status::STATUS_OK)
            ->withOutput(['hours_worked' => $hoursWorked]);
    }
}


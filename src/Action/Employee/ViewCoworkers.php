<?php

namespace WIW\Action\Employee;

use Equip\Adr\Status;
use Equip\Exception\HttpException;
use WIW\Action\EmployeeAction;

class ViewCoworkers extends EmployeeAction
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $this->fetchEmployeeByRoute($input);
        list($startTime, $endTime) = $this->parseStartEndDatesFromUri($input);

        $coworkers = $this->service->fetchCoworkers($this->employee, $startTime, $endTime);

        return $this->payload
            ->withStatus(Status::STATUS_OK)
            ->withOutput($coworkers);
    }
}


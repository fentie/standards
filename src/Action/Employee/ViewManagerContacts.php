<?php

namespace WIW\Action\Employee;

use Equip\Adr\Status;
use WIW\Action\EmployeeAction;

class ViewManagerContacts extends EmployeeAction
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $this->fetchEmployeeByRoute($input);
        list($startTime, $endTime) = $this->parseStartEndDatesFromUri($input);

        $managers = $this->service->fetchManagerContacts($this->employee, $startTime, $endTime);

        return $this->payload
            ->withStatus(Status::STATUS_OK)
            ->withOutput($managers);
    }
}


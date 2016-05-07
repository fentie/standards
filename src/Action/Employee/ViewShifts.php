<?php

namespace WIW\Action\Employee;

use Equip\Adr\Status;
use Equip\Exception\HttpException;
use WIW\Action\EmployeeAction;

class ViewShifts extends EmployeeAction
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $this->fetchEmployeeByRoute($input);
        try {
            $startTime = !empty($input['start_time']) ? new \DateTimeImmutable($input['start_time']) : null;
            $endTime = !empty($input['end_time']) ? new \DateTimeImmutable($input['end_time']) : null;
        } catch (\Exception $e) {
            throw HttpException::badRequest('Start or end time contain invalid or malformed data');
        }

        $shifts = $this->service->fetchShifts($this->employee, $startTime, $endTime);

        return $this->payload
            ->withStatus(Status::STATUS_OK)
            ->withOutput($shifts);
    }
}


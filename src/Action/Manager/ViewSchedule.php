<?php

namespace WIW\Action\Manager;

use Equip\Adr\Status;
use Equip\Exception\HttpException;
use WIW\Action\ManagerAction;

class ViewSchedule extends ManagerAction
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $this->fetchManagerByRoute($input);
        try {
            $startTime = !empty($input['start_time']) ? new \DateTimeImmutable($input['start_time']) : null;
            $endTime = !empty($input['end_time']) ? new \DateTimeImmutable($input['end_time']) : null;
        } catch (\Exception $e) {
            throw HttpException::badRequest('Start or end time contain invalid or malformed data');
        }

        $shifts = $this->service->fetchShifts($startTime, $endTime);

        return $this->payload
            ->withStatus(Status::STATUS_OK)
            ->withOutput($shifts);
    }
}


<?php

namespace WIW\Action\Manager;

use Equip\Adr\Status;
use Equip\Exception\HttpException;
use WIW\Action\ManagerAction;
use WIW\Domain\Shift;

class RescheduleShift extends ManagerAction
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $this->fetchManagerByRoute($input);
        list($startTime, $endTime) = $this->parseStartEndDatesFromUri($input);

        $shift = $this->fetchShiftByRoute($input);

        $shift->beginsAt($startTime);
        $shift->endsAt($endTime);

        try {
            $this->service->rescheduleShift($shift);
        } catch (\InvalidArgumentException $e) {
            // @todo normally I'd use a 422 here
            throw HttpException::badRequest('Submitted shift data was invalid');
        }

        return $this->payload
            ->withStatus(Status::STATUS_CREATED)
            ->withOutput($shift->toArray());
    }

    /**
     * @param array $input
     * @return Shift
     */
    private function fetchShiftByRoute($input)
    {
        if (empty($input['shiftId'])) {
            throw HttpException::notFound($this->request->getUri());
        }
        $shift = $this->service->fetchShiftById($input['shiftId']);

        if (!$shift->getId()) {
            throw HttpException::notFound($this->request->getUri());
        }

        return $shift;
    }
}


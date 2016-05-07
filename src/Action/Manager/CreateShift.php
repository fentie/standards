<?php

namespace WIW\Action\Manager;

use Equip\Adr\Status;
use Equip\Exception\HttpException;
use WIW\Action\ManagerAction;
use WIW\Domain\Shift;

class CreateShift extends ManagerAction
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $this->fetchManagerByRoute($input);
        unset($input['name']);

        if (empty($input['manager_id'])) {
            $input['manager_id'] = $this->manager->getId();
        }

        try {
            $shift = $this->service->createShift($input);
        } catch (\InvalidArgumentException $e) {
            // @todo normally I'd use a 422 here
            throw HttpException::badRequest('Submitted shift data was invalid');
        }

        return $this->payload
            ->withStatus(Status::STATUS_CREATED)
            ->withOutput($shift->toArray());
    }
}


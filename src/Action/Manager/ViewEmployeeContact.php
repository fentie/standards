<?php

namespace WIW\Action\Manager;

use Equip\Adr\Status;
use Equip\Exception\HttpException;
use WIW\Action\ManagerAction;

class ViewEmployeeContact extends ManagerAction
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $input)
    {
        $this->fetchManagerByRoute($input);
        if (empty($input['employeeName'])) {
            throw HttpException::notFound($this->request->getUri());
        }

        $contact = $this->service->fetchEmployeeByName($input['employeeName']);

        return $this->payload
            ->withStatus(Status::STATUS_OK)
            ->withOutput($contact->toArray());
    }
}


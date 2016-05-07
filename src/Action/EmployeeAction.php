<?php

namespace WIW\Action;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use Equip\Exception\HttpException;
use Psr\Http\Message\RequestInterface;
use WIW\Domain\Employee\Employee;
use WIW\Domain\Employee\EmployeeService;

abstract class EmployeeAction implements DomainInterface
{
    /**
     * @var PayloadInterface
     */
    protected $payload;
    /**
     * @var EmployeeService
     */
    protected $service;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var Employee
     */
    protected $employee;

    /**
     * @param PayloadInterface $payload
     * @param RequestInterface $request
     * @param EmployeeService $service
     */
    public function __construct(PayloadInterface $payload, RequestInterface $request, EmployeeService $service)
    {
        $this->payload = $payload;
        $this->request = $request;
        $this->service = $service;
    }

    /**
     * @inheritDoc
     */
    abstract public function __invoke(array $input);

    /**
     * Look up the employee performing this action using route params, or 404 if not found
     *
     * @todo You could make a serious argument for a middleware here
     * @param array $input
     */
    protected function fetchEmployeeByRoute(array $input)
    {
        if (empty($input['name'])) {
            throw HttpException::notFound($this->request->getUri());
        }

        $this->employee = $this->service->findByName($input['name']);
        if (!$this->employee->getId()) {
            throw HttpException::notFound($this->request->getUri());
        }
    }

    protected function parseStartEndDatesFromUri(array $input)
    {
        try {
            $startTime = new \DateTimeImmutable($input['start_time']);
            $endTime = new \DateTimeImmutable($input['end_time']);
        } catch (\Exception $e) {
            throw HttpException::badRequest('Start or end time contain invalid or malformed data');
        }
        return [$startTime, $endTime];
    }
}

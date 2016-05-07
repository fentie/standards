<?php

namespace WIW\Action;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use Equip\Exception\HttpException;
use Psr\Http\Message\RequestInterface;
use WIW\Domain\Manager\Manager;
use WIW\Domain\Manager\ManagerService;

abstract class ManagerAction implements DomainInterface
{
    /**
     * @var PayloadInterface
     */
    protected $payload;
    /**
     * @var ManagerService
     */
    protected $service;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param PayloadInterface $payload
     * @param RequestInterface $request
     * @param ManagerService $service
     */
    public function __construct(PayloadInterface $payload, RequestInterface $request, ManagerService $service)
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
     * Look up the manager performing this action using route params, or 404 if not found
     *
     * @todo You could make a serious argument for a middleware here
     * @param array $input
     */
    protected function fetchManagerByRoute(array $input)
    {
        if (empty($input['name'])) {
            throw HttpException::notFound($this->request->getUri());
        }

        $this->manager = $this->service->findByName($input['name']);
        if (!$this->manager->getId()) {
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

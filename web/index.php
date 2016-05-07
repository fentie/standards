<?php
require __DIR__ . '/../vendor/autoload.php';

Equip\Application::build()
    ->setConfiguration([
        Equip\Configuration\AurynConfiguration::class,
        Equip\Configuration\DiactorosConfiguration::class,
        Equip\Configuration\PayloadConfiguration::class,
        Equip\Configuration\RelayConfiguration::class,
        Equip\Configuration\WhoopsConfiguration::class,
        WIW\Configuration\WIWConfiguration::class,
    ])
    ->setMiddleware([
        Relay\Middleware\ResponseSender::class,
        Equip\Handler\ExceptionHandler::class,
        Equip\Handler\DispatchHandler::class,
        Relay\Middleware\JsonContentHandler::class,
        Relay\Middleware\FormContentHandler::class,
        Equip\Handler\ActionHandler::class,
        // @todo authentication & authorization wrappers
    ])
    ->setRouting(function (Equip\Directory $directory) {
        return $directory
            // @todo name is a very poor choice of key, should probably be fed in from auth
            ->get('/employees/{name}/shifts', WIW\Action\Employee\ViewShifts::class)
            ->get('/employees/{name}/coworkers', WIW\Action\Employee\ViewCoworkers::class)
            ->get('/employees/{name}/managers', WIW\Action\Employee\ViewManagerContacts::class)
            ->get('/employees/{name}/summary', WIW\Action\Employee\ViewWeeklySummary::class)
            ->get('/managers/{name}/shifts', WIW\Action\Manager\ViewSchedule::class)
            ->get('/managers/{name}/employees/{employeeName}', WIW\Action\Manager\ViewEmployeeContact::class)
            ->post('/managers/{name}/shifts', WIW\Action\Manager\CreateShift::class)
            // @todo I'll leave the PATCH/PUT discussion for later
            // curl 'http://localhost:8000/managers/Jane/shifts/2/assign' -X PUT -d '{"employee_id":3}' -H 'Content-Type: application/json' -H 'Accept: application/json'
            ->put('/managers/{name}/shifts/{shiftId}/assign', WIW\Action\Manager\AssignShift::class)
            ->put('/managers/{name}/shifts/{shiftId}/reschedule', WIW\Action\Manager\RescheduleShift::class)
            ; // End of routing
    })
    ->run();

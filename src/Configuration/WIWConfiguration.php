<?php

namespace WIW\Configuration;

use Auryn\Injector;
use Equip\Configuration\ConfigurationInterface;

class WIWConfiguration implements ConfigurationInterface
{
    public function apply(Injector $injector)
    {
        // @todo normally I'd load this from .env
        // @todo persisting data only across a single request is a downside of this method
        $injector->define('PDO', [':dsn' => 'sqlite::memory:']);
        $injector->share('PDO');

        $this->configureStubDb($injector->make('PDO'));
    }

    /**
     * Ridiculously non-standard
     *
     * @param \PDO $db
     */
    private function configureStubDb(\PDO $db)
    {
        if ($db->exec(file_get_contents(__DIR__ . '/../../db/schema.sql')) === false) {
            print_r($db->errorInfo());
            die();
        }
        if ($db->exec(file_get_contents(__DIR__ . '/../../db/fixtures.sql')) === false) {
            print_r($db->errorInfo());
            die();
        }
    }
}

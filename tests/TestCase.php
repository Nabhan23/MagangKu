<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Paksa environment 'testing' dan isolasi database test secara andal.
     *
     * Beberapa mesin (mis. Windows dengan Git Bash) mengekspos variabel
     * lingkungan (APP_ENV, DB_DATABASE, SESSION_PATH) yang mengganggu
     * override dari phpunit.xml dan bisa menyebabkan test menghapus
     * database pengembangan asli. Di sini kita paksa langsung via config.
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        $app->detectEnvironment(fn () => 'testing');
        $app->make('config')->set('database.default', 'sqlite');
        $app->make('config')->set('database.connections.sqlite.database', ':memory:');

        // Env var mesin Windows (SESSION_DRIVER, CACHE_STORE, MAIL_MAILER, QUEUE_CONNECTION)
        // bisa menimpa override phpunit.xml via $_SERVER. Paksa langsung via config agar
        // test selalu berjalan di driver in-memory/array dan tidak menyentuh MySQL dev.
        $app->make('config')->set('session.driver', 'array');
        $app->make('config')->set('cache.default', 'array');
        $app->make('config')->set('mail.default', 'array');
        $app->make('config')->set('queue.default', 'sync');

        return $app;
    }
}

<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\CreateApplication;

abstract class BaseTest extends TestCase
{
    use CreateApplication;

    protected $app;

    public function setUp() {
        parent::setUp();

        $this->app = $this->getAppInstance();
    }
}
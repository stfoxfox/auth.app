<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\Console\Tester\CommandTester;

class mainTest extends WebTestCase
{

    public function test()
    {
        $this->assertEquals(42,42);

    }
}
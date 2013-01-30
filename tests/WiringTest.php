<?php
namespace Aura\Marshal;

use Aura\Framework\Test\WiringAssertionsTrait;

class WiringTest extends \PHPUnit_Framework_TestCase
{
    use WiringAssertionsTrait;

    protected function setUp()
    {
        $this->loadDi();
    }

    public function testServices()
    {
        $this->assertGet('marshal_manager', 'Aura\Marshal\Manager');
    }

    public function testInstances()
    {
        $this->assertNewInstance('Aura\Marshal\Manager');
    }
}

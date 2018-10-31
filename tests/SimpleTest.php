<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class SimpleTest
 */
class SimpleTest extends TestCase
{
    public function testAddition()
    {
        $value = true;

        $array = [
            'key' => 'value'
        ];

        $this->assertEquals(4, (2 + 2), '4 was expected to equal 2 + 2');
        $this->assertTrue($value);
        $this->assertArrayHasKey('key', $array);
        $this->assertEquals('value', $array['key']);
        $this->assertCount(1, $array);
    }
}
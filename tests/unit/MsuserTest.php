<?php

namespace Tests\Unit;

use App\Models\Msuser;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class MsuserTest extends CIUnitTestCase
{
    public function testMsuserHasEditMethod(): void
    {
        $this->assertTrue(method_exists(Msuser::class, 'edit'), 'Msuser must have edit method');
    }
}

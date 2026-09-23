<?php

namespace Tests\Unit;

use App\Services\Auth\ProfileService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProfileServiceTest extends CIUnitTestCase
{
    public function testProfileServiceHasUpdateProfileMethod(): void
    {
        $this->assertTrue(
            method_exists(ProfileService::class, 'updateProfile'),
            'ProfileService must have updateProfile method'
        );
    }
}

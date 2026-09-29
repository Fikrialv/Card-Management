<?php

namespace Tests\Feature;

use Tests\TestCase;

final class CorsTest extends TestCase
{
    public function test_same_origin_application_does_not_require_customer_portal_cors(): void
    {
        self::assertSame([], config('cors.allowed_origins'));
        self::assertSame([], config('cors.paths'));
    }
}

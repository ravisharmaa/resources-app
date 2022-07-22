<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewResourcesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function guest_can_view_resources()
    {
        $this->get('/')->assertViewIs('welcome');
    }
}

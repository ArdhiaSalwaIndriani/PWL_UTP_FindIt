<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_root_redirects_to_the_login_page(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/masuk');
    }

    public function test_the_login_page_uses_the_shared_layout(): void
    {
        $this->withoutVite()
            ->get('/masuk')
            ->assertOk()
            ->assertSee('FindIt Campus')
            ->assertSee('Kata sandi');
    }
}

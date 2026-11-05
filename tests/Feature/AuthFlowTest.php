<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    public function test_halaman_login_mengarah_ke_modal_portal(): void
    {
        $this->get('/login')
            ->assertRedirect(route('portal.index') . '#login');
    }

    public function test_endpoint_token_csrf_mengembalikan_token(): void
    {
        $this->getJson('/csrf-token')
            ->assertOk()
            ->assertJsonStructure(['token']);
    }
}

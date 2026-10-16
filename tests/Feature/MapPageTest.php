<?php

namespace Tests\Feature;

use Tests\TestCase;

class MapPageTest extends TestCase
{
    /**
     * Test /map renders map.index view successfully.
     */
    public function test_map_route_renders_successfully(): void
    {
        $response = $this->get('/map');

        $response->assertStatus(200);
        $response->assertSee('Interactive Map — HeritageFinder');
        $response->assertSee('heritage-map-container');
        $response->assertSee('heritageMap()');
    }

    /**
     * Test /parallax-demo still renders the preserved demo page.
     */
    public function test_parallax_demo_route_is_preserved(): void
    {
        $response = $this->get('/parallax-demo');

        $response->assertStatus(200);
        $response->assertSee('Parallax GSAP - Laravel Blade');
    }

    /**
     * Test /api/map/heritages endpoint route exists and returns json response.
     */
    public function test_api_map_heritages_route_validation(): void
    {
        $response = $this->getJson('/api/map/heritages?category=invalid_category');
        $response->assertStatus(422);

        $responseValid = $this->getJson('/api/map/heritages?category=Cultural&danger=true&country=Indonesia');
        // Will either return 200 (if Fuseki is running) or 503 (guarded Fuseki fallback)
        $this->assertContains($responseValid->status(), [200, 503]);
    }
}

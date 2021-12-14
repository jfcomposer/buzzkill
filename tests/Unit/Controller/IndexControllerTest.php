<?php

namespace Tests\Controllers;

use Illuminate\Http\Response;
use PHPUnit\Framework\Test;
use Tests\TestCase;

class IndexControllerTest extends TestCase {

    public function testIndexReturnsValidCode() {
        $this->json('get', '/')
            ->assertStatus(Response::HTTP_OK);
    }
}



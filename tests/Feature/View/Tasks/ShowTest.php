<?php

namespace Tests\Feature\View\Tasks;

use Tests\TestCase;

class ShowTest extends TestCase
{
    /**
     * A basic view test example.
     */
    public function test_it_can_render(): void
    {
        $contents = $this->view('tasks.show', [
            //
        ]);

        $contents->assertSee('');
    }
}

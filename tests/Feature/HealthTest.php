<?php

it('returns health status with database connectivity', function () {
    $this->get(route('health'))
        ->assertOk()
        ->assertJson([
            'status' => 'ok',
            'database' => 'ok',
        ]);
});

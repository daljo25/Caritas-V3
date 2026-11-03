<?php

it('redirects guests to the panel login page', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

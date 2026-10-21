<?php

test('la raíz web redirige al frontend React', function () {
    $this->get('/')->assertRedirect(rtrim(config('app.frontend_url'), '/').'/');
});

test('la API responde', function () {
    $this->get('/up')->assertOk();
});

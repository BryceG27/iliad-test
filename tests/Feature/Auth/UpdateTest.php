<?php

test('change personal data', function () {
    login()->patch('/api/user', [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com'
    ])->assertStatus(200);
});

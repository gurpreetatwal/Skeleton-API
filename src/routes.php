<?php

$app->group('/user', function () {
    $this->get('', '\SkeletonAPI\Controllers\User:all');
    $this->post('', '\SkeletonAPI\Controllers\User:create');
    $this->get('/:uid', '\SkeletonAPI\Controllers\User:find');
    $this->put('/:uid', '\SkeletonAPI\Controllers\User:update');
    $this->delete('/:uid', '\SkeletonAPI\Controllers\User:delete');
});
$app->post('/login', '\SkeletonAPI\Controllers\Auth:login');
$app->post('/recover', '\SkeletonAPI\Controllers\Auth:recover');
$app->post('/reset-password', '\SkeletonAPI\Controllers\Auth:reset');

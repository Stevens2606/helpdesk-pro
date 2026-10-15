<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Semua tes di folder Feature memakai Tests\TestCase dan RefreshDatabase,
| sehingga setiap tes mulai dengan database bersih.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');
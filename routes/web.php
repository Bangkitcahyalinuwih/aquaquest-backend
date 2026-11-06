<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['aplikasi' => 'AquaQuest API', 'versi' => 'v1']));

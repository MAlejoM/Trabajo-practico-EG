<?php
require_once __DIR__ . '/../src/autoload.php';

use App\Modules\Usuarios\AuthService;

AuthService::logout();

header('Location: ../index.php');
exit();

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    /**
     * Lets any controller call $this->authorize(), so record-level checks can go
     * through the policies in app/Policies rather than an inline permission test.
     */
    use AuthorizesRequests;
}

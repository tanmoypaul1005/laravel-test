<?php

// app/Interfaces/UserInterface.php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface UserInterface
{
    public function addUsers(Request $request);
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Design;

interface IDesign
{
    public function applyTags($id, array $data);
    public function allLive();
}

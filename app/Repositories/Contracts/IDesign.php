<?php

namespace App\Repositories\Contracts;

use App\Models\Design;

interface IDesign
{
    public function applyTags($id, array $data);
    public function like($id);
    public function isLikedByUser($id);
    public function addComment($designId, array $data);
}

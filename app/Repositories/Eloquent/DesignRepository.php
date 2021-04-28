<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Eloquent\BaseRepository;

class DesignRepository extends BaseRepository implements IDesign
{
    // public function all()
    // {
    //     return Design::all();
    // }

    public function model()
    {
        return Design::class;
    }

    // public function allLive()
    // {
    //     return $this->model->where('is_live', true)->get();
    // }

    public function applyTags($id, array $data)
    {
        $design = $this->find($id);
        $design->retag($data);
    }

    public function addComment($designId, array $data)
    {
        // get design for comment
        $design = $this->find($designId);

        //create comment for design
        $comment = $design->comments()->create($data);

        return $comment;
    }

    public function like($id)
    {
        $design = $this->model->find($id);
        if ($design->isLikedByUser(auth()->id())) {
            $design->unlike();
        } else {
            $design->like();
        }

        return $design->likes()->count();
    }

    public function isLikedByUser($id)
    {
        $design = $this->model->find($id);
        return $design->isLikedByUser(auth()->id());
    }
}

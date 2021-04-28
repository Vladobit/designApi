<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    protected $design;

    public function __construct(IDesign $design)
    {
        $this->designs = $design;
    }


    public function index()
    {
        $design = $this->designs->withCriteria([
            new LatestFirst(),
            // new IsLive(),
            // new ForUser(2)
        ])->all();   //mozi ->paginate(1) da bidi
        return DesignResource::collection($design);
    }

    public function findDesign($id)
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }
    public function update(Request $request, $id)
    {

        $design = $this->designs->find($id);

        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required']
        ]);



        $design = $this->designs->update($id, [
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successfule ? false : $request->is_live
        ]);

        //apply Tags
        $this->designs->applyTags($id, $request->tags);

        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = $this->designs->find($id);

        $this->authorize('delete', $design);

        //delete files associated with record

        foreach (['thumbnail', 'large', 'original'] as $size) {
            //check if files exist
            if (Storage::disk($design->disk)->exists("uploads/designs/{$size}/" . $design->image)) {
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/" . $design->image);
            }
        }
        $this->designs->delete();
        return response()->json(['message' => 'Record deleted'], 200);
    }
}

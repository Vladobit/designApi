<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\IUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $user;
    public function __construct(IUser $user)
    {
        $this->users = $user;
    }
    public function index()
    {
        $users = $this->users->withCriteria([
            new EagerLoad(['desings']),
        ])->all();
        return UserResource::collection($users);
    }

    public function search(Request $request)
    {
        $designers = $this->users->search($request);
        return UserResource::collection($designers);
    }
}

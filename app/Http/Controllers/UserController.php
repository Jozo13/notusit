<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function updateUser(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|between:2,100',
            'roles' => 'required|array',
            'roles.*' => [Rule::enum(Roles::class)]
        ]);

        $user = User::find($request->id);

        if (!$user) {
            abort(404);
        }

        DB::beginTransaction();

        try {
            $user->name = $request->name;
            $user->save();

            $user->roles()->detach();

            $roles = Role::whereIn('name', $request->roles)->get();

            foreach ($roles as $role) {
                $user->roles()->attach($role->id);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return $this->returnData($user);
    }

    public function getUser(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $user = User::with('roles')->find($request->id);

        if (!$user) {
            abort(404);
        }

        return $this->returnData($user);
    }

    public function getUsers(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $users = User::with('roles')->paginate(5);

        return $this->returnData($users);
    }

    public function deleteUser(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $user = User::find($request->id);

        if (!$user) {
            abort(404);
        }

        DB::beginTransaction();

        try {
            $user->roles()->detach();
            $user->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return $this->returnData($user);
    }
}

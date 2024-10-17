<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '<>', 'admin')->orderByDesc("id")->paginate(10);


        return view('user.index', compact('users'));
    }

    public function update(Request $request, $id)
    {



        $user = User::findOrFail($id);
        $user->status = $request->has('status') ? 'active' : 'inactive';
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully!');
    }
}

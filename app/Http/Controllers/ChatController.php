<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    //
    public function dashboard(): Response
    {
        $friends = [];
        $initialMessages = [];
        return Inertia::render('Dashboard', ['friends' => $friends, 'initialMessages' => $initialMessages]);
    }

    public function searchUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string'
        ]);

        $username = trim(strtolower($request->username));

        $users = User::query()->where(function ($q) use ($username) {
            $q->where('first_name', 'LIKE', "%$username%")
                ->orWhere('last_name', 'LIKE', "%$username%")
                ->orWhere('email', 'LIKE', "%$username%");
        })
            ->get();

        return response()->json($users);
    }
}

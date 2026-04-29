<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(['admin', 'operator', 'viewer'])],
        ]);

        if (Auth::id() === $user->id && $request->role !== 'admin') {
            return redirect()
                ->route('users.index')
                ->with('error', 'Role akun admin yang sedang login tidak boleh diturunkan.');
        }

        $user->update([
            'role' => $request->role,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Role user berhasil diperbarui.');
    }
}
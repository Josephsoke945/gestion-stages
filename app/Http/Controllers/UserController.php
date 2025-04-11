<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'nom', 'email', 'role', 'created_at')->get(); // Remplacez 'name' par 'nom'

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255', // Remplacez 'name' par 'nom'
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:stagiaire,agent,admin',
        ]);

        User::create([
            'nom' => $validated['nom'], // Remplacez 'name' par 'nom'
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur ajouté avec succès.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255', // Remplacez 'name' par 'nom'
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string|in:stagiaire,agent,admin',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}

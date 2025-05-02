<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        \Log::info('Update Profile Request', [
            'has_file' => $request->hasFile('avatar'),
            'all_data' => $request->all(),
            'validated_data' => $data
        ]);

        if ($request->hasFile('avatar')) {
            \Log::info('Processing avatar file');
            
            // Suppression de l'ancien avatar s'il existe
            if ($user->avatar) {
                \Log::info('Deleting old avatar: ' . $user->avatar);
                Storage::disk('public')->delete($user->avatar);
            }

            // Upload du nouvel avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            \Log::info('New avatar path: ' . $path);
            $data['avatar'] = $path;
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        \Log::info('User updated', [
            'user_id' => $user->id,
            'avatar' => $user->avatar
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Profile updated successfully',
                'avatar' => $user->avatar,
                'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null
            ]);
        }

        return Redirect::route('profile.edit')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Suppression de l'avatar lors de la suppression du compte
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

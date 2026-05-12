<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Index', [
            'user' => $request->user()->only(['id', 'username', 'role', 'photo']),
            'tenant' => $request->user()->tenant,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($request->user()->id)],
        ]);
        $request->user()->update($validated);

        return back()->with('success', 'បានកែប្រែឈ្មោះអ្នកប្រើប្រាស់ដោយជោគជ័យ។');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return back()->with('success', 'បានផ្លាស់ប្តូរពាក្យសម្ងាត់ដោយជោគជ័យ។');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:5120'],
        ]);
        $user = $request->user();
        if (! empty($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->update([
            'photo' => $validated['photo']->store('images/user', 'public'),
        ]);

        return back()->with('success', 'you have successfully updated your profile photo.');
    }
}

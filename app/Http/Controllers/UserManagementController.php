<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Users/Index', [
            'users' => User::with('tenant')->orderByDesc('id')->get(),
            'currentUserId' => $request->user()?->id,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'role' => ['required', Rule::in(['User', 'Admin', 'SuperAdmin'])],
            'password' => ['required', 'confirmed', Password::defaults()],
            'photo' => ['nullable', 'image', 'max:5120'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'id_card' => ['nullable', 'string', 'max:100'],
            'address_origin' => ['nullable', 'string', 'max:255'],
        ]);
        $actor = $request->user();
        if ($actor->role !== 'SuperAdmin' && $validated['role'] !== 'User') {
            return back()->with('error', 'អ្នកមានសិទ្ធិបង្កើតបានតែគណនី User ប៉ុណ្ណោះ។');
        }
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('images/user', 'public');
        }
        $user = User::create([
            'username' => $validated['username'],
            'role' => $validated['role'],
            'password' => $validated['password'],
            'photo' => $validated['photo'] ?? null,
            'email' => null,
        ]);
        Tenant::create([
            'user_id' => $user->id,
            'full_name' => $validated['full_name'] ?? $validated['username'],
            'phone' => $validated['phone'] ?? null,
            'id_card' => $validated['id_card'] ?? null,
            'address_origin' => $validated['address_origin'] ?? null,
        ]);

        return back()->with('success', 'បានបន្ថែមអ្នកប្រើប្រាស់ដោយជោគជ័យ។');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        if ($user->role === 'SuperAdmin' && $actor->id !== $user->id) {
            return back()->with('error', 'អ្នកមិនមានសិទ្ធិកែប្រែគណនី SuperAdmin ផ្សេងទេ។');
        }
        if ($actor->role !== 'SuperAdmin' && $user->role === 'Admin' && $actor->id !== $user->id) {
            return back()->with('error', 'អ្នកមិនមានសិទ្ធិកែប្រែគណនី Admin ផ្សេងទេ។');
        }
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'role' => ['required', Rule::in(['User', 'Admin', 'SuperAdmin'])],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'photo' => ['nullable', 'image', 'max:5120'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'id_card' => ['nullable', 'string', 'max:100'],
            'address_origin' => ['nullable', 'string', 'max:255'],
        ]);
        if ($actor->role !== 'SuperAdmin' && $validated['role'] !== 'User' && $actor->id !== $user->id) {
            return back()->with('error', 'អ្នកគ្មានសិទ្ធិដំឡើងតួនាទីទៅជា Admin/SuperAdmin ទេ។');
        }
        if ($request->hasFile('photo')) {
            if (! empty($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('images/user', 'public');
        }
        $user->username = $validated['username'];
        $user->role = $validated['role'];
        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }
        $user->save();
        $tenant = Tenant::firstOrNew([
            'user_id' => $user->id,
        ]);
        $tenant->fill([
            'full_name' => $validated['full_name'] ?? $validated['username'],
            'phone' => $validated['phone'] ?? null,
            'id_card' => $validated['id_card'] ?? null,
            'address_origin' => $validated['address_origin'] ?? null,
        ]);
        $tenant->save();

        return back()->with('success', 'បានកែប្រែអ្នកប្រើប្រាស់ដោយជោគជ័យ។');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        if ($actor->id === $user->id) {
            return back()->with('error', 'មិនអាចលុបគណនីរបស់ខ្លួនឯងបានទេ។');
        }
        if ($user->role === 'SuperAdmin') {
            return back()->with('error', 'មិនអាចលុបគណនី SuperAdmin ទេ។');
        }
        if ($actor->role !== 'SuperAdmin' && $user->role === 'Admin') {
            return back()->with('error', 'អ្នកមិនមានសិទ្ធិលុបគណនី Admin ផ្សេងទេ។');
        }
        if (! empty($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        if ($user->tenant) {
            $user->tenant->update([
                'user_id' => null,
            ]);
        }
        $user->delete();

        return back()->with('success', 'បានលុបអ្នកប្រើប្រាស់ដោយជោគជ័យ។');
    }
}

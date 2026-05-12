<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AboutMeController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('AboutMe/Index', [
            'tenant' => $request->user()?->tenant,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'id_card' => ['required', 'string', 'max:100'],
            'address_origin' => ['required', 'string', 'max:255'],
        ]);
        $tenant = Tenant::firstOrNew([
            'user_id' => $request->user()->id,
        ]);
        $tenant->fill($validated);
        $tenant->save();

        return back()->with('success', 'បានកែប្រែព័ត៌មានលម្អិតដោយជោគជ័យ។');
    }
}

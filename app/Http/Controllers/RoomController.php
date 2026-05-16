<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index(Request $request): Response
    {
        $categoryId = $request->integer('category_id');
        $search = $request->input('search');

        $query = Room::query()->with('category')->latest('id');

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

        if ($request->filled('search')) {
            $query->where('room_number', 'like', '%' . $search . '%');
        }

        $tenant = $request->user()?->tenant;
        $hasRequiredInfo = (bool) ($tenant?->full_name && $tenant?->phone && $tenant?->id_card && $tenant?->address_origin);

        return Inertia::render('Rooms/Index', [
            'rooms' => $query->paginate(12)->withQueryString(),
            'categories' => Category::orderBy('cat_name')->get(),
            'filters' => [
                'category_id' => $categoryId ?: null,
                'search' => $search,
            ],
            'isAdmin' => $request->user()?->isAdmin() ?? false,
            'hasRequiredInfo' => $hasRequiredInfo,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:255', Rule::unique('rooms', 'room_number')],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'price' => ['required', 'numeric', 'gt:0'],
            'status' => ['required', Rule::in(['ទំនេរ', 'ជួលហើយ', 'ជួសជុល'])],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('images/rooms', 'public');
        }
        Room::create($validated);

        return back()->with('success', 'បានបន្ថែមបន្ទប់ដោយជោគជ័យ។');
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:255', Rule::unique('rooms', 'room_number')->ignore($room->id)],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'price' => ['required', 'numeric', 'gt:0'],
            'status' => ['required', Rule::in(['ទំនេរ', 'ជួលហើយ', 'ជួសជុល'])],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);
        if ($request->hasFile('photo')) {
            if (! empty($room->photo)) {
                Storage::disk('public')->delete($room->photo);
            }
            $validated['photo'] = $request->file('photo')->store('images/rooms', 'public');
        }
        $room->update($validated);

        return back()->with('success', 'បានកែប្រែបន្ទប់ដោយជោគជ័យ។');
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->bookings()->whereIn('status', ['រង់ចាំ', 'បានអនុម័ត', 'កំពុងជួល'])->exists()) {
            return back()->with('error', 'មិនអាចលុបបន្ទប់នេះបានទេ ព្រោះមានប្រតិបត្តិការកក់កំពុងដំណើរការ។');
        }
        if (! empty($room->photo)) {
            Storage::disk('public')->delete($room->photo);
        }
        $room->delete();

        return back()->with('success', 'បានលុបបន្ទប់ដោយជោគជ័យ។');
    }

    public function book(Request $request, Room $room): RedirectResponse
    {
        if ($request->user()?->isAdmin()) {
            return back()->with('error', 'អ្នកគ្រប់គ្រងមិនអាចកក់បន្ទប់បានទេ។');
        }
        $tenant = $request->user()?->tenant;
        $hasRequiredInfo = (bool) ($tenant?->full_name && $tenant?->phone && $tenant?->id_card && $tenant?->address_origin);
        if (! $hasRequiredInfo) {
            return back()->with('error', 'សូមបំពេញព័ត៌មានអំពីខ្ញុំឲ្យបានគ្រប់គ្រាន់មុនពេលកក់បន្ទប់។');
        }
        $validated = $request->validate([
            'move_in_date' => ['required', 'date'],
        ]);
        if ($room->status !== 'ទំនេរ') {
            return back()->with('error', 'បន្ទប់នេះមិនទំនេរទេ មិនអាចកក់បានឡើយ។');
        }
        Booking::create([
            'user_id' => $request->user()->id,
            'room_id' => $room->id,
            'move_in_date' => $validated['move_in_date'],
            'status' => 'រង់ចាំ',
        ]);

        return back()->with('success', 'សំណើកក់បន្ទប់របស់អ្នកត្រូវបានបញ្ជូនដោយជោគជ័យ។');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Bookings/Index', [
            'bookings' => Booking::query()
                ->with(['user.tenant', 'room'])
                ->whereIn('status', ['រង់ចាំ', 'បានអនុម័ត', 'កំពុងជួល', 'បដិសេធ'])
                ->latest('id')
                ->get(),
        ]);
    }

    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'move_in_date' => ['required', 'date'],
        ]);
        DB::transaction(function () use ($booking, $validated): void {
            $booking->refresh();
            $booking->load('room');
            if ($booking->status !== 'រង់ចាំ') {
                abort(422, 'សំណើនេះមិនអាចអនុម័តបានទេ។');
            }
            if (! $booking->room || $booking->room->status !== 'ទំនេរ') {
                abort(422, 'បន្ទប់នេះមិនទំនេរទេ មិនអាចអនុម័តការកក់បានឡើយ។');
            }
            $booking->room->update([
                'status' => 'ជួលហើយ',
            ]);
            $booking->update([
                'move_in_date' => $validated['move_in_date'],
                'status' => 'បានអនុម័ត',
            ]);
        });

        return back()->with('success', 'បានអនុម័តសំណើកក់បន្ទប់ដោយជោគជ័យ។');
    }

    public function reject(Booking $booking): RedirectResponse
    {
        if ($booking->status !== 'រង់ចាំ') {
            return back()->with('error', 'សំណើនេះមិនអាចបដិសេធបានទេ។');
        }
        $booking->update([
            'status' => 'បដិសេធ',
        ]);

        return back()->with('success', 'បានបដិសេធសំណើសុំកក់បន្ទប់។');
    }

    public function markMovedIn(Booking $booking): RedirectResponse
    {
        if ($booking->status !== 'បានអនុម័ត') {
            return back()->with('error', 'សំណើនេះមិនទាន់អាចផ្តល់សោបន្ទប់បានទេ។');
        }
        $booking->update([
            'start_date' => now()->toDateString(),
            'status' => 'កំពុងជួល',
        ]);

        return back()->with('success', 'ស្ថានភាពត្រូវបានប្តូរទៅជា កំពុងជួល។');
    }
}

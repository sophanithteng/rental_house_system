<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'totalRooms' => Room::count(),
                'availableRooms' => Room::where('status', 'ទំនេរ')->count(),
                'rentedRooms' => Room::where('status', 'ជួលហើយ')->count(),
                'pendingBookings' => Booking::where('status', 'រង់ចាំ')->count(),
            ],
            'isAdmin' => $request->user()?->isAdmin() ?? false,
        ]);
    }
}

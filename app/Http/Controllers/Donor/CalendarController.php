<?php

declare(strict_types=1);

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\CalendarService;
use App\Support\DateHelper;
use App\Support\IcsGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        protected CalendarService $calendarService
    ) {}

    public function index(Request $request): View
    {
        $month = (string) $request->query('month', DateHelper::today()->format('Y-m'));
        $filter = (string) $request->query('filter', 'all');

        $data = $this->calendarService->buildDonorCalendar($month, $request->user(), $filter);

        return view('pages.donor.calendar', $data);
    }

    /**
     * Download an iCalendar (.ics) invite for a booked appointment.
     */
    public function exportIcs(Request $request, Appointment $appointment): Response
    {
        // Ensure the appointment belongs to the authenticated user
        if ($appointment->user_id !== $request->user()->id) {
            abort(403);
        }

        $ics = IcsGenerator::generate($appointment);
        $filename = 'donorlink-appointment-'.$appointment->id.'.ics';

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}

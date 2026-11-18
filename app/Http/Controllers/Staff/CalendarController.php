<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\CalendarService;
use App\Support\DateHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        protected CalendarService $calendarService
    ) {}

    public function index(Request $request): View
    {
        $month = (string) $request->query('month', DateHelper::today()->format('Y-m'));
        $status = (string) $request->query('status', 'all');

        $data = $this->calendarService->buildStaffCalendar($month, $status);

        return view('pages.staff.calendar', $data);
    }
}

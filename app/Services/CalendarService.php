<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\DriveStatus;
use App\Models\Appointment;
use App\Models\Drive;
use App\Models\User;
use App\Support\DateHelper;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class CalendarService
{
    /**
     * Build the calendar data structure for UBTS Staff/Admin.
     *
     * @return array<string, mixed>
     */
    public function buildStaffCalendar(string $monthString, string $statusFilter = 'all'): array
    {
        $currentMonth = $this->resolveMonth($monthString);
        $gridStart = $currentMonth->startOfWeek(CarbonInterface::MONDAY);
        $gridEnd = $currentMonth->endOfMonth()->endOfWeek(CarbonInterface::SUNDAY);

        // Fetch drives overlapping the grid window
        $drivesQuery = Drive::query()
            ->where('starts_on', '<=', $gridEnd->toDateString())
            ->where('ends_on', '>=', $gridStart->toDateString())
            ->with(['slots' => function ($q) {
                $q->orderBy('starts_at');
            }]);

        if ($statusFilter !== 'all' && DriveStatus::tryFrom($statusFilter)) {
            $drivesQuery->where('status', $statusFilter);
        }

        $drives = $drivesQuery->orderBy('starts_on')->get();

        // Calculate drive slot metrics in memory
        $drivesWithMetrics = $drives->map(function (Drive $drive) {
            $slots = $drive->slots;
            $capacity = $slots->sum('capacity');
            $booked = $slots->sum('booked_count');
            $fillRate = $capacity > 0 ? round(($booked / $capacity) * 100, 1) : 0.0;

            $earliestSlot = $slots->first();
            $latestSlot = $slots->last();

            $timeRange = null;
            if ($earliestSlot && $latestSlot) {
                $timeRange = DateHelper::formatTime($earliestSlot->starts_at).' – '.DateHelper::formatTime($latestSlot->ends_at);
            }

            return [
                'id' => $drive->id,
                'title' => $drive->title,
                'venue' => $drive->venue,
                'address' => $drive->address,
                'status' => $drive->status,
                'starts_on' => $drive->starts_on,
                'ends_on' => $drive->ends_on,
                'capacity' => $capacity,
                'booked' => $booked,
                'fill_rate' => $fillRate,
                'time_range' => $timeRange,
                'slots_count' => $slots->count(),
                'is_live' => $drive->starts_on->toDateString() <= DateHelper::today()->toDateString()
                    && $drive->ends_on->toDateString() >= DateHelper::today()->toDateString()
                    && $drive->isPublished(),
            ];
        });

        // Compute monthly summary stats (for drives whose starts_on or ends_on falls inside current month)
        $monthStartStr = $currentMonth->startOfMonth()->toDateString();
        $monthEndStr = $currentMonth->endOfMonth()->toDateString();

        $monthDrives = $drivesWithMetrics->filter(function ($d) use ($monthStartStr, $monthEndStr) {
            return $d['starts_on']->toDateString() <= $monthEndStr && $d['ends_on']->toDateString() >= $monthStartStr;
        });

        $monthStats = [
            'total_drives' => $monthDrives->count(),
            'published_drives' => $monthDrives->where('status', DriveStatus::Published)->count(),
            'draft_drives' => $monthDrives->where('status', DriveStatus::Draft)->count(),
            'closed_drives' => $monthDrives->where('status', DriveStatus::Closed)->count(),
            'total_capacity' => $monthDrives->sum('capacity'),
            'total_booked' => $monthDrives->sum('booked'),
        ];

        // Build 7-day grid cells
        $days = $this->buildGridDays($gridStart, $gridEnd, $currentMonth, $drivesWithMetrics);

        return [
            'month' => $currentMonth->format('Y-m'),
            'monthLabel' => $currentMonth->format('F Y'),
            'prevMonth' => $currentMonth->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->addMonth()->format('Y-m'),
            'todayDate' => DateHelper::today()->toDateString(),
            'statusFilter' => $statusFilter,
            'monthStats' => $monthStats,
            'days' => $days,
            'agendaDrives' => $monthDrives->values(),
        ];
    }

    /**
     * Build the calendar data structure for a Voluntary Donor.
     *
     * @return array<string, mixed>
     */
    public function buildDonorCalendar(string $monthString, User $donor, string $filter = 'all'): array
    {
        $currentMonth = $this->resolveMonth($monthString);
        $gridStart = $currentMonth->startOfWeek(CarbonInterface::MONDAY);
        $gridEnd = $currentMonth->endOfMonth()->endOfWeek(CarbonInterface::SUNDAY);

        // Fetch published drives in window
        $drives = Drive::query()
            ->where('status', DriveStatus::Published)
            ->where('starts_on', '<=', $gridEnd->toDateString())
            ->where('ends_on', '>=', $gridStart->toDateString())
            ->with(['slots' => function ($q) {
                $q->orderBy('starts_at');
            }])
            ->orderBy('starts_on')
            ->get();

        // Fetch donor appointments in the calendar grid window
        $gridStartUtc = DateHelper::toUtc($gridStart);
        $gridEndUtc = DateHelper::toUtc($gridEnd->endOfDay());

        $appointments = Appointment::query()
            ->where('user_id', $donor->id)
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->whereHas('slot', function ($q) use ($gridStartUtc, $gridEndUtc) {
                $q->whereBetween('starts_at', [$gridStartUtc, $gridEndUtc]);
            })
            ->with(['slot.drive'])
            ->get();

        $drivesWithMetrics = $drives->map(function (Drive $drive) {
            $slots = $drive->slots;
            $capacity = $slots->sum('capacity');
            $booked = $slots->sum('booked_count');
            $spotsLeft = max(0, $capacity - $booked);

            $earliestSlot = $slots->first();
            $latestSlot = $slots->last();

            $timeRange = null;
            if ($earliestSlot && $latestSlot) {
                $timeRange = DateHelper::formatTime($earliestSlot->starts_at).' – '.DateHelper::formatTime($latestSlot->ends_at);
            }

            return [
                'id' => $drive->id,
                'title' => $drive->title,
                'venue' => $drive->venue,
                'address' => $drive->address,
                'starts_on' => $drive->starts_on,
                'ends_on' => $drive->ends_on,
                'capacity' => $capacity,
                'booked' => $booked,
                'spots_left' => $spotsLeft,
                'has_spots' => $spotsLeft > 0,
                'time_range' => $timeRange,
            ];
        });

        // Map appointments by local date string
        $appointmentsByDate = $appointments->groupBy(function (Appointment $appointment) {
            $slot = $appointment->slot;

            return $slot ? DateHelper::toEat($slot->starts_at)->toDateString() : '';
        });

        // Build grid days
        $days = $this->buildGridDays($gridStart, $gridEnd, $currentMonth, $drivesWithMetrics, $appointmentsByDate, $filter);

        $monthStartStr = $currentMonth->startOfMonth()->toDateString();
        $monthEndStr = $currentMonth->endOfMonth()->toDateString();

        $monthDrives = $drivesWithMetrics->filter(function ($d) use ($monthStartStr, $monthEndStr) {
            return $d['starts_on']->toDateString() <= $monthEndStr && $d['ends_on']->toDateString() >= $monthStartStr;
        });

        $donorMonthAppointments = $appointments->filter(function ($a) use ($monthStartStr, $monthEndStr) {
            $date = $a->slot ? DateHelper::toEat($a->slot->starts_at)->toDateString() : '';

            return $date >= $monthStartStr && $date <= $monthEndStr;
        });

        $monthStats = [
            'total_drives' => $monthDrives->count(),
            'drives_with_open_slots' => $monthDrives->where('has_spots', true)->count(),
            'my_appointments_count' => $donorMonthAppointments->count(),
        ];

        return [
            'month' => $currentMonth->format('Y-m'),
            'monthLabel' => $currentMonth->format('F Y'),
            'prevMonth' => $currentMonth->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->addMonth()->format('Y-m'),
            'todayDate' => DateHelper::today()->toDateString(),
            'filter' => $filter,
            'monthStats' => $monthStats,
            'days' => $days,
            'myAppointments' => $donorMonthAppointments,
            'agendaDrives' => $monthDrives->values(),
        ];
    }

    /**
     * Resolve and validate a year-month string in EAT.
     */
    private function resolveMonth(string $monthString): CarbonImmutable
    {
        try {
            $parsed = CarbonImmutable::createFromFormat('Y-m', trim($monthString), DateHelper::TIMEZONE);
            if ($parsed !== false) {
                return $parsed->startOfMonth();
            }
        } catch (\Throwable) {
            // fallback to current month
        }

        return DateHelper::today()->startOfMonth();
    }

    /**
     * Build day cells for the calendar grid.
     *
     * @param  Collection<int, array<string, mixed>>  $drives
     * @param  Collection<string, Collection<int, Appointment>>|null  $appointmentsByDate
     * @return array<int, array<string, mixed>>
     */
    private function buildGridDays(
        CarbonImmutable $gridStart,
        CarbonImmutable $gridEnd,
        CarbonImmutable $currentMonth,
        Collection $drives,
        ?Collection $appointmentsByDate = null,
        string $filter = 'all'
    ): array {
        $days = [];
        $current = $gridStart;
        $todayStr = DateHelper::today()->toDateString();

        while ($current <= $gridEnd) {
            $dateStr = $current->toDateString();

            // Find drives on this day
            $dayDrives = $drives->filter(function ($drive) use ($dateStr) {
                return $drive['starts_on']->toDateString() <= $dateStr && $drive['ends_on']->toDateString() >= $dateStr;
            })->values();

            // Find appointments on this day
            $dayAppointments = $appointmentsByDate ? ($appointmentsByDate->get($dateStr) ?? collect())->values() : collect();

            if ($filter === 'my_appointments') {
                // If filtering by my appointments only, keep drives empty if no appointment
                if ($dayAppointments->isEmpty()) {
                    $dayDrives = collect();
                }
            }

            $days[] = [
                'date' => $dateStr,
                'dayNumber' => $current->day,
                'dayName' => $current->format('D'),
                'isCurrentMonth' => $current->month === $currentMonth->month,
                'isToday' => $dateStr === $todayStr,
                'isPast' => $dateStr < $todayStr,
                'drives' => $dayDrives,
                'appointments' => $dayAppointments,
                'hasDrives' => $dayDrives->isNotEmpty(),
                'hasAppointments' => $dayAppointments->isNotEmpty(),
            ];

            $current = $current->addDay();
        }

        return $days;
    }
}

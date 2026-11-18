<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\DonationRecord;
use App\Models\Drive;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

echo '=== DRIVES ('.Drive::count().") ===\n";
foreach (Drive::all() as $d) {
    echo "ID: {$d->id} | {$d->title} | Status: {$d->status->value} | Dates: {$d->starts_on->toDateString()} to {$d->ends_on->toDateString()} | Slots: {$d->slots()->count()}\n";
}

echo "\n=== USERS (".User::count().") ===\n";
echo "Staff/Admins:\n";
foreach (User::whereIn('role', ['admin', 'staff'])->get() as $u) {
    echo "- {$u->role->value}: {$u->name} ({$u->email})\n";
}
echo 'Donors count: '.User::where('role', 'donor')->count()."\n";
echo 'Appointments: '.Appointment::count()."\n";
echo 'Donation records: '.DonationRecord::count()."\n";
echo 'Audit logs: '.AuditLog::count()."\n";
echo 'Notification logs: '.NotificationLog::count()."\n";

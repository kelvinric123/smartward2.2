<?php

namespace App\Observers;

use App\Models\ShiftSchedule;

class ShiftScheduleObserver
{
    /**
     * Handle the ShiftSchedule "created" event.
     */
    public function created(ShiftSchedule $shiftSchedule): void
    {
        //
    }

    /**
     * Handle the ShiftSchedule "updated" event.
     */
    public function updated(ShiftSchedule $shiftSchedule): void
    {
        //
    }

    /**
     * Handle the ShiftSchedule "deleted" event.
     */
    public function deleted(ShiftSchedule $shiftSchedule): void
    {
        //
    }

    /**
     * Handle the ShiftSchedule "restored" event.
     */
    public function restored(ShiftSchedule $shiftSchedule): void
    {
        //
    }

    /**
     * Handle the ShiftSchedule "force deleted" event.
     */
    public function forceDeleted(ShiftSchedule $shiftSchedule): void
    {
        //
    }
}

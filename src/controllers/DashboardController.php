<?php

class DashboardController extends BaseController {
    public function owner(): void {
        Auth::requireOwner();
        $properties = Property::getByOwner(Auth::getCurrentUserId());
        $bookings = Booking::getForOwner(Auth::getCurrentUserId());

        $this->render('dashboard/owner', [
            'properties' => $properties,
            'bookings' => $bookings,
        ]);
    }

    public function tenant(): void {
        Auth::requireTenant();
        $bookings = Booking::getForTenant(Auth::getCurrentUserId());
        $this->render('dashboard/tenant', ['bookings' => $bookings]);
    }
}

?>

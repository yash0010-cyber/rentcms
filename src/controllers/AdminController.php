<?php

class AdminController extends BaseController {
    public function dashboard(): void {
        Auth::requireAdmin();

        $stats = [
            'users' => count(User::all()),
            'properties' => count(Property::search()),
            'bookings' => count(Booking::getAll()),
            'members' => count(Member::all()),
        ];

        $this->render('admin/dashboard', ['stats' => $stats]);
    }

    public function users(): void {
        Auth::requireAdmin();
        $users = User::all();
        $this->render('admin/users', ['users' => $users]);
    }

    public function properties(): void {
        Auth::requireAdmin();
        $properties = Property::search();
        $this->render('admin/properties', ['properties' => $properties]);
    }

    public function bookings(): void {
        Auth::requireAdmin();
        $bookings = Booking::getAll();
        $this->render('admin/bookings', ['bookings' => $bookings]);
    }

    public function members(): void {
        Auth::requireAdmin();
        $members = Member::all();
        $this->render('admin/members', ['members' => $members]);
    }
}

?>

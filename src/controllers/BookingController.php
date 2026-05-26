<?php

class BookingController extends BaseController {
    public function index(): void {
        Auth::requireLogin();

        if (Auth::isAdmin()) {
            $bookings = Booking::getAll();
        } elseif (Auth::isOwner()) {
            $bookings = Booking::getForOwner(Auth::getCurrentUserId());
        } else {
            $bookings = Booking::getForTenant(Auth::getCurrentUserId());
        }

        $this->render('bookings/index', ['bookings' => $bookings]);
    }

    public function store(): void {
        Auth::requireTenant();
        $this->requireCsrfToken();

        $propertyId = (int) ($_POST['property_id'] ?? 0);
        $checkIn = $_POST['check_in_date'] ?? '';
        $checkOut = $_POST['check_out_date'] ?? '';

        if ($propertyId <= 0 || !Validator::date($checkIn) || !Validator::date($checkOut)) {
            $this->setFlash('danger', 'Invalid booking details.');
            $this->redirect('/properties/view?id=' . $propertyId);
        }

        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        if ($checkOutDate <= $checkInDate) {
            $this->setFlash('danger', 'Check-out date must be after check-in date.');
            $this->redirect('/properties/view?id=' . $propertyId);
        }

        $property = Property::getById($propertyId);
        if (!$property) {
            $this->setFlash('danger', 'Property not found.');
            $this->redirect('/properties');
        }

        $totalNights = (int) $checkInDate->diff($checkOutDate)->format('%a');
        $pricePerNight = ((float) $property['price_per_month']) / 30;
        $totalPrice = $pricePerNight * $totalNights;

        $bookingId = Booking::create([
            'property_id' => $propertyId,
            'tenant_id' => Auth::getCurrentUserId(),
            'owner_id' => (int) $property['owner_id'],
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_nights' => $totalNights,
            'price_per_night' => $pricePerNight,
            'total_price' => $totalPrice,
        ]);

        Activity::logBookingCreated(Auth::getCurrentUserId(), $bookingId, $propertyId);

        if (!empty($_SESSION['user_email'])) {
            Email::sendBookingConfirmation(
                $_SESSION['user_email'],
                $_SESSION['user_name'] ?? '',
                $property['title'],
                $checkIn,
                $checkOut,
                number_format($totalPrice, 2)
            );
        }

        $this->setFlash('success', 'Booking request submitted.');
        $this->redirect('/bookings');
    }
}

?>

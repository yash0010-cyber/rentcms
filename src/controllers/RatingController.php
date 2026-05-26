<?php

class RatingController extends BaseController {
    public function store(): void {
        Auth::requireTenant();
        $this->requireCsrfToken();

        $propertyId = (int) ($_POST['property_id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 0);
        $review = Validator::sanitizeString($_POST['review'] ?? '');

        if ($propertyId <= 0 || $rating < 1 || $rating > 5) {
            $this->setFlash('danger', 'Invalid rating submission.');
            $this->redirect('/properties/view?id=' . $propertyId);
        }

        $success = Rating::create(Auth::getCurrentUserId(), $propertyId, $rating, $review);
        $this->setFlash($success ? 'success' : 'danger', $success ? 'Rating submitted.' : 'Unable to submit rating.');
        $this->redirect('/properties/view?id=' . $propertyId);
    }
}

?>

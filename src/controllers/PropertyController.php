<?php

class PropertyController extends BaseController {
    public function index(): void {
        $filters = [
            'city' => $_GET['city'] ?? '',
            'deal_type' => $_GET['deal_type'] ?? '',
            'category' => $_GET['category'] ?? '',
        ];

        $properties = Property::search($filters);
        $this->render('properties/index', [
            'properties' => $properties,
            'filters' => $filters,
        ]);
    }

    public function show(): void {
        $propertyId = (int) ($_GET['id'] ?? 0);
        if ($propertyId <= 0) {
            $this->redirect('/properties');
        }

        $property = Property::getById($propertyId);
        if (!$property) {
            $this->setFlash('danger', 'Property not found.');
            $this->redirect('/properties');
        }

        $ratings = Rating::getByProperty($propertyId);

        $this->render('properties/show', [
            'property' => $property,
            'ratings' => $ratings,
        ]);
    }

    public function ownerIndex(): void {
        Auth::requireOwner();
        $properties = Property::getByOwner(Auth::getCurrentUserId());
        $this->render('properties/owner', ['properties' => $properties]);
    }

    public function create(): void {
        Auth::requireOwner();
        $this->render('properties/form', ['property' => null]);
    }

    public function store(): void {
        Auth::requireOwner();
        $this->requireCsrfToken();

        $data = $this->collectPropertyData();
        if ($data['error']) {
            $this->setFlash('danger', $data['error']);
            $this->redirect('/owner/properties/create');
        }

        $data['owner_id'] = Auth::getCurrentUserId();
        $propertyId = Property::create($data);
        Activity::logPropertyCreated(Auth::getCurrentUserId(), $propertyId, $data['title']);

        $this->setFlash('success', 'Property added successfully.');
        $this->redirect('/owner/properties');
    }

    public function edit(): void {
        Auth::requireOwner();
        $propertyId = (int) ($_GET['id'] ?? 0);
        if ($propertyId <= 0) {
            $this->redirect('/owner/properties');
        }

        $property = Property::getById($propertyId);
        if (!$property || (int) $property['owner_id'] !== Auth::getCurrentUserId()) {
            $this->setFlash('danger', 'Access denied.');
            $this->redirect('/owner/properties');
        }

        $this->render('properties/form', ['property' => $property]);
    }

    public function update(): void {
        Auth::requireOwner();
        $this->requireCsrfToken();

        $propertyId = (int) ($_POST['id'] ?? 0);
        $property = Property::getById($propertyId);
        if (!$property || (int) $property['owner_id'] !== Auth::getCurrentUserId()) {
            $this->setFlash('danger', 'Access denied.');
            $this->redirect('/owner/properties');
        }

        $data = $this->collectPropertyData($property['featured_image'] ?? '');
        if ($data['error']) {
            $this->setFlash('danger', $data['error']);
            $this->redirect('/owner/properties/edit?id=' . $propertyId);
        }

        Property::update($propertyId, Auth::getCurrentUserId(), $data);
        Activity::logPropertyUpdated(Auth::getCurrentUserId(), $propertyId, $data['title']);

        $this->setFlash('success', 'Property updated successfully.');
        $this->redirect('/owner/properties');
    }

    private function collectPropertyData(string $currentImage = ''): array {
        $required = ['title', 'address', 'city', 'price_per_month', 'bedrooms', 'bathrooms'];
        $errors = Validator::required($_POST, $required);
        if ($errors) {
            return ['error' => reset($errors)];
        }

        $featuredImage = $currentImage;
        if (!empty($_FILES['featured_image']['name'])) {
            $validation = Validator::file($_FILES['featured_image']);
            if (!$validation['valid']) {
                return ['error' => $validation['message']];
            }

            $filename = uniqid('property_', true) . '.' . strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
            $targetPath = UPLOAD_PATH . $filename;
            if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
                return ['error' => 'Unable to upload image'];
            }
            $featuredImage = $targetPath;
        }

        return [
            'error' => '',
            'title' => Validator::sanitizeString($_POST['title'] ?? ''),
            'description' => Validator::sanitizeString($_POST['description'] ?? ''),
            'address' => Validator::sanitizeString($_POST['address'] ?? ''),
            'city' => Validator::sanitizeString($_POST['city'] ?? ''),
            'state' => Validator::sanitizeString($_POST['state'] ?? ''),
            'country' => Validator::sanitizeString($_POST['country'] ?? ''),
            'postal_code' => Validator::sanitizeString($_POST['postal_code'] ?? ''),
            'price_per_month' => (float) ($_POST['price_per_month'] ?? 0),
            'bedrooms' => (int) ($_POST['bedrooms'] ?? 1),
            'bathrooms' => (int) ($_POST['bathrooms'] ?? 1),
            'square_feet' => (int) ($_POST['square_feet'] ?? 0),
            'property_type' => Validator::sanitizeString($_POST['property_type'] ?? ''),
            'amenities' => Validator::sanitizeString($_POST['amenities'] ?? ''),
            'featured_image' => $featuredImage,
        ];
    }
}

?>

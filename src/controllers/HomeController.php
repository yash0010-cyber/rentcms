<?php

class HomeController extends BaseController {
    public function index(): void {
        $filters = [
            'city' => $_GET['city'] ?? '',
            'deal_type' => $_GET['deal_type'] ?? '',
            'category' => $_GET['category'] ?? '',
        ];

        $properties = Property::search($filters);
        if (empty($properties)) {
            $properties = Property::getDemoListings();
        }

        $this->render('home/index', [
            'properties' => $properties,
            'filters' => $filters,
        ]);
    }
}

?>

<?php
class ReviewController extends BaseController
{
    private $reviewModel;

    public function __construct()
    {
        $this->reviewModel = $this->loadModel('ReviewModel');
    }

    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
        $reviews = $this->reviewModel->getAllReviews($keyword);

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'review_list',
            'reviews' => $reviews,
            'keyword' => $keyword
        ]);
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $this->reviewModel->deleteReview($_GET['id']);
        }
        header("Location: index.php?module=Admin&controller=Review");
    }
}

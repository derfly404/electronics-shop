<?php
class CommentController extends BaseController
{
    private $commentModel;

    public function __construct()
    {
        $this->commentModel = $this->loadModel('CommentModel');
    }

    // Danh sách bình luận tin tức
    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
        $comments = $this->commentModel->getAllComments($keyword);

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'comment_list',
            'comments' => $comments,
            'keyword' => $keyword
        ]);
    }

    // Xóa bình luận
    public function delete()
    {
        if (isset($_GET['id'])) {
            $this->commentModel->deleteComment($_GET['id']);
        }
        header("Location: index.php?module=Admin&controller=Comment");
    }
}

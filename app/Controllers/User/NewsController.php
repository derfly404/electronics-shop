<?php
class NewsController extends BaseController
{
    private $newsModel;
    private $commentModel; // Khai báo thêm

    public function __construct()
    {
        $this->newsModel = $this->loadModel('NewsModel');
        $this->commentModel = $this->loadModel('CommentModel'); // Load Model
    }

    public function index()
    {
        $newsList = $this->newsModel->getActiveNews();
        $this->loadView('User', 'news_list', ['newsList' => $newsList]);
    }

    // --- CẬP NHẬT HÀM DETAIL ---
    public function detail()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $news = $this->newsModel->getNewsById($id);

        if ($news) {
            // --- LOGIC MỚI: KIỂM TRA SESSION ---

            // Tạo một cái tên khóa duy nhất cho bài viết này, ví dụ: 'viewed_news_15'
            $sessionKey = 'viewed_news_' . $id;

            // Nếu trong Session CHƯA CÓ cái khóa này (nghĩa là lần đầu xem trong phiên này)
            if (!isset($_SESSION[$sessionKey])) {
                $this->newsModel->increaseView($id); // Thì mới tăng view
                $_SESSION[$sessionKey] = true;       // Và đánh dấu là đã xem
            }

            // --- HẾT LOGIC MỚI ---

            $comments = $this->commentModel->getCommentsByNews($id);
            $count = $this->commentModel->countComments($id);

            $this->loadView('User', 'news_detail', [
                'news' => $news,
                'comments' => $comments,
                'commentCount' => $count
            ]);
        } else {
            die("Bài viết không tồn tại");
        }
    }

    // --- MỚI: HÀM GỬI BÌNH LUẬN ---
    public function submit_comment()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['user'])) {
                // Nếu chưa đăng nhập thì chuyển hướng đăng nhập
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }

            $newsId = $_POST['news_id'];
            $content = $_POST['noi_dung'];
            $userId = $_SESSION['user']['id'];

            if (!empty($content)) {
                $this->commentModel->addComment([
                    ':noidung' => $content,
                    ':matin' => $newsId,
                    ':mauid' => $userId
                ]);
            }

            // Quay lại trang bài viết
            header("Location: index.php?controller=News&action=detail&id=" . $newsId . "#comments");
        }
    }

    // --- MỚI: Xóa bình luận ---
    public function delete_comment()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }

        $commentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $newsId = isset($_GET['news_id']) ? intval($_GET['news_id']) : 0;
        $userId = $_SESSION['user']['id'];

        // Gọi hàm xóa có kiểm tra quyền sở hữu
        $this->commentModel->deleteCommentByUser($commentId, $userId);

        // Quay lại trang tin tức
        header("Location: index.php?controller=News&action=detail&id=" . $newsId . "#comments");
    }

    // --- MỚI: Sửa bình luận ---
    public function update_comment()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['user'])) {
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }

            $commentId = $_POST['comment_id'];
            $newsId = $_POST['news_id'];
            $content = $_POST['noi_dung'];
            $userId = $_SESSION['user']['id'];

            if (!empty($content)) {
                $this->commentModel->updateComment($commentId, $userId, $content);
            }

            header("Location: index.php?controller=News&action=detail&id=" . $newsId . "#comments");
        }
    }
}

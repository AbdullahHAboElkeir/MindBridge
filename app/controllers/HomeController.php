<?php

/**
 * Controller: Home
 * Public landing page.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard');
        }
        $pageTitle = 'Mental Health & Wellness Portal';
        $this->view('home.index', compact('pageTitle'));
    }

    /** GET /search?q=query */
    public function search(): void
    {
        Middleware::requireAuth();
        $query = trim($this->get('q', ''));
        $results = [];

        if (!empty($query)) {
            $db = Database::getInstance();

            // Search therapists
            $therapists = $db->fetchAll(
                "SELECT t.id, u.first_name, u.last_name, t.specializations, t.languages, t.bio, t.rating, t.total_reviews
                 FROM therapists t
                 JOIN users u ON u.id = t.user_id
                 WHERE u.status = 'active' AND t.is_available = 1
                   AND (u.first_name LIKE ? OR u.last_name LIKE ? OR t.specializations LIKE ? OR t.languages LIKE ? OR t.bio LIKE ?)",
                ["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"]
            );

            // Search forum posts
            $forumPosts = $db->fetchAll(
                "SELECT f.id, f.title, f.content, f.created_at, u.first_name, u.last_name
                 FROM forum_posts f
                 JOIN users u ON u.id = f.user_id
                 WHERE f.title LIKE ? OR f.content LIKE ?",
                ["%{$query}%", "%{$query}%"]
            );

            // Search wellness resources
            $resources = $db->fetchAll(
                "SELECT id, title, description, category, created_at
                 FROM wellness_resources
                 WHERE title LIKE ? OR description LIKE ? OR category LIKE ?",
                ["%{$query}%", "%{$query}%", "%{$query}%"]
            );

            $results = [
                'therapists' => $therapists,
                'forum_posts' => $forumPosts,
                'resources' => $resources,
            ];
        }

        $pageTitle = 'Search Results';
        $this->view('search.index', compact('pageTitle', 'query', 'results'));
    }
}

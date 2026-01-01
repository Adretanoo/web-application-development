<?php

namespace Acer\Pr8\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Acer\Pr8\models\Comment;

class CommentController
{
    private function requireAuth(Response $response)
    {
        if (empty($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        return null;
    }

    public function create(Request $request, Response $response)
    {
        if ($redirect = $this->requireAuth($response)) return $redirect;

        $data = $request->getParsedBody();
        $post_id = $data['post_id'] ?? null;
        $content = trim($data['content'] ?? '');

        if ($post_id && !empty($content)) {
            Comment::create([
                'user_id' => $_SESSION['user_id'],
                'post_id' => $post_id,
                'content' => $content,
            ]);
        }

        return $response->withHeader('Location', '/posts/' . $post_id)->withStatus(302);
    }

    public function update(Request $request, Response $response, array $args)
    {
        if ($redirect = $this->requireAuth($response)) return $redirect;

        $comment = Comment::findOrFail($args['id']);

        $isOwner = $comment->user_id === $_SESSION['user_id'];
        $isAdmin = !empty($_SESSION['is_admin']) && $_SESSION['is_admin'];

        if (!$isOwner && !$isAdmin) {
            return $response->withStatus(403);
        }

        $data = $request->getParsedBody();
        $content = trim($data['content'] ?? '');

        if (!empty($content)) {
            $comment->update(['content' => $content]);
        }

        return $response->withHeader('Location', '/posts/' . $comment->post_id)->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args)
    {
        if ($redirect = $this->requireAuth($response)) return $redirect;

        $comment = Comment::findOrFail($args['id']);

        $isOwner = $comment->user_id === $_SESSION['user_id'];
        $isAdmin = !empty($_SESSION['is_admin']) && $_SESSION['is_admin'];

        if ($isOwner || $isAdmin) {
            $post_id = $comment->post_id;
            $comment->delete();
            return $response->withHeader('Location', '/posts/' . $post_id)->withStatus(302);
        }

        return $response->withStatus(403);
    }
}
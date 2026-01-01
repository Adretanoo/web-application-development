<?php

namespace Acer\Pr8\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Acer\Pr8\models\Post;
use Acer\Pr8\models\Tag;

class BlogController
{
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    private function requireAdmin(Response $response)
    {
        if (empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        return null;
    }

    public function index(Request $request, Response $response)
    {
        $posts = Post::with(['user', 'tags'])->latest()->get();

        return $this->view->render($response, 'posts/index.twig', ['posts' => $posts]);
    }

    public function create(Request $request, Response $response)
    {
        if ($redirect = $this->requireAdmin($response)) return $redirect;

        $tags = Tag::all();

        return $this->view->render($response, 'posts/create.twig', ['tags' => $tags]);
    }

    public function store(Request $request, Response $response)
    {
        if ($redirect = $this->requireAdmin($response)) return $redirect;

        $data = $request->getParsedBody();

        $post = Post::create([
            'user_id' => $_SESSION['user_id'],
            'title'   => $data['title'] ?? '',
            'content' => $data['content'] ?? '',
        ]);

        if (!empty($data['tags']) && is_array($data['tags'])) {
            $post->tags()->attach($data['tags']);
        }

        return $response->withHeader('Location', '/posts/' . $post->id)->withStatus(302);
    }

    public function show(Request $request, Response $response, array $args)
    {
        $post = Post::with(['user', 'comments.user', 'tags'])->findOrFail($args['id']);

        return $this->view->render($response, 'posts/show.twig', ['post' => $post]);
    }

    public function edit(Request $request, Response $response, array $args)
    {
        if ($redirect = $this->requireAdmin($response)) return $redirect;

        $post = Post::findOrFail($args['id']);
        $tags = Tag::all();
        $selectedTags = $post->tags->pluck('id')->toArray();

        return $this->view->render($response, 'posts/edit.twig', [
            'post' => $post,
            'tags' => $tags,
            'selectedTags' => $selectedTags
        ]);
    }

    public function update(Request $request, Response $response, array $args)
    {
        if ($redirect = $this->requireAdmin($response)) return $redirect;

        $post = Post::findOrFail($args['id']);
        $data = $request->getParsedBody();

        $post->update([
            'title'   => $data['title'] ?? $post->title,
            'content' => $data['content'] ?? $post->content,
        ]);

        $post->tags()->sync($data['tags'] ?? []);

        return $response->withHeader('Location', '/posts/' . $post->id)->withStatus(302);
    }

    public function destroy(Request $request, Response $response, array $args)
    {
        if ($redirect = $this->requireAdmin($response)) return $redirect;

        $post = Post::findOrFail($args['id']);
        $post->delete();

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
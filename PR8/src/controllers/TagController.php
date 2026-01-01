<?php

namespace Acer\Pr8\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Acer\Pr8\models\Tag;

class TagController
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
        if ($redirect = $this->requireAdmin($response)) return $redirect;

        $tags = Tag::orderBy('name')->get();

        return $this->view->render($response, 'tags/index.twig', ['tags' => $tags]);
    }

    public function store(Request $request, Response $response)
    {
        if ($redirect = $this->requireAdmin($response)) return $redirect;

        $data = $request->getParsedBody();
        $name = trim($data['name'] ?? '');

        if (!empty($name)) {
            Tag::firstOrCreate(['name' => $name]);
        }

        return $response->withHeader('Location', '/tags')->withStatus(302);
    }

    public function destroy(Request $request, Response $response, array $args)
    {
        if ($redirect = $this->requireAdmin($response)) return $redirect;

        $tag = Tag::findOrFail($args['id']);
        $tag->delete();

        return $response->withHeader('Location', '/tags')->withStatus(302);
    }
}
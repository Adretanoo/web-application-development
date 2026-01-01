<?php

namespace Acer\Pr8\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use  Acer\Pr8\models\User;

class AuthController
{
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function showLoginForm(Request $request, Response $response)
    {
        return $this->view->render($response, 'auth/login.twig');
    }

    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $user = User::where('username', $data['username'] ?? '')->first();

        if ($user && password_verify($data['password'] ?? '', $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['is_admin'] = $user->role === 'admin';

            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $this->view->render($response, 'auth/login.twig', [
            'error' => 'Неправильний логін або пароль'
        ]);
    }

    public function showRegistrationForm(Request $request, Response $response)
    {
        return $this->view->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (($data['password'] ?? '') !== ($data['password_confirm'] ?? '')) {
            return $this->view->render($response, 'auth/register.twig', [
                'error' => 'Паролі не співпадають'
            ]);
        }

        $user = User::create([
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'email'    => $data['email'] ?? null,
            'role'     => 'user',
        ]);

        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['is_admin'] = false;

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logout(Request $request, Response $response)
    {
        session_destroy();
        session_start();

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
</head>
<body>
<nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/tasks">Tasks</a> | <a href="/logout">Logout</a>
    <?php else: ?>
        <a href="/">Login</a> | <a href="/register">Register</a>
    <?php endif; ?>
</nav>
<hr>
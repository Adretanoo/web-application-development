<?php
declare(strict_types=1);

namespace Acer\Pr7\enum;

enum TaskStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}
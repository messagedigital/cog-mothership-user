<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\TasksInterface;
use Message\Mothership\User\Task;

class Tasks implements TasksInterface
{
    public function registerTasks($tasks)
    {
        // Order related ports
        $tasks->add(new Task\Porting\EmailSubscriptions('user:porting:email_subscription'), 'Ports subscribed emails from pre mothership');
    }
}
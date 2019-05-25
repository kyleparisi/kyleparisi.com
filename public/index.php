<?php

require __DIR__ . "/../vendor/autoload.php";

use RedBeanPHP\R as R;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Jenssegers\Blade\Blade;

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
*/
session_start();

/*
|--------------------------------------------------------------------------
| Logging
|--------------------------------------------------------------------------
*/
$log = new Logger('app');
if (PHP_SAPI !== 'cli') {
    $formatter = new JsonFormatter();
    try {
        $stream = new StreamHandler(
            __DIR__ . '/../application.log',
            Logger::DEBUG
        );
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }
    $stream->setFormatter($formatter);
    $log->pushHandler($stream);
}
$log->pushHandler(new \Monolog\Handler\ErrorLogHandler());

/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/
R::setup(
    PHP_SAPI === 'cli'
        ? 'sqlite::memory:'
        : 'sqlite:' . __DIR__ . '/../application.db'
);
R::setAutoResolve(true);

/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/

function isLoggedIn($next)
{
    return function ($params) use ($next) {
        if ($_SESSION['user'] ?? false) {
            return call_user_func($next, $params);
        }

        $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];
        redirect("Location: /login");
        return false;
    };
}

function isAdmin($next)
{
    return isLoggedIn(function ($params) use ($next) {
        if ($_SESSION['user']['role'] === 'admin' ?? false) {
            return call_user_func($next, $params);
        }

        header($_SERVER["SERVER_PROTOCOL"] . ' 403 Forbidden');
        echo "Not allowed";
        exit();
    });
}

function slugify($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    //    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

/*
|--------------------------------------------------------------------------
| Application
|--------------------------------------------------------------------------
*/

$router = new AltoRouter();
$parameters = (object) $_GET;
$body = json_decode(file_get_contents('php://input')) ?? (object) $_POST;
/** @var AppBlade $blade */
$blade = new Blade([__DIR__ . '/../views'], sys_get_temp_dir());
function redirect($location)
{
    if (PHP_SAPI === "cli") {
        return $location;
    }

    header($location);
    exit();
}

function user()
{
    return $_SESSION['user'];
}

function randomKey($length = null)
{
    try {
        $bytes = random_bytes($length ?? 16);
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Ran out of entropy.";
        exit();
    }

    return bin2hex($bytes);
}

/**
 * Checks if user asked to be remembered.  Log them in if their key still exists and update expiration.
 * @return bool
 */
function remembered()
{
    if (isset($_SESSION['user']) || !isset($_COOKIE['remember'])) {
        return false;
    }

    $remember_key = $_COOKIE['remember'];
    /** @var Remember $remembered */
    $remembered = R::findOne('remember', 'key = ?', [$remember_key]);
    if (!$remembered) {
        return false;
    }

    $_SESSION['user'] = $remembered->user;
    $remembered->expires = strtotime("2 weeks");
    R::store($remembered);
    setcookie("remember", $remember_key, $remembered->expires, "/");

    return true;
}
remembered();

try {
    $router->map(
        'GET',
        '/',
        function () use ($log, $blade) {
            $posts = R::findAll("post", "order by `id` DESC");
            return $blade->make('homepage', compact('posts'));
        },
        'home'
    );

    $router->map(
        'GET',
        '/privacy',
        function () use ($log, $blade) {
            return $blade->make('privacy');
        },
        'privacy'
    );

    $router->map(
        'GET',
        '/terms',
        function () use ($log, $blade) {
            return $blade->make('terms');
        },
        'terms'
    );

    $router->map(
        'GET',
        '/login',
        function () use ($log, $blade) {
            return $blade->make('login');
        },
        'login'
    );

    $router->map('POST', '/login', function () use ($log, $blade, &$body) {
        $errors = new stdClass();
        $email = $body->email ?? false;
        $password = $body->password ?? false;
        if (!$email) {
            $errors->email = "Please provide an email address.";
        }
        if (!$password) {
            $errors->password = "Please provide a password.";
        }
        if (isset($errors->email) || isset($errors->password)) {
            $log->info("Incorrect login attempt.", compact('errors'));
            return $blade->make('login', compact('errors', 'email'));
        }
        /** @var User $user */
        $user = R::findOne('user', 'email = ?', [$email]);

        if (!$user) {
            $log->info("Incorrect email attempt.", compact('email'));
            $errors->invalid = "Email or password is incorrect";
            return $blade->make('login', compact('errors', 'email'));
        }

        if (!password_verify($password, $user->password)) {
            $log->info("Incorrect password attempt.", compact('email'));
            $errors->invalid = "Email or password is incorrect";
            return $blade->make('login', compact('errors', 'email'));
        }

        $log->info('User logged in.', compact('email'));

        if (isset($body->remember)) {
            $log->info("Remember user", compact('email'));
            $bytes = random_bytes(16);
            $key = bin2hex($bytes);
            /** @var Remember $remember */
            $remember = R::dispense('remember');
            $remember->user = $user;
            $remember->key = $key;
            $remember->expires = strtotime("2 weeks");
            R::store($remember);
            setcookie("remember", $key, $remember->expires, "/");
        }

        $_SESSION['user'] = $user;
        return redirect("Location: /dashboard");
    });

    $router->map(
        'GET',
        '/sign-up',
        function () use ($log, $blade) {
            return $blade->make('sign-up');
        },
        'sign-up'
    );

    $router->map('POST', '/sign-up', function () use ($log, $blade, &$body) {
        $errors = new stdClass();
        $email = $body->email ?? false;
        $password = $body->password ?? false;
        if (!$email) {
            $errors->email = "Please provide an email address.";
        }
        if (!$password) {
            $errors->password = "Please provide a password.";
        }

        if (isset($errors->email) || isset($errors->password)) {
            $log->info("Incorrect login attempt.", compact('errors'));
            return $blade->make('sign-up', compact('errors', 'email'));
        }
        $password = password_hash($password, PASSWORD_BCRYPT);

        $user = R::findOne('user', 'email = ?', [$email]);

        if (!is_null($user)) {
            $errors->exists = "User already exists.";
            return $blade->make('sign-up', compact('errors', 'email'));
        }

        $log->info('User signed up.', compact('email'));

        /** @var User $user */
        $user = R::dispense('user');
        $user->email = $email;
        $user->password = $password;
        $user->forgot_token = "";
        $user->verified_email = false;
        $user->role = 'user';
        R::store($user);

        $_SESSION['user'] = $user;

        // shouldn't have a remember cookie on sign up
        PHP_SAPI === "cli" ?: setcookie("remember", "", time() - 3600);
        return redirect("Location: /dashboard");
    });

    $router->map(
        'GET',
        '/password/email',
        function () use ($log, $blade) {
            return $blade->make('forgot');
        },
        'forgot'
    );

    $router->map('POST', '/password/email', function () use (
        $log,
        $blade,
        &$body
    ) {
        $errors = new stdClass();
        $email = $body->email ?? false;
        $log->info("User forgot password.", compact('email'));
        if (!$email) {
            $errors->email = "Please provide an email address.";
        }
        if (isset($errors->email)) {
            return $blade->make('forgot', compact('errors', 'email'));
        }

        $user = R::findOne('user', 'email = ?', [$email]);

        if (!$user) {
            $errors->email = "Email address not recognized.";
            return $blade->make('forgot', compact('errors', 'email'));
        }

        // TODO: send email here
        // recommended postmark

        $success = new stdClass();
        $success->message = "An email has been sent to reset your password.";
        return $blade->make('forgot', compact('success'));
    });

    $router->map('GET', '/session', function () {
        var_dump($_SESSION);
        exit();
    });

    $router->map(
        'GET',
        '/logout',
        function () use ($log, $blade) {
            setcookie("remember", "", time() - 3600);
            session_destroy();
            return redirect("Location: /");
        },
        'logout'
    );

    $router->map('GET', '/newsletter/subscribe', function () use (
        $log,
        $blade
    ) {
        return $blade->make('subscribe');
    });

    $router->map('POST', '/newsletter/subscribe', function () use (
        $log,
        $blade,
        &$body
    ) {
        $errors = new stdClass();
        $email = $body->email ?? false;
        if (!$email) {
            $errors->email = "- Email required.";
            $log->info("Newsletter sign up failed.", compact('errors', 'body'));
            return $blade->make('subscribe', compact('errors', 'body'));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors->email = "- Email failed validation.";
            $log->info("Newsletter sign up failed.", compact('errors', 'body'));
            return $blade->make('subscribe', compact('errors', 'body'));
        }
        $success = new stdClass();
        /** @var Subscriber $subscriber */
        $subscriber = R::findOne('subscriber', 'email = ?', [$email]);
        if ($subscriber && $subscriber->subscribed) {
            $success->message = "You are already subscribed!";
            return $blade->make('subscribe', compact('success'));
        }

        if ($subscriber && !$subscriber->subscribed) {
            $subscriber->subscribed = true;
            // make a new hash to negate previous unsubscribe links in sent emails
            $subscriber->hash_id = randomKey(4);
            R::store($subscriber);
            $success->message = "You are now subscribed!";
            return $blade->make('subscribe', compact('success'));
        }

        $log->info("New newsletter sign up validated.", compact('body'));
        $newsLetterSignUp = R::dispense("subscriber");
        $body->created = date('Y-m-d H:i:s');
        $body->subscribed = true;
        $body->hash_id = randomKey(4);
        $newsLetterSignUp->import($body, [
            'firstName',
            'email',
            'subscribed',
            'hash_id',
            'created'
        ]);
        R::store($newsLetterSignUp);
        $success->message = "You are now subscribed!";
        return $blade->make('subscribe', compact('success'));
    });

    $router->map('GET', '/newsletter/unsubscribe', function () use (
        $log,
        $blade,
        &$parameters
    ) {
        $errors = new stdClass();
        if (empty($parameters->id)) {
            $errors->message = "Unknown email address";
            return $blade->make('unsubscribe', compact('errors'));
        }

        /** @var Subscriber $subscriber */
        $subscriber = R::findOne("subscriber", "hash_id = ?", [
            $parameters->id
        ]);

        if (!$subscriber) {
            $errors->message = "Unknown email address";
            return $blade->make('unsubscribe', compact('errors'));
        }

        $subscriber->subscribed = false;
        R::store($subscriber);

        $success = new stdClass();
        $success->message = "You have been unsubscribed from the newsletter.";
        return $blade->make('unsubscribe', compact('success'));
    });

    $router->map(
        'GET',
        '/blog/post',
        isAdmin(function () use ($log, $blade) {
            return $blade->make('blog');
        })
    );

    $router->map('GET', '/blog', function () use ($log, $blade) {
        $posts = R::findAll("post", "order by `id` DESC");
        $user = $_SESSION['user'] ?? false;
        if (!$posts) {
            redirect("Location: /");
        }
        return $blade->make('blog-list', compact('posts', 'user'));
    });

    $router->map('GET', '/blog/[*:title]/edit', isAdmin(function ($path) use ($log, $blade) {
        /** @var Post $post */
        $post = R::findOne("post", "slug = ?", [urldecode($path->title)]);
        if (!$post) {
            redirect("Location: /blog");
        }
        return $blade->make('blog', compact('post'));
    }));

    $router->map('POST', '/blog/[*:title]/edit', isAdmin(function ($path) use ($log, $blade, $body) {
        /** @var Post $post */
        $post = R::findOne("post", "slug = ?", [urldecode($path->title)]);
        if (!$post) {
            redirect("Location: /blog");
        }
        $post->title = $body->title;
        $post->content = $body->content;
        R::store($post);
        return $blade->make('blog', compact('post'));
    }));

    $router->map('GET', '/blog/[*:title]', function ($path) use ($log, $blade) {
        $ParseDown = new Parsedown();
        /** @var Post $post */
        $post = R::findOne("post", "slug = ?", [urldecode($path->title)]);
        if (!$post) {
            redirect("Location: /blog");
        }
        $post->content = $ParseDown->text($post->content);
        return $blade->make('blog-page', compact('post'));
    });

    $router->map('DELETE', '/blog/[*:title]', isAdmin(function ($path) use ($log, $blade) {
        /** @var Post $post */
        $post = R::findOne("post", "slug = ?", [urldecode($path->title)]);
        if (!$post) {
            redirect("Location: /blog");
        }
        R::trash($post);
        return $blade->make('blog-page');
    }));

    $router->map(
        'POST',
        '/blog/post',
        isAdmin(function () use ($log, $blade, &$body) {
            /** @var Post $post */
            $post = R::dispense('post');
            $post->title = $body->title;
            $post->slug = slugify($body->title);
            $post->author = R::load('user', $_SESSION['user']['id']);
            $post->content = $body->content;
            $post->deleted = false;
            $post->draft = true;
            $post->date = date("M d, Y");
            R::store($post);
            return $blade->make('blog');
        })
    );

    $router->map(
        'POST',
        '/parser/preview',
        isAdmin(function () use ($log, $blade, &$body) {
            $parser = new Parsedown();
            echo $parser->text($body->content);
            echo PHP_EOL;
            exit();
        })
    );

    $router->map(
        'GET',
        '/components/chat',
        function () use ($log, $blade, &$body) {
            $note = R::load("note", 1);
            return $blade->make('chat', compact('note'));
        }
    );

    $router->map(
        'GET',
        '/components/chat/edit',
        isAdmin(function () use ($log, $blade, &$body) {
            $note = R::load("note", 1);
            return $blade->make('chat-edit', compact('note'));
        })
    );

    $router->map(
        'POST',
        '/components/chat/edit',
        isAdmin(function () use ($log, $blade, &$body) {
            /** @var Document $post */
            $note = R::load('note', 1);
            $note->title = $body->title;
            $note->slug = slugify($body->title);
            $note->author = R::load('user', $_SESSION['user']['id']);
            $note->content = $body->content;
            $note->deleted = false;
            $note->draft = true;
            $note->date = date("M d, Y");
            R::store($note);
            return $blade->make('chat-edit', compact('note'));
        })
    );

} catch (Exception $exception) {
    $log->emergency("", compact('exception'));
    header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Sever Error');
}

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/
if (PHP_SAPI === "cli") {
    return;
}

$match = $router->match();
if ($match && is_callable($match['target'])) {
    $response = call_user_func($match['target'], (object)$match['params']);
    if ($response instanceof Illuminate\View\View) {
        if (
            isset($_SERVER['HTTP_ACCEPT']) &&
            strpos($_SERVER['HTTP_ACCEPT'], "application/json") !== false
        ) {
            header('Content-Type: application/json');
            echo json_encode($response->getData());
            echo "\n";
            return;
        }
        echo $response;
        return;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    echo "\n";
} else {
    // no route was matched
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    include "404.html";
}

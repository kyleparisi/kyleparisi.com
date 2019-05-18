<?php
use RedBeanPHP\R as R;

assert_options(ASSERT_EXCEPTION, 1);
require_once "public/index.php";
error_reporting(-1);
function title($msg) {
    fwrite(STDOUT, "\e[32m" . $msg . "\e[0m" . PHP_EOL);
}

title("Test: Login: No email or password.");
$match = $router->match('/login', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Please provide an email"));
assert(strpos($view, "Please provide a password"));

title("Test: Login: No password.");
$body->email = "test@test.com";
$match = $router->match('/login', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Please provide an email") === false);
assert(strpos($view, "Please provide a password"));

title("Test: Login: No user.");
$body->password = "test";
$match = $router->match('/login', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Please provide an email") === false);
assert(strpos($view, "Please provide a password") === false);
assert(strpos($view, "Email or password is incorrect"));

title("Test: Sign-up: No email or password.");
$body = new stdClass();
$match = $router->match('/sign-up', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Please provide an email"));
assert(strpos($view, "Please provide a password"));

title("Test: Sign-up: No password.");
$body->email = "test@test.com";
$match = $router->match('/sign-up', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Please provide an email") === false);
assert(strpos($view, "Please provide a password"));

title("Test: Sign-up:");
$body->password = "test";
$match = $router->match('/sign-up', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Location") !== false);
assert(R::count('user') === 1);

title("Test: Login:");
$match = $router->match('/login', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Location") !== false);
assert(isset($_SESSION['user']));

$body = new stdClass();

title("Test: Forgot Password: missing email");
$match = $router->match('/password/email', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Please provide an email"));

title("Test: Forgot Password: not a user");
$body->email = "fake";
$match = $router->match('/password/email', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Email address not recognized"));

title("Test: Forgot Password:");
$body->email = "test@test.com";
$match = $router->match('/password/email', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "An email has been sent"));

$body = new stdClass();

title("Test: Subscribe: view");
$match = $router->match('/newsletter/subscribe', 'GET');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Subscribe"));

title("Test: Subscribe: no data");
$match = $router->match('/newsletter/subscribe', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Email required"));

title("Test: Subscribe: invalid email");
$body->email = "blah";
$match = $router->match('/newsletter/subscribe', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Email failed validation"));

title("Test: Subscribe: valid email");
$body->email = "test@test.com";
$match = $router->match('/newsletter/subscribe', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "You are now subscribed"));

title("Test: Subscribe: valid email");
$body->email = "test@test.com";
$match = $router->match('/newsletter/subscribe', 'POST');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "You are already subscribed"));

$body = new stdClass();

title("Test: Unsubscribe: no hash");
$match = $router->match('/newsletter/unsubscribe', 'GET');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Unknown email address"));

title("Test: Unsubscribe: bad hash");
$match = $router->match('/newsletter/unsubscribe?id=blah', 'GET');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "Unknown email address"));

title("Test: Unsubscribe:");
/** @var Subscriber $subscriber */
$subscriber = R::load("subscriber", 1);
$parameters->id = $subscriber->hash_id;
$match = $router->match('/newsletter/unsubscribe', 'GET');
$view = call_user_func_array($match['target'], $match['params']);
assert(strpos($view, "You have been unsubscribed"));

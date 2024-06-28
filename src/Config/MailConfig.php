<?php

namespace App\config;

define('MAIL_SMTP_HOST', getenv('MAIL_SMTP_HOST'));
define('MAIL_SMTP_USERNAME', getenv('MAIL_SMTP_USERNAME'));
define('MAIL_SMTP_PASSWORD', getenv('MAIL_SMTP_PASSWORD'));
define('MAIL_SMTP_PORT', getenv('MAIL_SMTP_PORT'));
define('MAIL_SENDER_EMAIL', getenv('MAIL_SENDER_EMAIL'));
define('MAIL_SENDER_NAME', getenv('MAIL_SENDER_NAME'));

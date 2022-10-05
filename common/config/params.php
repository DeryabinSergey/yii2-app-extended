<?php

defined('ROLE_ADMIN') || define('ROLE_ADMIN', 'admin');
defined('ROLE_MANAGER') || define('ROLE_MANAGER', 'manager');
defined('ROLE_VIEWER') || define('ROLE_VIEWER', 'viewer');

defined('PERMISSION_BACKEND') || define('PERMISSION_BACKEND', 'backend');

defined('PERMISSION_USER_CREATE') || define('PERMISSION_USER_CREATE', 'userCreate');
defined('PERMISSION_USER_READ') || define('PERMISSION_USER_READ', 'userRead');
defined('PERMISSION_USER_UPDATE') || define('PERMISSION_USER_UPDATE', 'userUpdate');
defined('PERMISSION_USER_DELETE') || define('PERMISSION_USER_DELETE', 'userDelete');

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
];

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('app_read_environment_value')) {
    function app_read_environment_value(string $key): ?string
    {
        if (function_exists('readEnvironmentValue')) {
            $value = readEnvironmentValue($key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        $envValue = getenv($key);
        if ($envValue !== false && $envValue !== '') {
            return $envValue;
        }

        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string) $_ENV[$key];
        }

        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string) $_SERVER[$key];
        }

        return null;
    }
}

if (!function_exists('app_local_config')) {
    function app_local_config(): array
    {
        static $config = null;

        if ($config !== null) {
            return $config;
        }

        $configPath = __DIR__ . '/../backend/config.local.php';
        if (!file_exists($configPath)) {
            $config = [];
            return $config;
        }

        $loadedConfig = require $configPath;
        $config = is_array($loadedConfig) ? $loadedConfig : [];

        return $config;
    }
}

if (!function_exists('app_config_value')) {
    function app_config_value(string $configKey, array $envKeys = []): ?string
    {
        $config = app_local_config();
        if (array_key_exists($configKey, $config) && is_scalar($config[$configKey])) {
            $value = (string) $config[$configKey];
            if ($value !== '') {
                return $value;
            }
        }

        foreach ($envKeys as $envKey) {
            $envValue = app_read_environment_value($envKey);
            if ($envValue !== null && $envValue !== '') {
                return $envValue;
            }
        }

        return null;
    }
}

if (!function_exists('admin_username')) {
    function admin_username(): ?string
    {
        return app_config_value('admin_user', ['ADMIN_USER']);
    }
}

if (!function_exists('admin_password')) {
    function admin_password(): ?string
    {
        return app_config_value('admin_pass', ['ADMIN_PASS']);
    }
}

if (!function_exists('admin_credentials_are_configured')) {
    function admin_credentials_are_configured(): bool
    {
        return admin_username() !== null && admin_password() !== null;
    }
}

if (!function_exists('set_flash_message')) {
    function set_flash_message(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('redirect_to')) {
    function redirect_to(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}

if (!function_exists('ensure_csrf_token')) {
    function ensure_csrf_token(): string
    {
        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_input')) {
    function csrf_input(): string
    {
        $token = htmlspecialchars(ensure_csrf_token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $token . '">';
    }
}

if (!function_exists('validate_csrf_token')) {
    function validate_csrf_token(): bool
    {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        $submittedToken = $_POST['_csrf'] ?? '';

        return is_string($sessionToken)
            && $sessionToken !== ''
            && is_string($submittedToken)
            && hash_equals($sessionToken, $submittedToken);
    }
}

if (!function_exists('require_post_request')) {
    function require_post_request(string $redirectPath): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            redirect_to($redirectPath);
        }
    }
}

if (!function_exists('require_valid_csrf')) {
    function require_valid_csrf(string $redirectPath, string $message = 'Your session expired. Please try again.'): void
    {
        require_post_request($redirectPath);

        if (!validate_csrf_token()) {
            set_flash_message('error', $message);
            redirect_to($redirectPath);
        }
    }
}

if (!function_exists('require_admin_session')) {
    function require_admin_session(string $redirectPath, ?string $message = null): void
    {
        if (!empty($_SESSION['admin'])) {
            return;
        }

        if ($message !== null && $message !== '') {
            set_flash_message('error', $message);
        }

        redirect_to($redirectPath);
    }
}

if (!function_exists('require_student_session')) {
    function require_student_session(string $redirectPath, ?string $message = null): void
    {
        if (!empty($_SESSION['student_reg_no'])) {
            return;
        }

        if ($message !== null && $message !== '') {
            set_flash_message('error', $message);
        }

        redirect_to($redirectPath);
    }
}

if (!function_exists('request_is_local')) {
    function request_is_local(): bool
    {
        if (PHP_SAPI === 'cli') {
            return true;
        }

        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
        return in_array($remoteAddr, ['127.0.0.1', '::1'], true);
    }
}

if (!function_exists('require_local_or_admin_access')) {
    function require_local_or_admin_access(string $redirectPath, string $message): void
    {
        if (request_is_local() || !empty($_SESSION['admin'])) {
            return;
        }

        set_flash_message('error', $message);
        redirect_to($redirectPath);
    }
}

if (!function_exists('harden_session_after_login')) {
    function harden_session_after_login(): void
    {
        session_regenerate_id(true);
        ensure_csrf_token();
    }
}

<?php

namespace Roots\BladeCheck;

use Illuminate\Support\Str;

if (class_exists('BladeCheck')) {
    return;
}

/**
 * Blade Check
 */
class BladeCheck
{
    /**
     * Status
     *
     * @var mixed
     */
    protected $status;

    /**
     * Default Config
     *
     * @var array
     */
    protected $config = [
        'hide'       => false,
        'duration'   => 60 * 60 * 24,
        'extensions' => ['blade.php']
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->check();
    }

    /**
     * Config
     *
     * @return object
     */
    protected function config()
    {
        return (object) collect($this->config)
            ->merge(apply_filters('roots.blade.check', []))
            ->all();
    }

    /**
     * Check for accessible template files.
     *
     * @return void
     */
    protected function check()
    {
        /**
         * Show admin notice if template files are publicly accessible.
         */
        add_action('admin_init', function () {
            if (! current_user_can('manage_options') || $this->isDev() || $this->config()->hide) {
                return;
            }

            $this->status = collect();

            if ($this->cache()->isEmpty()) {
                collect($this->config()->extensions)
                    ->map(function ($extension) {
                        $response = wp_remote_get(home_url('/ping'.Str::start($extension, '.')));
                        $status   = wp_remote_retrieve_response_code($response);

                        if ($status !== 403 && $status !== 301) {
                            $this->status->put(Str::start($extension, '.'), $status);
                        }
                    });

                set_site_transient('roots_blade_response', $this->status->all() ?: false, $this->config()->duration);
            }

            if ($this->cache()->isNotEmpty()) {
                $this->notice(
                    'Your <code>'.$this->cache()->keys()->implode(', ').'</code> files are publicly accessible. Please visit the Sage <a href="https://roots.io/sage/docs/theme-installation/">docs</a> to learn more. <a title="Recheck Blade Files" class="dashicons dashicons-update alignright" href="' . wp_nonce_url(admin_url(add_query_arg('check-blade', 'true', 'index.php')), 'check-blade') . '"></a>',
                    'warning'
                );
            }

            if (! empty($_GET['check-blade']) && $_GET['check-blade'] === 'true' && wp_verify_nonce($_GET['_wpnonce'], 'check-blade')) {
                if (delete_site_transient('roots_blade_response')) {
                    $this->notice(
                        'Successfully rechecked template files.',
                        'success',
                        true
                    );
                }
            }
        });
    }

    /**
     * Return cache transient inside of a collection.
     *
     * @return \Illuminate\Support\Collection;
     */
    protected function cache()
    {
        return collect(
            get_site_transient('roots_blade_response') ?: []
        );
    }

    /**
     * Simple helper function for admin notices.
     *
     * @param  string  $message
     * @param  string  $type
     * @param  boolean $dismissible
     * @return void
     */
    protected function notice($message, $type = 'info', $dismissible = false)
    {
        add_action('admin_notices', function () use ($message, $type, $dismissible) {
            printf(
                '<div class="%1$s"><p>%2$s</p></div>',
                esc_attr("notice notice-{$type}" . ($dismissible ? ' is-dismissible' : '')),
                __($message, 'roots')
            );
        });
    }

    /**
     * Check if current environment is set to development.
     *
     * @return boolean
     */
    protected function isDev()
    {
        if (getenv('WP_ENV') === 'development' || pathinfo(home_url(), PATHINFO_EXTENSION) === 'test') {
            return true;
        }

        return false;
    }
}

if (function_exists('add_action')) {
    return new BladeCheck;
}

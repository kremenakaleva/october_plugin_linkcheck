<?php namespace Pensoft\LinkCheck;

use Pensoft\LinkCheck\Models\BrokenLink;
use System\Classes\PluginBase;
use Pensoft\LinkCheck\Models\Settings;
use Backend;
use Lang;

/**
 * LinkCheck Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'pensoft.linkcheck::lang.details.name',
            'description' => 'pensoft.linkcheck::lang.details.description',
            'icon'        => 'icon-chain-broken',
        ];
    }

    public function registerPermissions()
    {
        return [
            'pensoft.linkcheck.manage' => [
                'tab'   => 'pensoft.linkcheck::lang.plugin.tab',
                'label' => 'pensoft.linkcheck::lang.plugin.manage',
            ],
            'pensoft.linkcheck.view' => [
                'tab'   => 'pensoft.linkcheck::lang.plugin.tab',
                'label' => 'pensoft.linkcheck::lang.plugin.view',
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'pensoft.linkcheck::lang.menu.settings.label',
                'description' => 'pensoft.linkcheck::lang.menu.settings.description',
                'category'    => 'pensoft.linkcheck::lang.plugin.category',
                'icon'        => 'icon-chain-broken',
                'class'       => 'Pensoft\LinkCheck\Models\Settings',
                'order'       => 410,
                'permissions' => ['pensoft.linkcheck.manage']
            ],
            'brokenlinks' => [
                'label'       => 'pensoft.linkcheck::lang.menu.brokenlinks.label',
                'description' => 'pensoft.linkcheck::lang.menu.brokenlinks.description',
                'category'    => 'pensoft.linkcheck::lang.plugin.category',
                'icon'        => 'icon-list',
                'url'         => Backend::url('bombozama/linkcheck/brokenlinks'),
                'order'       => 411,
                'permissions' => ['pensoft.linkcheck.view']
            ],
        ];
    }

    public function registerListColumnTypes()
    {
        return [
            'httpstatus' => [$this, 'httpStatus'],
        ];
    }

    public function httpStatus($value, $column, $record)
    {
        return '<span title="' . Lang::get('pensoft.linkcheck::lang.codes.' . $value ) . '">' . $value . '</span>';
    }

    // Please do https://octobercms.com/docs/setup/installation#crontab-setup
    public function registerSchedule($schedule)
    {
        $settings = Settings::instance();
        $schedule->call(function(){
            BrokenLink::processLinks();
        })->cron($settings->time);
    }
}

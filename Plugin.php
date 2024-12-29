<?php

declare (strict_types = 1);

namespace Hts\Mall;

use Hts\Mall\Classes\Registration\BootComponents;
use Hts\Mall\Classes\Registration\BootEvents;
use Hts\Mall\Classes\Registration\BootExtensions;
use Hts\Mall\Classes\Registration\BootMails;
use Hts\Mall\Classes\Registration\BootServiceContainer;
use Hts\Mall\Classes\Registration\BootSettings;
use Hts\Mall\Classes\Registration\BootTwig;
use Hts\Mall\Classes\Registration\BootValidation;
use Hts\Mall\Console\CheckCommand;
use Hts\Mall\Console\IndexCommand;
use Hts\Mall\Console\PurgeCommand;
use Hts\Mall\Console\SeedDataCommand;
use Hts\Mall\Models\CustomField;
use Hts\Mall\Models\CustomFieldOption;
use Hts\Mall\Models\Discount;
use Hts\Mall\Models\ImageSet;
use Hts\Mall\Models\PaymentMethod;
use Hts\Mall\Models\Product;
use Hts\Mall\Models\ServiceOption;
use Hts\Mall\Models\ShippingMethod;
use Hts\Mall\Models\ShippingMethodRate;
use Hts\Mall\Models\Variant;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\View;
use October\Rain\Database\Relations\Relation;
use System;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    use BootEvents;
    use BootExtensions;
    use BootServiceContainer;
    use BootSettings;
    use BootComponents;
    use BootMails;
    use BootValidation;
    use BootTwig;

    /**
     * Required plugin dependencies.
     * @var array
     */
    public $require = [
        'RainLab.User',
        'Hts.Location',
        'RainLab.Translate',
    ];

    /**
     * Required model morph-map relations, must be registered n the constructor
     * to make them available when the plugin migrations are run.
     * @var array
     */
    protected $relations = [
        Variant::MORPH_KEY            => Variant::class,
        Product::MORPH_KEY            => Product::class,
        ImageSet::MORPH_KEY           => ImageSet::class,
        Discount::MORPH_KEY           => Discount::class,
        CustomField::MORPH_KEY        => CustomField::class,
        PaymentMethod::MORPH_KEY      => PaymentMethod::class,
        ShippingMethod::MORPH_KEY     => ShippingMethod::class,
        CustomFieldOption::MORPH_KEY  => CustomFieldOption::class,
        ShippingMethodRate::MORPH_KEY => ShippingMethodRate::class,
        ServiceOption::MORPH_KEY      => ServiceOption::class,
    ];

    /**
     * Create a new plugin instance.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        Relation::morphMap($this->relations);
    }

    /**
     * Register this plugin.
     * @return void
     */
    public function register()
    {
        $this->registerServices();
        $this->registerTwigEnvironment();
    }

    /**
     * Boot this plugin.
     * @return void
     */
    public function boot()
    {
        $this->registerExtensions();
        $this->registerEvents();
        $this->registerValidationRules();

        $this->registerConsoleCommand('hts.mall.check', CheckCommand::class);
        $this->registerConsoleCommand('hts.mall.index', IndexCommand::class);
        $this->registerConsoleCommand('hts.mall.purge', PurgeCommand::class);
        $this->registerConsoleCommand('hts.mall.seed', SeedDataCommand::class);

        View::share('app_url', config('app.url'));
    }

    /**
     * Register Backend-Navigation items for this plugin.
     * @return array
     */
    public function registerNavigation()
    {
        $navigation = parent::registerNavigation();

        // Icon name has been changed from 'icon-star-half-full' to 'icon-star-half'
        if (version_compare(System::VERSION, '3.6', '>=')) {
            $navigation['mall-catalogue']['sideMenu']['mall-reviews']['icon'] = 'icon-star-half';
        }

        return $navigation;
    }
}

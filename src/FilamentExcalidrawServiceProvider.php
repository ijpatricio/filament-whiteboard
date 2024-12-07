<?php

namespace Ijpatricio\FilamentExcalidraw;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Ijpatricio\FilamentExcalidraw\Livewire\ExcalidrawEditor;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ijpatricio\FilamentExcalidraw\Commands\FilamentExcalidrawCommand;
use Ijpatricio\FilamentExcalidraw\Testing\TestsFilamentExcalidraw;

class FilamentExcalidrawServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-excalidraw';

    public static string $viewNamespace = 'filament-excalidraw';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('ijpatricio/filament-excalidraw');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-excalidraw/{$file->getFilename()}"),
                ], 'filament-excalidraw-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentExcalidraw);

        Livewire::component('excalidraw-editor', ExcalidrawEditor::class);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'ijpatricio/filament-excalidraw';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-excalidraw', __DIR__ . '/../resources/dist/components/filament-excalidraw.js'),
            // Css::make('filament-excalidraw-styles', __DIR__ . '/../resources/dist/filament-excalidraw.css'),
            // Js::make('filament-excalidraw-scripts', __DIR__ . '/../resources/dist/filament-excalidraw.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentExcalidrawCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_laravel_whiteboards_table',
        ];
    }
}

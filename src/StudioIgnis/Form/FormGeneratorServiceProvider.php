<?php namespace StudioIgnis\Form;

use Illuminate\Support\ServiceProvider;
use StudioIgnis\Form\Exception\ValidationError;

class FormGeneratorServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('studioignis/form-generator');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('form.validator', function($app)
        {
            return $app->make('StudioIgnis\Form\Validator');
        });

        $this->app->bind('form.element', function($app)
        {
            return $app->make('StudioIgnis\Form\Element');
        });

        $this->app->bind('form.generator', function($app)
        {
            return new Generator(
                $app['form.validator'],
                $app['form.element'],
                $app['form'],
                $app['request']
            );
        });

        // Go back on form validation error
        $this->app->error(function(ValidationError $e)
        {
            return \Redirect::back()->withInput()->withErrors($e->getErrors());
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('form.validator', 'form.element', 'form.generator');
    }
}

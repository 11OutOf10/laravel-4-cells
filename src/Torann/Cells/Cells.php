<?php namespace Torann\Cells;

use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use ReflectionClass;
use Illuminate\View\Environment;

class Cells {

	/**
	 * Environment view.
	 *
	 * @var Boolean
	 */
	protected $caching_disabled = false;

	/**
     * @var \Illuminate\Foundation\Application
     */
	protected $app;

	/**
	 * Create a new instance.
	 *
	 * @param  \Illuminate\View\Environment      $view
	 * @return void
	 */
	public function __construct($caching_disabled, Application $app)
	{
		$this->caching_disabled = $caching_disabled;
		$this->app 	            = $app;
	}

	/**
	 * Cell instance.
	 *
	 * @param  string $className
	 * @param  string $template
	 * @param  array  $attributes
	 * @return Torann\Cells
	 */
	public function get($className, $template = 'display', $attributes = array() , $action = 'display' )
	{
		static $cells = array();

		// If the class name is not lead with upper case add prefix "Cell".
		if ( ! preg_match('|^[A-Z]|', $className))
		{
			$className = 'Cell'.Str::studly($className);
		}

		if ( ! $instance = array_get($cells, $className))
		{
			$reflector = new ReflectionClass($className);

			if ( ! $reflector->isInstantiable())
			{
				throw new UnknownCellClassException("Cell target [$className] is not instantiable.");
			}

			$instance = $this->app->make($className);
			$instance->setDisableCache($this->caching_disabled);

			array_set($cells, $className, $instance);
		}

		$instance->setAttributes($attributes);

		$instance->initCell( $template , $action );

		return $instance->displayView();
	}

}

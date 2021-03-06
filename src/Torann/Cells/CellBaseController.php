<?php namespace Torann\Cells;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Config\Repository;

abstract class CellBaseController {

    /**
     * Environment view.
     *
     * @var \Illuminate\View\Environment
     */
    protected $view;

    /**
     * View template.
     *
     * @var string
     */
    protected $template;

    /**
     * View action.
     *
     * @var string
     */
    protected $action = 'display';

    /**
     * Default attributes.
     *
     * @var array
     */
    public $attributes = array();

    /**
     * Default cache value.
     *
     * @var string
     */
    public $cache = 0;

    public function setDisableCache($disable = true) {
        if($disable)
            $this->cache = 0;
    }

    /**
     * Abstract class init for a cell factory.
     *
     * @return void
     */
    abstract public function init();

    /**
     * Set attributes to object var.
     *
     * @param  arary  $attributes
     * @return void
     */
    public function setAttributes($attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    /**
     * Set attribute.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get attribute with a key.
     *
     * @param  string  $key
     * @param  string  $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return array_get($this->attributes, $key, $default);
    }

    /**
     * Start cell factory.
     *
     * @return void
     */
    public function initCell( $template = 'display' , $action = 'display' )
    {
        $this->template = $template;

        $this->init();

        $data = (array) $this->$action();
        $this->data = array_merge($this->attributes, $data);
    }

    /**
     * Display cell HTML.
     *
     * @return string
     */
    public function displayView()
    {
        $path = "$this->name.$this->template";

        // Are we caching this?
        if($this->cache)
        {
            return Cache::remember("Cells.$path", $this->cache, function() use ($path) {
                return $this->renderView( $path );
            });
        }

        return $this->renderView( $path );
    }

    /**
     * Render cell to HTML.
     *
     * @return string
     */
    public function renderView( $path )
    {
        return View::make('cells' . DIRECTORY_SEPARATOR . $path,$this->data);
    }

}

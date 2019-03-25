<?php

namespace App\Http;

use GuzzleHttp\Psr7\Request as PsrRequest;
/**
 * Class Request
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Request extends PsrRequest
{
    /**
     * Route settings
     * [method => ..., controller => ..., action => ..., attributes => ...]
     *
     * @var array
     */
    protected $routeSettings;

    /**
     * Set route settings of request
     *
     * @param array $settings
     *
     * @return Request
     */
    public function setRouteSettings(array $settings): self
    {
        $this->routeSettings = $settings;

        return $this;
    }

    /**
     * Returns request's route settings
     *
     * @param string|null $name Setting name
     *
     * @return mixed
     */
    public function getRouteSettings(string $name = null)
    {
        if ($name) {
            return $this->routeSettings[$name] ?? null;
        }

        return $this->routeSettings;
    }
}

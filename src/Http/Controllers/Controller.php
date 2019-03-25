<?php

namespace App\Http\Controllers;

use App\Http\Request;

/**
 * Class Controller
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Controller
{
    /**
     * PSR-7 request
     *
     * @var Request
     */
    protected $request;

    /**
     * Set request instance
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}

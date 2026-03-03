<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Startup de Bolso API",
 *      description="API documentation for Startup de Bolso Gestão Operacional",
 *      @OA\Contact(
 *          email="suporte@startupdebolso.com"
 *      )
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 */
abstract class Controller
{
    //
}

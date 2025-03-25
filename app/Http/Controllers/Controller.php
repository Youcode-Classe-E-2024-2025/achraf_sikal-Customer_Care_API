<?php

namespace App\Http\Controllers;
/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="CustomerCareAPI",
 *         version="1.0.0",
 *         description="API for renting and managing cars"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="BearerAuth",
 *             type="http",
 *             scheme="bearer"
 *         )
 *     )
 * )
 */

abstract class Controller
{
    //
}

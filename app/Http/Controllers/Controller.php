<?php

namespace App\Http\Controllers;

    /**
 * @OA\Info(
 *    title="Swagger with Laravel",
 *    version="1.0.0",
 * )
 * @OA\Server(
 *   url=L5_SWAGGER_CONST_HOST,
 *  description="Demo API Server"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Tag(
 *    name="Users",
 *   description="Everything about users",
 * )
 * @OA\Tag(
 *   name="Products",
 *  description="Everything about products",
 * )

 */

abstract class Controller
{

}

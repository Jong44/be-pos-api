<?php

/**
 * @OA\Schema(
 *     schema="Cart",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=10),
 *     @OA\Property(property="items", type="array", @OA\Items(
 *         type="object",
 *         @OA\Property(property="product_id", type="integer", example=5),
 *         @OA\Property(property="quantity", type="integer", example=2),
 *         @OA\Property(property="price", type="number", format="float", example=120000)
 *     )),
 *     @OA\Property(property="total", type="number", format="float", example=240000)
 * )
 */



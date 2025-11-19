<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *          version="1.0.0",
 *          title="MiniStrava API",
 *          description="REST API for MiniStrava mobile and admin panel clients."
 *     ),
 *     @OA\Server(
 *          url=L5_SWAGGER_CONST_HOST,
 *          description="Primary API server"
 *     )
 * )
 */
class AuthDocumentation
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation"},
     *             @OA\Property(property="first_name", type="string", example="Jan"),
     *             @OA\Property(property="last_name", type="string", example="Kowalski"),
     *             @OA\Property(property="email", type="string", format="email", example="jan@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Pass123!"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="Pass123!"),
     *             @OA\Property(property="locale", type="string", example="pl"),
     *             @OA\Property(property="timezone", type="string", example="Europe/Warsaw")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *         )
     *     )
     * )
     */
    public function register()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Log in existing user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *         )
     *     )
     * )
     */
    public function login()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Invalidate user token",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=204, description="Logged out")
     * )
     */
    public function logout()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     summary="Send reset password link",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Reset link sent")
     * )
     */
    public function forgotPassword()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/auth/reset-password",
     *     summary="Reset password with token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password","password_confirmation","token"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password changed")
     * )
     */
    public function resetPassword()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get authenticated profile",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile data",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )
     */
    public function profileShow()
    {
    }

    /**
     * @OA\Put(
     *     path="/api/profile",
     *     summary="Update authenticated profile",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="birth_date", type="string", format="date"),
     *             @OA\Property(property="gender", type="string", enum={"male","female","other"}),
     *             @OA\Property(property="height_cm", type="integer"),
     *             @OA\Property(property="weight_kg", type="integer"),
     *             @OA\Property(property="avatar_url", type="string"),
     *             @OA\Property(property="bio", type="string"),
     *             @OA\Property(property="locale", type="string", example="pl"),
     *             @OA\Property(property="timezone", type="string", example="Europe/Warsaw")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated profile",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )
     */
    public function profileUpdate()
    {
    }
}

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="birth_date", type="string", format="date", nullable=true),
 *     @OA\Property(property="gender", type="string", nullable=true),
 *     @OA\Property(property="height_cm", type="integer", nullable=true),
 *     @OA\Property(property="weight_kg", type="integer", nullable=true),
 *     @OA\Property(property="avatar_url", type="string", nullable=true),
 *     @OA\Property(property="bio", type="string", nullable=true),
 *     @OA\Property(property="locale", type="string"),
 *     @OA\Property(property="timezone", type="string"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class UserSchema
{
}

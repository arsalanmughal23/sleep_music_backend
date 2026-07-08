<?php

namespace App\Models;

/**
 * Class Register
 * @package App\Models
 *
 * @SWG\Definition(
 *      definition="Register",
 *      required={"first_name", "last_name", "email", "phone", "address", "image", "password", "password_confirmation", "device_token", "device_type"},
 *      @SWG\Property(
 *          property="name",
 *          description="User First Name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="phone",
 *          description="User Phone Name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="image",
 *          description="User Image {upload file}",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          description="User Email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="password",
 *          description="Password",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="password_confirmation",
 *          description="Password Confirmation",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="device_token",
 *          description="Device Token",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="device_type",
 *          description="User Device Type:ios,android,web",
 *          type="string"
 *      )
 * )
 */
class Register
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name'                  => 'required',
        'image'                 => 'sometimes', // |mimes:jpg,png
        'email'                 => 'required|email|unique:users,email',
        'device_token'          => 'sometimes|required',
        'device_type'           => 'required|string|in:ios,android,web',
        'password'              => 'required|min:6|required_with:password_confirmation|same:password_confirmation',
//        'password'              => 'required|min:6|confirmed',
        'password_confirmation' => 'required|min:6',
        'gender'                => 'required',
        'dob'                   => 'required',
        'username'              => 'required|unique:user_details,username'
    ];
}
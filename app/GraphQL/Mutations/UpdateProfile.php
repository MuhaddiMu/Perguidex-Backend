<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Hash;

class UpdateProfile
{
    /**
     * Return a value for the field.
     *
     * @param  @param  null  $root Always null, since this field has no parent.
     * @param  array<string, mixed>  $args The field arguments passed by the client.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Shared between all fields.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Metadata for advanced query resolution.
     * @return mixed
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {

        $UserID   = $context->user()->id;

        $Input = $args["input"];

        if (isset($Input["name"]) && isset($Input["timezone"])) {
            // Update Name and Timestamp

            if (User::where('id', $UserID)->update(['timezone' => $Input["timezone"], 'name' => $Input["name"]])) {

                return ["status" => "success"];
            } else {
                return ["status" => "false"];
            }
        } else if (isset($Input["email"]) && isset($Input["currentPassword"])) {
            // Update Email Address

            $User = User::where('id', $UserID)->first();

            if (Hash::check($Input["currentPassword"], $User->password)) {

                $User->email = $Input["email"];
                $User->save();

                return ["status" => "success"];
            } else {
                return ["status" => "error"];
            }
        } else if (isset($Input["currentPassword"]) && isset($Input["newPassword"])) {
            // Update Current Password

            $User = User::where('id', $UserID)->first();

            if (Hash::check($Input["currentPassword"], $User->password)) {

                $User->password = Hash::make($Input["newPassword"]);
                $User->save();

                return ["status" => "success"];
            } else {
                return ["status" => "error"];
            }
        } else {
            return ["status" => "error"];
        }
    }
}

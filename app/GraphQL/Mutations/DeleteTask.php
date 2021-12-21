<?php

namespace App\GraphQL\Mutations;

use App\Models\Task;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteTask
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


        $UserID = $context->user()->id;
        $TaskID = $args['id'];

        if (Task::where('id', $TaskID)->where('user_id', $UserID)->update(['deleted' => True])) {
            return ["status" => "success"];
        } else {
            return ["status" => "error"];
        }
    }
}

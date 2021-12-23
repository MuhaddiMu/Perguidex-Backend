<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use \Carbon\Carbon;
use App\Models\Task;

class CreateTask
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
        $Timezone = $context->user()->timezone;

        $Task               = new Task;
        $Task->user_id      = $UserID;
        $Task->task         = $args["input"]["task"];
        $Task->onDate       = Carbon::now($Timezone);
        $Task->status       = false;
        $Task->created_at   = Carbon::now($Timezone);
        $Task->save();

        return $Task;
    }
}

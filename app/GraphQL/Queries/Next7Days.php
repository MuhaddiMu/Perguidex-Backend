<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Task;
use Carbon\Carbon;

class Next7Days
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

        $Tasks    = Task::where('user_id', $UserID)->where('status', False)->whereBetween('onDate', [Carbon::now($Timezone), Carbon::now($Timezone)->addDays(7)])->where('deleted', False)->get();

        return $Tasks;
    }
}

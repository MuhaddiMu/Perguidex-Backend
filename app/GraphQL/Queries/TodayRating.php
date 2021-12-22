<?php

namespace App\GraphQL\Queries;

use App\Models\DayReview;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use \Carbon\Carbon;

class TodayRating
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

        $Rating =  DayReview::where('user_id', $UserID)->whereDate('created_at', Carbon::now())->where('deleted', False)->first();

        return $Rating;
    }
}

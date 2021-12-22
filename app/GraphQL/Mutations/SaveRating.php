<?php

namespace App\GraphQL\Mutations;

use App\Models\DayReview;
use App\Exceptions\CustomException;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SaveRating
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

        $Review = DayReview::whereDate('created_at', Carbon::now())->first();

        if ($Review === null) {
            $DayReview              = new DayReview;
            $DayReview->user_id     = $UserID;
            $DayReview->rate        = $args["input"]["rate"];
            $DayReview->reason      = $args["input"]["reason"];
            $DayReview->save();

            return $DayReview;
        } else {
            throw new CustomException(
                'Day reviewed already',
                'User already reviewed a day for today'
            );
        }
    }
}

<?php

namespace App\GraphQL\Queries;

use App\Models\Quotation as QuotationModel;

class Quotation
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver

        $dailyQuotation = QuotationModel::where('show', True)->first();

        return [
            'author' => $dailyQuotation["author"],
            'quote' => $dailyQuotation["quote"]
        ];
    }
}
